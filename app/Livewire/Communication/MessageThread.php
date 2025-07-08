<?php

namespace App\Livewire\Communication;

use App\Interfaces\Services\MessageServiceInterface;
use App\Interfaces\Services\ConversationServiceInterface;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class MessageThread extends Component
{
    use WithFileUploads;

    public $conversationId = null;
    public $conversation = null;
    public $messages = [];
    public $newMessage = '';
    public $attachments = [];
    public $isLoading = false;

    public function mount($conversationId = null)
    {
        if ($conversationId) {
            $this->loadConversation($conversationId);
        }
    }

    #[On('conversation-selected')]
    public function loadConversation($conversationId)
    {
        // Prevent reloading the same conversation
        if ($this->conversationId == $conversationId && !empty($this->messages)) {
            return;
        }

        $this->conversationId = $conversationId;
        $this->isLoading = true;

        try {
            $conversationService = app(ConversationServiceInterface::class);
            $messageService = app(MessageServiceInterface::class);

            $this->conversation = $conversationService->getConversation($conversationId);

            if (!$this->conversation) {
                $this->conversation = null;
                $this->messages = [];
                return;
            }

            // Check if user has permission to view this conversation
            if (!$this->canAccessConversation()) {
                session()->flash('error', 'You do not have permission to view this conversation.');
                return;
            }

            $paginatedMessages = $messageService->getConversationMessages($conversationId);

            // Extract messages from paginated result and transform to array format
            $this->messages = $this->transformMessagesToArray($paginatedMessages);

            // Mark messages as read
            $messageService->markMessagesAsRead($conversationId, auth()->id());

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load conversation: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
            'attachments.*' => 'file|max:10240' // 10MB max per file
        ]);

        if (!$this->conversationId) {
            session()->flash('error', 'No conversation selected.');
            return;
        }

        try {
            $messageService = app(MessageServiceInterface::class);

            $messageData = [
                'conversation_id' => $this->conversationId,
                'sender_id' => auth()->id(),
                'content' => $this->newMessage
            ];

            $message = $messageService->createMessage($messageData);

            // Handle file attachments
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $messageService->addMessageFile($message->id, $attachment);
                }
            }

            // Clear form
            $this->newMessage = '';
            $this->attachments = [];

            // Reload messages to include the new message
            $paginatedMessages = $messageService->getConversationMessages($this->conversationId);
            $this->messages = $this->transformMessagesToArray($paginatedMessages);

            // Notify other components about the change (but don't trigger self update)
            $this->dispatch('message-sent', conversationId: $this->conversationId);
            $this->dispatch('conversation-updated');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    public function deleteMessage($messageId)
    {
        try {
            $messageService = app(MessageServiceInterface::class);
            $message = Message::find($messageId);

            if (!$message || $message->sender_id !== auth()->id()) {
                session()->flash('error', 'You can only delete your own messages.');
                return;
            }

            $messageService->deleteMessage($messageId);
            $this->loadConversation($this->conversationId);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete message: ' . $e->getMessage());
        }
    }

    public function downloadAttachment($fileId)
    {
        try {
            $messageService = app(MessageServiceInterface::class);
            return $messageService->downloadMessageFile($fileId);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to download file: ' . $e->getMessage());
        }
    }

    private function canAccessConversation(): bool
    {
        if (!$this->conversation) {
            return false;
        }

        $userId = auth()->id();
        return $this->conversation->user_one_id === $userId || $this->conversation->user_two_id === $userId;
    }

    public function getOtherParticipant()
    {
        if (!$this->conversation) {
            return null;
        }

        $userId = auth()->id();

        return $this->conversation->user_one_id === $userId
            ? $this->conversation->userTwo
            : $this->conversation->userOne;
    }

    /**
     * Less intensive method to check for new messages without full reload
     */
    public function refreshMessages()
    {
        if (!$this->conversationId) {
            return;
        }

        try {
            $messageService = app(MessageServiceInterface::class);

            // Get the latest messages
            $paginatedMessages = $messageService->getConversationMessages($this->conversationId);
            $latestMessages = $this->transformMessagesToArray($paginatedMessages);

            // Only update if there are new messages
            if (count($latestMessages) > count($this->messages)) {
                $this->messages = $latestMessages;

                // Mark messages as read
                $messageService->markMessagesAsRead($this->conversationId, auth()->id());

                // Dispatch event for scrolling to bottom
                $this->dispatch('messageReceived');
            }

        } catch (\Exception $e) {
            // Silent fail on polling - don't disrupt UI
        }
    }

    /**
     * Update the message input value
     * This helps with text visibility issues in different modes
     */
    public function updatedNewMessage($value)
    {
        $this->newMessage = $value;
    }

    /**
     * Transform paginated messages to array format for the view
     */
    private function transformMessagesToArray($paginatedMessages)
    {
        return $paginatedMessages->items() ? collect($paginatedMessages->items())->map(function ($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at,
                'files' => $message->files ? $message->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'original_name' => $file->original_name,
                        'file_path' => $file->file_path,
                    ];
                })->toArray() : []
            ];
        })->toArray() : [];
    }

    public function render()
    {
        return view('livewire.communication.message-thread', [
            'otherParticipant' => $this->getOtherParticipant()
        ]);
    }
}
