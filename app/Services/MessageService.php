<?php

namespace App\Services;

use App\Interfaces\Repositories\MessageRepositoryInterface;
use App\Interfaces\Services\ConversationServiceInterface;
use App\Interfaces\Services\MessageServiceInterface;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        
        // Determine message type
        $messageType = $file ? 'file' : 'text';
        
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
            $filePath = $this->uploadFile($file, $message->id);
            $message->update(['file_path' => $filePath]);
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
    
    public function uploadFile(UploadedFile $file, int $messageId): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        
        // Store file in messages directory
        $filePath = $file->storeAs('messages', $fileName, 'public');
        
        // Create file record
        $message = $this->messageRepository->findById($messageId);
        if ($message) {
            $message->files()->create([
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_type' => $file->getMimeType(),
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
}
