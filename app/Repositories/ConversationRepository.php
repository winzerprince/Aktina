<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ConversationRepositoryInterface;
use App\Models\Conversation;
use App\Models\User;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function create(array $data): Conversation
    {
        return Conversation::create($data);
    }
    
    public function findByUsers(User $user1, User $user2): ?Conversation
    {
        return Conversation::where(function ($query) use ($user1, $user2) {
            $query->where('user1_id', $user1->id)->where('user2_id', $user2->id);
        })->orWhere(function ($query) use ($user1, $user2) {
            $query->where('user1_id', $user2->id)->where('user2_id', $user1->id);
        })->first();
    }
    
    public function findById(int $conversationId): ?Conversation
    {
        return Conversation::with(['user1', 'user2', 'messages'])->find($conversationId);
    }
    
    public function getUserConversations(User $user, int $page = 1, int $perPage = 20)
    {
        return Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['user1', 'user2'])
            ->withCount(['messages'])
            ->orderBy('last_message_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
    
    public function update(int $conversationId, array $data): bool
    {
        return Conversation::where('id', $conversationId)->update($data);
    }
    
    public function delete(int $conversationId): bool
    {
        $conversation = Conversation::find($conversationId);
        if ($conversation) {
            // Delete all messages and files in this conversation
            $conversation->messages()->each(function ($message) {
                $message->files()->delete();
                $message->delete();
            });
            return $conversation->delete();
        }
        return false;
    }
    
    public function updateLastMessage(int $conversationId): void
    {
        $conversation = Conversation::find($conversationId);
        if ($conversation) {
            $latestMessage = $conversation->messages()->latest()->first();
            $conversation->update([
                'last_message_at' => $latestMessage ? $latestMessage->created_at : now(),
            ]);
        }
    }
}
