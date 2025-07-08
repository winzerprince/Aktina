<?php

namespace App\Services;

use App\Interfaces\Services\CommunicationPermissionServiceInterface;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Retailer;
use App\Models\RetailerListing;
use Illuminate\Support\Facades\DB;

class CommunicationPermissionService implements CommunicationPermissionServiceInterface
{
    /**
     * Check if two users can communicate with each other based on roles
     */
    public function canCommunicate(User $user1, User $user2): bool
    {
        // Check if they already have a conversation - if so, always allow communication
        $existingConversation = $this->checkForExistingConversation($user1, $user2);
        if ($existingConversation) {
            return true;
        }

        // Admin, Production Manager, and HR Manager can communicate with each other
        if (
            ($user1->isAdmin() || $user1->isHRManager() || $user1->isProductionManager()) &&
            ($user2->isAdmin() || $user2->isHRManager() || $user2->isProductionManager())
        ) {
            return true;
        }

        // Admin and Production Manager can communicate with Suppliers and Vendors
        if (
            ($user1->isAdmin() || $user1->isProductionManager()) &&
            ($user2->isSupplier() || $user2->isVendor())
        ) {
            return true;
        }

        if (
            ($user2->isAdmin() || $user2->isProductionManager()) &&
            ($user1->isSupplier() || $user1->isVendor())
        ) {
            return true;
        }

        // Vendors can communicate with Admin, Production Manager, and their Retailers
        if ($user1->isVendor() && $user2->isRetailer()) {
            return $this->isVendorForRetailer($user1, $user2);
        }

        if ($user2->isVendor() && $user1->isRetailer()) {
            return $this->isVendorForRetailer($user2, $user1);
        }

        // Retailers can only communicate with their vendor
        if ($user1->isRetailer() && $user2->isVendor()) {
            return $this->isVendorForRetailer($user2, $user1);
        }

        if ($user2->isRetailer() && $user1->isVendor()) {
            return $this->isVendorForRetailer($user1, $user2);
        }

        return false;
    }

    /**
     * Check if there's an existing conversation between two users
     * If a conversation exists, they should be able to continue communicating
     */
    private function checkForExistingConversation(User $user1, User $user2): bool
    {
        return \App\Models\Conversation::where(function($query) use ($user1, $user2) {
            $query->where('user_one_id', $user1->id)
                  ->where('user_two_id', $user2->id);
        })->orWhere(function($query) use ($user1, $user2) {
            $query->where('user_one_id', $user2->id)
                  ->where('user_two_id', $user1->id);
        })->exists();
    }

    /**
     * Get all users that a user can communicate with based on role
     */
    /**
     * Get all contacts that a user can communicate with
     */
    public function getAvailableContacts(User $user): array
    {
        $contacts = [];

        // For admin and production manager, they can communicate with other admins, production managers,
        // hr managers, suppliers, and vendors
        if ($user->isAdmin() || $user->isProductionManager()) {
            $contacts = User::where('id', '!=', $user->id)
                ->whereIn('role', ['admin', 'production_manager', 'hr_manager', 'supplier', 'vendor'])
                ->get()
                ->all();
        }

        // For HR manager, they can communicate with admins, production managers, and other HR managers
        elseif ($user->isHRManager()) {
            $contacts = User::where('id', '!=', $user->id)
                ->whereIn('role', ['admin', 'production_manager', 'hr_manager'])
                ->get()
                ->all();
        }

        // For suppliers, they can communicate with admins and production managers
        elseif ($user->isSupplier()) {
            $contacts = User::whereIn('role', ['admin', 'production_manager'])
                ->get()
                ->all();
        }

        // For vendors, they can communicate with admins, production managers, and their retailers
        elseif ($user->isVendor()) {
            // Get admin and production managers
            $adminContacts = User::whereIn('role', ['admin', 'production_manager'])
                ->get()
                ->all();

            // Get associated retailers
            $retailerContacts = $this->getVendorRetailers($user);

            $contacts = array_merge($adminContacts, $retailerContacts);
        }

        // For retailers, they can only communicate with their vendor
        elseif ($user->isRetailer()) {
            $vendor = $this->getRetailerVendor($user);
            if ($vendor) {
                $contacts = [$vendor];
            }
        }

        return $contacts;
    }

    /**
     * Get all retailers that a vendor can communicate with
     */
    /**
     * Get all retailers that a vendor can communicate with
     */
    public function getVendorRetailers(User $vendor): array
    {
        if (!$vendor->isVendor()) {
            return [];
        }

        $vendorModel = Vendor::where('user_id', $vendor->id)->first();
        if (!$vendorModel || !$vendorModel->application) {
            return [];
        }

        $application = $vendorModel->application;

        // Get all retailer listings for this vendor's application
        $retailerEmails = RetailerListing::where('application_id', $application->id)
            ->pluck('retailer_email')
            ->toArray();

        if (empty($retailerEmails)) {
            return [];
        }

        // Find retailer users with these emails
        $retailers = User::whereIn('email', $retailerEmails)
            ->where('role', 'retailer')
            ->get()
            ->all();

        return $retailers;
    }

    /**
     * Get the vendor that a retailer can communicate with
     */
    public function getRetailerVendor(User $retailer): ?User
    {
        if (!$retailer->isRetailer()) {
            return null;
        }

        // Find retailer listing with this email
        $retailerListing = RetailerListing::where('retailer_email', $retailer->email)->first();
        if (!$retailerListing || !$retailerListing->application) {
            return null;
        }

        $application = $retailerListing->application;

        // Find vendor for this application
        if (!$application->vendor) {
            return null;
        }

        return $application->vendor->user;
    }

    /**
     * Check if a vendor is associated with a retailer
     */
    private function isVendorForRetailer(User $vendor, User $retailer): bool
    {
        if (!$vendor->isVendor() || !$retailer->isRetailer()) {
            return false;
        }

        $vendorModel = Vendor::where('user_id', $vendor->id)->first();
        if (!$vendorModel || !$vendorModel->application) {
            return false;
        }

        return RetailerListing::where('application_id', $vendorModel->application->id)
            ->where('retailer_email', $retailer->email)
            ->exists();
    }
}
