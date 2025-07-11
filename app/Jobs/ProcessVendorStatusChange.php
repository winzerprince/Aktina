<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Application;
use App\Models\User;
use App\Notifications\VendorApplicationScored;
use App\Notifications\VendorApplicationApproved;
use App\Notifications\VendorApplicationRejected;
use Illuminate\Support\Facades\Log;

class ProcessVendorStatusChange implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $applicationId,
        private string $newStatus
    ) {}

    public function handle(): void
    {
        try {
            $application = Application::findOrFail($this->applicationId);
            $vendor = $application->vendor;
            $user = $vendor->user;

            // Send appropriate notifications based on status
            switch ($this->newStatus) {
                case Application::STATUS_SCORED:
                    $user->notify(new VendorApplicationScored($application));
                    break;

                case Application::STATUS_APPROVED:
                    $user->notify(new VendorApplicationApproved($application));
                    $this->activateVendorAccount($user);
                    break;

                case Application::STATUS_REJECTED:
                    $user->notify(new VendorApplicationRejected($application));
                    break;
            }

            // Notify admins about status changes
            $this->notifyAdmins($application, $this->newStatus);

            Log::info("Vendor application status changed", [
                'application_id' => $this->applicationId,
                'new_status' => $this->newStatus,
                'vendor_id' => $vendor->id
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to process vendor status change", [
                'application_id' => $this->applicationId,
                'status' => $this->newStatus,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function activateVendorAccount(User $user): void
    {
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }
    }

    private function notifyAdmins(Application $application, string $status): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification(
                'Vendor Status Update',
                "Vendor application #{$application->id} status changed to: " . ucfirst(str_replace('_', ' ', $status))
            ));
        }
    }
}
