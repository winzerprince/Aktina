<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ApplicationRepositoryInterface;
use App\Models\Application;
use App\Models\Vendor;

class ApplicationRepository implements ApplicationRepositoryInterface
{
    /**
     * Find application by ID
     */
    public function findById(int $id): ?Application
    {
        return Application::find($id);
    }

    /**
     * Find application by vendor
     */
    public function findByVendor(Vendor $vendor): ?Application
    {
        return Application::where('vendor_id', $vendor->id)->first();
    }

    /**
     * Get all applications with optional filters
     */
    public function getAll(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Application::with('vendor.user');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['has_score'])) {
            if ($filters['has_score']) {
                $query->whereNotNull('score');
            } else {
                $query->whereNull('score');
            }
        }

        if (isset($filters['processed'])) {
            $query->where('processed_by_java_server', $filters['processed']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get applications by status
     */
    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Application::with('vendor.user')
                          ->where('status', $status)
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    /**
     * Get pending applications with scores
     */
    public function getPendingWithScores(): \Illuminate\Database\Eloquent\Collection
    {
        return Application::with('vendor.user')
                          ->where('status', Application::STATUS_SCORED)
                          ->whereNotNull('score')
                          ->orderBy('score', 'desc')
                          ->get();
    }

    /**
     * Create new application
     */
    public function create(array $data): Application
    {
        return Application::create($data);
    }

    /**
     * Update application
     */
    public function update(Application $application, array $data): bool
    {
        return $application->update($data);
    }

    /**
     * Delete application
     */
    public function delete(Application $application): bool
    {
        return $application->delete();
    }

    /**
     * Get applications requiring admin review
     */
    public function getRequiringReview(): \Illuminate\Database\Eloquent\Collection
    {
        return Application::with('vendor.user')
                          ->whereIn('status', [
                              Application::STATUS_SCORED,
                              Application::STATUS_MEETING_COMPLETED
                          ])
                          ->orderBy('score', 'desc')
                          ->get();
    }
}
