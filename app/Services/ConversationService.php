<?php

namespace App\Services;

use App\Interfaces\Repositories\ConversationRepositoryInterface;
use App\Interfaces\Services\ConversationServiceInterface;
use App\Interfaces\Services\CommunicationPermissionServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ConversationService implements ConversationServiceInterface
{
    protected $conversationRepository;
    protected $communicationPermissionService;

    public function __construct(
        ConversationRepositoryInterface $conversationRepository,
        CommunicationPermissionServiceInterface $communicationPermissionService
    ) {
        $this->conversationRepository = $conversationRepository;
        $this->communicationPermissionService = $communicationPermissionService;
    }

    public function createOrFindConversation(User $user1, User $user2)
    {
        // First check if conversation already exists regardless of permission
        // This allows a user to respond to an existing conversation even if they wouldn't
        // normally be able to initiate one
        $conversation = $this->conversationRepository->findByUsers($user1, $user2);

        // If conversation exists, return it without checking permissions again
        if ($conversation) {
            return $conversation;
        }

        // For new conversations, check if these users can communicate with each other
        if (!$this->communicationPermissionService->canCommunicate($user1, $user2)) {
            return null;
        }

        // Create new conversation
        $conversation = $this->conversationRepository->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
            'last_message_at' => now(),
        ]);

        return $conversation;
    }

    public function createConversation(array $data)
    {
        $user1 = User::find($data['user_one_id']);
        $user2 = User::find($data['user_two_id']);

        if (!$user1 || !$user2) {
            throw new \Exception('Invalid users specified');
        }

        // Check if these users can communicate with each other
        if (!$this->communicationPermissionService->canCommunicate($user1, $user2)) {
            throw new \Exception('Users are not allowed to communicate');
        }

        // Check if conversation already exists
        $existing = $this->conversationRepository->findByUsers($user1, $user2);
        if ($existing) {
            return $existing;
        }

        return $this->conversationRepository->create([
            'user_one_id' => $data['user_one_id'],
            'user_two_id' => $data['user_two_id'],
            'last_message_at' => now(),
        ]);
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

        return $conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id;
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

    /**
     * Get all available contacts that a user can message
     */
    public function getAvailableContacts(User $user)
    {
        return $this->communicationPermissionService->getAvailableContacts($user);
    }

    /**
     * Get all retailers associated with a vendor
     */
    public function getVendorRetailers(User $vendor)
    {
        return $this->communicationPermissionService->getVendorRetailers($vendor);
    }

    /**
     * Get the vendor associated with a retailer
     */
    public function getRetailerVendor(User $retailer)
    {
        return $this->communicationPermissionService->getRetailerVendor($retailer);
    }

    /**
     * Check if two users can communicate with each other
     */
    public function canUsersCommunicate(User $user1, User $user2): bool
    {
        return $this->communicationPermissionService->canCommunicate($user1, $user2);
    }
}
