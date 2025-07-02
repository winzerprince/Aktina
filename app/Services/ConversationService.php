<?php

namespace App\Services;

use App\Interfaces\Repositories\ConversationRepositoryInterface;
use App\Interfaces\Services\ConversationServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ConversationService implements ConversationServiceInterface
{
    protected $conversationRepository;

    public function __construct(ConversationRepositoryInterface $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function createOrFindConversation(User $user1, User $user2)
    {
        // Check if conversation already exists
        $conversation = $this->conversationRepository->findByUsers($user1, $user2);
        
        if (!$conversation) {
            $conversation = $this->conversationRepository->create([
                'user1_id' => $user1->id,
                'user2_id' => $user2->id,
                'last_message_at' => now(),
            ]);
        }
        
        return $conversation;
    }
    
    public function getConversation(int $conversationId)
    {
        return $this->conversationRepository->findById($conversationId);
    }
    
    public function getUserConversations(User $user, int $page = 1, int $perPage = 20)
    {
        return $this->conversationRepository->getUserConversations($user, $page, $perPage);
    }
    
    public function updateLastMessage(int $conversationId)
    {
        $this->conversationRepository->updateLastMessage($conversationId);
    }
    
    public function canUserAccessConversation(User $user, int $conversationId): bool
    {
        $conversation = $this->conversationRepository->findById($conversationId);
        
        if (!$conversation) {
            return false;
        }
        
        return $conversation->user1_id === $user->id || $conversation->user2_id === $user->id;
    }
    
    public function archiveConversation(int $conversationId, User $user): bool
    {
        if (!$this->canUserAccessConversation($user, $conversationId)) {
            return false;
        }
        
        // For now, we'll just mark it as archived in future enhancement
        // This is a placeholder for archive functionality
        return true;
    }
    
    public function deleteConversation(int $conversationId, User $user): bool
    {
        if (!$this->canUserAccessConversation($user, $conversationId)) {
            return false;
        }
        
        return $this->conversationRepository->delete($conversationId);
    }
}
