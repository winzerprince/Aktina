<?php

namespace App\Services;

use App\Interfaces\Services\VendorApplicationServiceInterface;
use App\Models\Application;
use App\Models\User;
use App\Jobs\ProcessVendorStatusChange;
use App\Jobs\ScheduleVendorMeeting;
use App\Jobs\TriggerPdfProcessing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class VendorApplicationService implements VendorApplicationServiceInterface
{
    /**
     * Change the status of a vendor application
     */
    public function changeApplicationStatus(int $applicationId, string $newStatus): bool
    {
        try {
            $application = Application::findOrFail($applicationId);

            // Validate status transition
            if (!$this->isValidStatusTransition($application->status, $newStatus)) {
                throw new \InvalidArgumentException("Invalid status transition from {$application->status} to {$newStatus}");
            }

            $application->status = $newStatus;
            $application->save();

            // Dispatch job for status change processing
            ProcessVendorStatusChange::dispatch($applicationId, $newStatus);

            Log::info("Application status changed", [
                'application_id' => $applicationId,
                'old_status' => $application->getOriginal('status'),
                'new_status' => $newStatus
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to change application status", [
                'application_id' => $applicationId,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Schedule a meeting for a vendor application
     */
    public function scheduleMeeting(int $applicationId, Carbon $meetingDateTime): bool
    {
        try {
            $application = Application::findOrFail($applicationId);

            // Validate that meeting can be scheduled for this application
            if (!in_array($application->status, ['pending', 'scored'])) {
                throw new \InvalidArgumentException("Cannot schedule meeting for application with status: {$application->status}");
            }

            // Validate meeting is in the future
            if ($meetingDateTime->isPast()) {
                throw new \InvalidArgumentException("Meeting cannot be scheduled in the past");
            }

            $application->meeting_schedule = $meetingDateTime;
            $application->status = Application::STATUS_MEETING_SCHEDULED;
            $application->save();

            // Dispatch job for meeting scheduling
            ScheduleVendorMeeting::dispatch($applicationId, $meetingDateTime);

            Log::info("Meeting scheduled", [
                'application_id' => $applicationId,
                'meeting_datetime' => $meetingDateTime->toDateTimeString()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to schedule meeting", [
                'application_id' => $applicationId,
                'meeting_datetime' => $meetingDateTime->toDateTimeString(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Trigger PDF processing for an application
     */
    public function triggerPdfProcessing(int $applicationId): bool
    {
        try {
            $application = Application::findOrFail($applicationId);

            if (!$application->pdf_path) {
                throw new \InvalidArgumentException("No PDF file found for application {$applicationId}");
            }

            if ($application->processed_by_java_server) {
                throw new \InvalidArgumentException("Application {$applicationId} has already been processed");
            }

            // Dispatch job for PDF processing
            TriggerPdfProcessing::dispatch($applicationId);

            Log::info("PDF processing triggered", [
                'application_id' => $applicationId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to trigger PDF processing", [
                'application_id' => $applicationId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get vendor applications with filtering and pagination
     */
    public function getApplications(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Application::with(['vendor.user']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->whereHas('vendor.user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply status filter
        if (!empty($filters['status_filter'])) {
            $query->where('status', $filters['status_filter']);
        }

        // Apply sorting
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Apply pagination
        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Get application statistics
     */
    public function getApplicationStats(): array
    {
        return [
            'total' => Application::count(),
            'pending' => Application::where('status', Application::STATUS_PENDING)->count(),
            'scored' => Application::where('status', Application::STATUS_SCORED)->count(),
            'meeting_scheduled' => Application::where('status', Application::STATUS_MEETING_SCHEDULED)->count(),
            'meeting_completed' => Application::where('status', Application::STATUS_MEETING_COMPLETED)->count(),
            'approved' => Application::where('status', Application::STATUS_APPROVED)->count(),
            'rejected' => Application::where('status', Application::STATUS_REJECTED)->count(),
            'processing_pending' => Application::where('processed_by_java_server', false)->count(),
        ];
    }

    /**
     * Validate if a status transition is allowed
     */
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $allowedTransitions = [
            Application::STATUS_PENDING => [
                Application::STATUS_SCORED,
                Application::STATUS_MEETING_SCHEDULED,
                Application::STATUS_REJECTED
            ],
            Application::STATUS_SCORED => [
                Application::STATUS_MEETING_SCHEDULED,
                Application::STATUS_APPROVED,
                Application::STATUS_REJECTED
            ],
            Application::STATUS_MEETING_SCHEDULED => [
                Application::STATUS_MEETING_COMPLETED,
                Application::STATUS_REJECTED
            ],
            Application::STATUS_MEETING_COMPLETED => [
                Application::STATUS_APPROVED,
                Application::STATUS_REJECTED
            ],
            Application::STATUS_APPROVED => [], // Terminal state
            Application::STATUS_REJECTED => []  // Terminal state
        ];

        return in_array($newStatus, $allowedTransitions[$currentStatus] ?? []);
    }

    /**
     * Get available status transitions for an application
     */
    public function getAvailableStatusTransitions(string $currentStatus): array
    {
        $transitions = [
            Application::STATUS_PENDING => [
                Application::STATUS_SCORED => 'Mark as Scored'
            ],
            Application::STATUS_SCORED => [
                Application::STATUS_MEETING_SCHEDULED => 'Schedule Meeting',
                Application::STATUS_APPROVED => 'Approve',
                Application::STATUS_REJECTED => 'Reject'
            ],
            Application::STATUS_MEETING_SCHEDULED => [
                Application::STATUS_MEETING_COMPLETED => 'Mark Meeting Complete'
            ],
            Application::STATUS_MEETING_COMPLETED => [
                Application::STATUS_APPROVED => 'Approve',
                Application::STATUS_REJECTED => 'Reject'
            ]
        ];

        return $transitions[$currentStatus] ?? [];
    }
}
