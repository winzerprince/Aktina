<?php

namespace App\Services;

use App\Interfaces\Services\ApplicationServiceInterface;
use App\Interfaces\Repositories\ApplicationRepositoryInterface;
use App\Models\Application;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\VendorApplicationReceived;
use App\Notifications\VendorApplicationScored;
use App\Notifications\VendorMeetingScheduled;
use App\Notifications\VendorApplicationApproved;
use App\Notifications\VendorApplicationRejected;
use App\Notifications\AdminNewApplicationSubmitted;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationService implements ApplicationServiceInterface
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository
    ) {}

    /**
     * Submit vendor application with PDF
     */
    public function submitApplication(Vendor $vendor, UploadedFile $pdf): Application
    {
        // Store PDF file
        $filename = 'application_' . $vendor->id . '_' . time() . '.pdf';
        $path = $pdf->storeAs('applications', $filename, 'public');

        // Create application
        $applicationData = [
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING,
            'pdf_path' => 'storage/' . $path,
            'application_reference' => 'APP-' . strtoupper(Str::random(6)),
        ];

        $application = $this->applicationRepository->create($applicationData);

        // Send notifications
        $vendor->user->notify(new VendorApplicationReceived($application));
        $this->notifyAdmins($application);

        return $application;
    }

    /**
     * Process application scoring (called by Java server)
     */
    public function processScoring(Application $application, int $score, array $formData = []): bool
    {
        $updateData = [
            'score' => $score,
            'status' => Application::STATUS_SCORED,
            'form_data' => $formData,
            'processed_by_java_server' => true,
            'processing_date' => now(),
        ];

        $success = $this->applicationRepository->update($application, $updateData);

        if ($success) {
            $application->refresh();
            $application->vendor->user->notify(new VendorApplicationScored($application));
        }

        return $success;
    }

    /**
     * Schedule meeting for application
     */
    public function scheduleMeeting(Application $application, string $date): bool
    {
        $updateData = [
            'meeting_schedule' => $date,
            'status' => Application::STATUS_MEETING_SCHEDULED,
        ];

        $success = $this->applicationRepository->update($application, $updateData);

        if ($success) {
            $application->refresh();
            $application->vendor->user->notify(new VendorMeetingScheduled($application));
        }

        return $success;
    }

    /**
     * Complete meeting and add notes
     */
    public function completeMeeting(Application $application, string $notes = null): bool
    {
        $updateData = [
            'meeting_notes' => $notes,
            'status' => Application::STATUS_MEETING_COMPLETED,
        ];

        return $this->applicationRepository->update($application, $updateData);
    }

    /**
     * Approve application
     */
    public function approveApplication(Application $application): bool
    {
        $updateData = [
            'status' => Application::STATUS_APPROVED,
        ];

        $success = $this->applicationRepository->update($application, $updateData);

        if ($success) {
            // Mark vendor user as verified and send notification
            $vendor = $application->vendor;
            if ($vendor && $vendor->user) {
                if (!$vendor->user->hasVerifiedEmail()) {
                    $vendor->user->markEmailAsVerified();
                }
                $application->refresh();
                $vendor->user->notify(new VendorApplicationApproved($application));
            }
        }

        return $success;
    }

    /**
     * Reject application
     */
    public function rejectApplication(Application $application, string $reason = null): bool
    {
        $updateData = [
            'status' => Application::STATUS_REJECTED,
            'meeting_notes' => $reason ? "Rejection reason: " . $reason : null,
        ];

        $success = $this->applicationRepository->update($application, $updateData);

        if ($success) {
            $application->refresh();
            $application->vendor->user->notify(new VendorApplicationRejected($application, $reason));
        }

        return $success;
    }

    /**
     * Get applications for admin review
     */
    public function getApplicationsForReview(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->applicationRepository->getRequiringReview();
    }

    /**
     * Get vendor application status
     */
    public function getVendorApplicationStatus(Vendor $vendor): ?array
    {
        $application = $this->applicationRepository->findByVendor($vendor);

        if (!$application) {
            return null;
        }

        return [
            'id' => $application->id,
            'status' => $application->status,
            'score' => $application->score,
            'meeting_schedule' => $application->meeting_schedule,
            'meeting_notes' => $application->meeting_notes,
            'reference' => $application->application_reference,
            'submitted_at' => $application->created_at,
            'processed_at' => $application->processing_date,
        ];
    }

    /**
     * Send notification to admins about new application
     */
    public function notifyAdmins(Application $application): bool
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new AdminNewApplicationSubmitted($application));
        }

        return true;
    }
}
