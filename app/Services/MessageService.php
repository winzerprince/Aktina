<?php

namespace App\Services;

use App\Interfaces\Repositories\MessageRepositoryInterface;
use App\Interfaces\Services\ConversationServiceInterface;
use App\Interfaces\Services\MessageServiceInterface;
use App\Models\User;
use App\Models\MessageFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageService implements MessageServiceInterface
{
    protected $messageRepository;
    protected $conversationService;

    public function __construct(
        MessageRepositoryInterface $messageRepository,
        ConversationServiceInterface $conversationService
    ) {
        $this->messageRepository = $messageRepository;
        $this->conversationService = $conversationService;
    }

    public function sendMessage(User $sender, User $recipient, string $content, ?UploadedFile $file = null)
    {
        // Create or find conversation
        $conversation = $this->conversationService->createOrFindConversation($sender, $recipient);

        // Determine message type based on file
        $messageType = 'text';
        if ($file) {
            $mimeType = $file->getMimeType();
            if (strpos($mimeType, 'image/') === 0) {
                $messageType = 'image';
            } else {
                $messageType = 'file';
            }
        }

        // Create message
        $message = $this->messageRepository->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'content' => $content,
            'message_type' => $messageType,
            'is_read' => false,
        ]);

        // Handle file upload if present
        if ($file) {
            $this->uploadFile($file, $message->id);
        }

        // Update conversation last message timestamp
        $this->conversationService->updateLastMessage($conversation->id);

        return $message;
    }

    public function getConversation(User $user1, User $user2)
    {
        return $this->conversationService->createOrFindConversation($user1, $user2);
    }

    public function getConversationMessages(int $conversationId, int $page = 1, int $perPage = 50)
    {
        return $this->messageRepository->getByConversation($conversationId, $page, $perPage);
    }

    public function getUserConversations(User $user)
    {
        return $this->conversationService->getUserConversations($user);
    }

    public function markAsRead(int $conversationId, User $user)
    {
        $this->messageRepository->markAsRead($conversationId, $user);
    }

    /**
     * Mark messages as read by conversation ID and user ID
     */
    public function markMessagesAsRead(int $conversationId, int $userId)
    {
        $user = User::find($userId);
        if ($user) {
            $this->messageRepository->markAsRead($conversationId, $user);
        }
    }

    public function uploadFile(UploadedFile $file, int $messageId): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;

        // Store file in messages directory
        $filePath = $file->storeAs('messages', $fileName, 'public');

        // Determine file type
        $mimeType = $file->getMimeType();
        $fileType = 'other';
        if (strpos($mimeType, 'image/') === 0) {
            $fileType = 'image';
        } elseif (
            strpos($mimeType, 'application/pdf') === 0 ||
            strpos($mimeType, 'application/msword') === 0 ||
            strpos($mimeType, 'application/vnd.openxmlformats-officedocument') === 0 ||
            strpos($mimeType, 'application/vnd.ms-') === 0 ||
            strpos($mimeType, 'text/') === 0
        ) {
            $fileType = 'document';
        }

        // Create file record
        $message = $this->messageRepository->findById($messageId);
        if ($message) {
            $message->files()->create([
                'message_id' => $messageId,
                'original_name' => $originalName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
            ]);
        }

        return $filePath;
    }

    public function deleteMessage(int $messageId, User $user): bool
    {
        $message = $this->messageRepository->findById($messageId);

        if (!$message || $message->sender_id !== $user->id) {
            return false;
        }

        // Delete file if exists
        if ($message->file_path) {
            Storage::disk('public')->delete($message->file_path);
        }

        return $this->messageRepository->delete($messageId);
    }

    public function getUnreadCount(User $user): int
    {
        return $this->messageRepository->getUnreadCountForUser($user);
    }

    /**
     * Create a new message
     */
    public function createMessage(array $data)
    {
        $message = $this->messageRepository->create($data);

        // Update conversation last message timestamp
        if (isset($data['conversation_id'])) {
            $this->conversationService->updateLastMessage($data['conversation_id']);
        }

        return $message;
    }

    /**
     * Add a file to a message
     */
    public function addMessageFile(int $messageId, UploadedFile $file)
    {
        return $this->uploadFile($file, $messageId);
    }

    /**
     * Download a message file attachment
     */
    public function downloadMessageFile(int $fileId): ?StreamedResponse
    {
        $file = MessageFile::find($fileId);

        if (!$file) {
            return null;
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return null;
        }

        return Storage::disk('public')->download(
            $file->file_path,
            $file->original_name
        );
    }
}
