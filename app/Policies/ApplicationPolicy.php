<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Application;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any applications.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the application.
     */
    public function view(User $user, Application $application): bool
    {
        // Admin can view all applications
        if ($user->isAdmin()) {
            return true;
        }

        // Users can only view their own applications through vendor relationship
        return $user->isVendor() && $user->vendor && $user->vendor->id === $application->vendor_id;
    }

    /**
     * Determine whether the user can create applications.
     */
    public function create(User $user): bool
    {
        // Only unverified vendors can create applications
        return $user->isVendor() && !$user->verified;
    }

    /**
     * Determine whether the user can update the application.
     */
    public function update(User $user, Application $application): bool
    {
        // Admin can update any application
        if ($user->isAdmin()) {
            return true;
        }

        // Users can only update their own pending applications through vendor relationship
        return $user->isVendor() &&
               $user->vendor &&
               $user->vendor->id === $application->vendor_id &&
               in_array($application->status, ['pending', 'scored']);
    }

    /**
     * Determine whether the user can delete the application.
     */
    public function delete(User $user, Application $application): bool
    {
        // Only admin can delete applications
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can approve the application.
     */
    public function approve(User $user, Application $application): bool
    {
        return $user->isAdmin() &&
               in_array($application->status, ['meeting_completed', 'scored']);
    }

    /**
     * Determine whether the user can reject the application.
     */
    public function reject(User $user, Application $application): bool
    {
        return $user->isAdmin() &&
               !in_array($application->status, ['approved', 'rejected']);
    }

    /**
     * Determine whether the user can schedule meetings.
     */
    public function scheduleMeeting(User $user, Application $application): bool
    {
        return $user->isAdmin() &&
               in_array($application->status, ['scored', 'pending']);
    }

    /**
     * Determine whether the user can download the application PDF.
     */
    public function downloadPdf(User $user, Application $application): bool
    {
        // Admin can download any PDF
        if ($user->isAdmin()) {
            return true;
        }

        // Users can only download their own PDFs through vendor relationship
        return $user->isVendor() &&
               $user->vendor &&
               $user->vendor->id === $application->vendor_id;
    }
}
