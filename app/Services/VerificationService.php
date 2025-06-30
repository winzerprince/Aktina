<?php

namespace App\Services;

use App\Interfaces\Services\VerificationServiceInterface;
use App\Models\User;
use App\Models\Application;
use App\Notifications\UserVerificationComplete;

class VerificationService implements VerificationServiceInterface
{
    /**
     * Check if user is fully verified based on their role
     */
    public function isUserFullyVerified(User $user): bool
    {
        // First check email verification
        if (!$user->hasVerifiedEmail()) {
            return false;
        }

        // Check role-specific verification
        return match ($user->role) {
            'admin' => true, // Admin bypasses all verification
            'vendor' => $this->isVendorApplicationComplete($user),
            'retailer' => $this->isRetailerDemographicsComplete($user),
            'supplier', 'production_manager', 'hr_manager' => true, // Just email verification
            default => false,
        };
    }

    /**
     * Get verification requirements for a specific role
     */
    public function getVerificationRequirements(string $role): array
    {
        return match ($role) {
            'admin' => [
                'email_verification' => false, // Admin bypasses
            ],
            'vendor' => [
                'email_verification' => true,
                'application_submission' => true,
                'application_approval' => true,
            ],
            'retailer' => [
                'email_verification' => true,
                'demographics_completion' => true,
            ],
            'supplier', 'production_manager', 'hr_manager' => [
                'email_verification' => true,
            ],
            default => [
                'email_verification' => true,
            ],
        };
    }

    /**
     * Mark user as verified (email verification)
     */
    public function markAsVerified(User $user): bool
    {
        $wasVerifiedBefore = $this->isUserFullyVerified($user);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Send notification if user is now fully verified and wasn't before
        if (!$wasVerifiedBefore && $this->isUserFullyVerified($user)) {
            $user->notify(new UserVerificationComplete($user));
        }

        return true;
    }

    /**
     * Complete retailer demographics and send notification if fully verified
     */
    public function completeRetailerDemographics(User $user): bool
    {
        if ($this->isUserFullyVerified($user)) {
            $user->notify(new UserVerificationComplete($user));
            return true;
        }
        return false;
    }

    /**
     * Check if vendor has completed application process
     */
    public function isVendorApplicationComplete(User $user): bool
    {
        if ($user->role !== 'vendor') {
            return false;
        }

        // Check if vendor exists and has approved application
        $vendor = $user->vendor;
        if (!$vendor) {
            return false;
        }

        $application = $vendor->application;
        return $application && $application->isApproved();
    }

    /**
     * Check if retailer has completed demographics
     */
    public function isRetailerDemographicsComplete(User $user): bool
    {
        if ($user->role !== 'retailer') {
            return false;
        }

        $retailer = $user->retailer;
        if (!$retailer) {
            return false;
        }

        // Check if all required demographic fields are filled
        return !is_null($retailer->male_female_ratio) &&
               !is_null($retailer->city) &&
               !is_null($retailer->urban_rural_classification) &&
               !is_null($retailer->customer_age_class) &&
               !is_null($retailer->customer_income_bracket) &&
               !is_null($retailer->customer_education_level);
    }

    /**
     * Get verification status for user
     */
    public function getVerificationStatus(User $user): array
    {
        $requirements = $this->getVerificationRequirements($user->role);
        $status = [
            'role' => $user->role,
            'fully_verified' => $this->isUserFullyVerified($user),
            'email_verified' => $user->hasVerifiedEmail(),
        ];

        // Add role-specific status
        if (isset($requirements['application_submission'])) {
            $vendor = $user->vendor;
            $application = $vendor?->application;

            $status['application_submitted'] = (bool) $application;
            $status['application_status'] = $application?->status ?? null;
            $status['application_approved'] = $application?->isApproved() ?? false;
        }

        if (isset($requirements['demographics_completion'])) {
            $status['demographics_completed'] = $this->isRetailerDemographicsComplete($user);
        }

        return $status;
    }
}
