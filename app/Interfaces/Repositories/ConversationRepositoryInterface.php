<?php

namespace App\Interfaces\Repositories;

use App\Models\Conversation;
use App\Models\User;

interface ConversationRepositoryInterface
{
    public function create(array $data): Conversation;
    
    public function findByUsers(User $user1, User $user2): ?Conversation;
    
    public function findById(int $conversationId): ?Conversation;
    
    public function getUserConversations(User $user, int $page = 1, int $perPage = 20);
    
    public function update(int $conversationId, array $data): bool;
    
    public function delete(int $conversationId): bool;
    
    public function updateLastMessage(int $conversationId): void;
}
