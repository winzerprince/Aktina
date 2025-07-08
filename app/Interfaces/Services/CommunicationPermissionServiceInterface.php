<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface CommunicationPermissionServiceInterface
{
    /**
     * Check if two users can communicate with each other
     */
    public function canCommunicate(User $user1, User $user2): bool;

    /**
     * Get all users that a user can communicate with
     */
    public function getAvailableContacts(User $user): array;

    /**
     * Get all retailers that a vendor can communicate with
     */
    public function getVendorRetailers(User $vendor): array;

    /**
     * Get the vendor that a retailer can communicate with
     */
    public function getRetailerVendor(User $retailer): ?User;
}
