<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Application;
use App\Models\User;
use App\Notifications\VendorMeetingScheduled;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ScheduleVendorMeeting implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $applicationId,
        private Carbon $meetingDateTime
    ) {}

    public function handle(): void
    {
        try {
            $application = Application::findOrFail($this->applicationId);
            $vendor = $application->vendor;
            $user = $vendor->user;

            // Send notification to vendor about meeting
            $user->notify(new VendorMeetingScheduled($application));

            // Notify admins about meeting scheduling
            $this->notifyAdmins($application);

            Log::info("Vendor meeting scheduled", [
                'application_id' => $this->applicationId,
                'meeting_datetime' => $this->meetingDateTime->toDateTimeString(),
                'vendor_id' => $vendor->id
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to process vendor meeting scheduling", [
                'application_id' => $this->applicationId,
                'meeting_datetime' => $this->meetingDateTime->toDateTimeString(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function notifyAdmins(Application $application): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification(
                'Meeting Scheduled',
                "Meeting scheduled for vendor application #{$application->id} on {$this->meetingDateTime->format('M d, Y \a\\t H:i')}"
            ));
        }
    }
}
