<?php

namespace App\Interfaces\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface MessageServiceInterface
{
    public function sendMessage(User $sender, User $recipient, string $content, ?UploadedFile $file = null);

    public function getConversation(User $user1, User $user2);

    public function getConversationMessages(int $conversationId, int $page = 1, int $perPage = 50);

    public function getUserConversations(User $user);

    public function markAsRead(int $conversationId, User $user);

    public function markMessagesAsRead(int $conversationId, int $userId);

    public function uploadFile(UploadedFile $file, int $messageId): string;

    public function deleteMessage(int $messageId, User $user): bool;

    public function getUnreadCount(User $user): int;

    public function createMessage(array $data);

    public function addMessageFile(int $messageId, UploadedFile $file);
}
