<?php

namespace App\Interfaces\Services;

use App\Models\Application;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;

interface ApplicationServiceInterface
{
    /**
     * Submit vendor application with PDF
     */
    public function submitApplication(Vendor $vendor, UploadedFile $pdf): Application;

    /**
     * Process application scoring (called by Java server)
     */
    public function processScoring(Application $application, int $score, array $formData = []): bool;

    /**
     * Schedule meeting for application
     */
    public function scheduleMeeting(Application $application, string $date): bool;

    /**
     * Complete meeting and add notes
     */
    public function completeMeeting(Application $application, string $notes = null): bool;

    /**
     * Approve application
     */
    public function approveApplication(Application $application): bool;

    /**
     * Reject application
     */
    public function rejectApplication(Application $application, string $reason = null): bool;

    /**
     * Get applications for admin review
     */
    public function getApplicationsForReview(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get vendor application status
     */
    public function getVendorApplicationStatus(Vendor $vendor): ?array;

    /**
     * Send notification to vendor about application status
     */
    public function notifyVendor(Application $application, string $type): bool;
}
