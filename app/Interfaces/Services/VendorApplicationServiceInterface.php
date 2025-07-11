<?php

namespace App\Interfaces\Services;

use Illuminate\Support\Carbon;

interface VendorApplicationServiceInterface
{
    /**
     * Change the status of a vendor application
     */
    public function changeApplicationStatus(int $applicationId, string $newStatus): bool;

    /**
     * Schedule a meeting for a vendor application
     */
    public function scheduleMeeting(int $applicationId, Carbon $meetingDateTime): bool;

    /**
     * Trigger PDF processing for an application
     */
    public function triggerPdfProcessing(int $applicationId): bool;

    /**
     * Get vendor applications with filtering and pagination
     */
    public function getApplications(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Get application statistics
     */
    public function getApplicationStats(): array;

    /**
     * Get available status transitions for an application
     */
    public function getAvailableStatusTransitions(string $currentStatus): array;
}
