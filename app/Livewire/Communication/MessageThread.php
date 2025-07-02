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
            
            $this->messages = $messageService->getConversationMessages($conversationId)->toArray();
            
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
            
            // Reload messages
            $this->loadConversation($this->conversationId);
            
            // Notify other components
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
        return $this->conversation->user1_id === $userId || $this->conversation->user2_id === $userId;
    }

    public function getOtherParticipant()
    {
        if (!$this->conversation) {
            return null;
        }
        
        $userId = auth()->id();
        $otherId = $this->conversation->user1_id === $userId 
            ? $this->conversation->user2_id 
            : $this->conversation->user1_id;
            
        return $this->conversation->user1_id === $userId 
            ? $this->conversation->user2 
            : $this->conversation->user1;
    }

    public function render()
    {
        return view('livewire.communication.message-thread', [
            'otherParticipant' => $this->getOtherParticipant()
        ]);
    }
}
