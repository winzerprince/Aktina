<?php

namespace App\Interfaces\Repositories;

use App\Models\Application;
use App\Models\Vendor;

interface ApplicationRepositoryInterface
{
    /**
     * Find application by ID
     */
    public function findById(int $id): ?Application;

    /**
     * Find application by vendor
     */
    public function findByVendor(Vendor $vendor): ?Application;

    /**
     * Get all applications with optional filters
     */
    public function getAll(array $filters = []): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get applications by status
     */
    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get pending applications with scores
     */
    public function getPendingWithScores(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Create new application
     */
    public function create(array $data): Application;

    /**
     * Update application
     */
    public function update(Application $application, array $data): bool;

    /**
     * Delete application
     */
    public function delete(Application $application): bool;

    /**
     * Get applications requiring admin review
     */
    public function getRequiringReview(): \Illuminate\Database\Eloquent\Collection;
}
