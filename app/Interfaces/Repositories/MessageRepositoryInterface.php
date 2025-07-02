<?php

namespace App\Interfaces\Repositories;

use App\Models\Message;
use App\Models\User;

interface MessageRepositoryInterface
{
    public function create(array $data): Message;
    
    public function getByConversation(int $conversationId, int $page = 1, int $perPage = 50);
    
    public function findById(int $messageId): ?Message;
    
    public function update(int $messageId, array $data): bool;
    
    public function delete(int $messageId): bool;
    
    public function getUnreadCountForUser(User $user): int;
    
    public function markAsRead(int $conversationId, User $user): void;
    
    public function getLatestByConversation(int $conversationId): ?Message;
}
