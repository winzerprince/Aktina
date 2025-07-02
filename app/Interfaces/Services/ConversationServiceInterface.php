<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface ConversationServiceInterface
{
    public function createOrFindConversation(User $user1, User $user2);
    
    public function getConversation(int $conversationId);
    
    public function getUserConversations(User $user, int $page = 1, int $perPage = 20);
    
    public function updateLastMessage(int $conversationId);
    
    public function canUserAccessConversation(User $user, int $conversationId): bool;
    
    public function archiveConversation(int $conversationId, User $user): bool;
    
    public function deleteConversation(int $conversationId, User $user): bool;
}
