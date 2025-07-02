<?php

namespace App\Repositories;

use App\Interfaces\Repositories\MessageRepositoryInterface;
use App\Models\Message;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageRepository implements MessageRepositoryInterface
{
    public function create(array $data): Message
    {
        return Message::create($data);
    }
    
    public function getByConversation(int $conversationId, int $page = 1, int $perPage = 50)
    {
        return Message::where('conversation_id', $conversationId)
            ->with(['sender', 'files'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
    
    public function findById(int $messageId): ?Message
    {
        return Message::with(['sender', 'files', 'conversation'])->find($messageId);
    }
    
    public function update(int $messageId, array $data): bool
    {
        return Message::where('id', $messageId)->update($data);
    }
    
    public function delete(int $messageId): bool
    {
        $message = Message::find($messageId);
        if ($message) {
            // Delete associated files
            $message->files()->delete();
            return $message->delete();
        }
        return false;
    }
    
    public function getUnreadCountForUser(User $user): int
    {
        return Message::whereHas('conversation', function ($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->where('sender_id', '!=', $user->id)
        ->where('is_read', false)
        ->count();
    }
    
    public function markAsRead(int $conversationId, User $user): void
    {
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
    
    public function getLatestByConversation(int $conversationId): ?Message
    {
        return Message::where('conversation_id', $conversationId)
            ->latest()
            ->first();
    }
}
