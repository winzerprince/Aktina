<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface VerificationServiceInterface
{
    /**
     * Check if user is fully verified based on their role
     */
    public function isUserFullyVerified(User $user): bool;

    /**
     * Get verification requirements for a specific role
     */
    public function getVerificationRequirements(string $role): array;

    /**
     * Mark user as verified (email verification)
     */
    public function markAsVerified(User $user): bool;

    /**
     * Check if vendor has completed application process
     */
    public function isVendorApplicationComplete(User $user): bool;

    /**
     * Check if retailer has completed demographics
     */
    public function isRetailerDemographicsComplete(User $user): bool;

    /**
     * Get verification status for user
     */
    public function getVerificationStatus(User $user): array;
}
