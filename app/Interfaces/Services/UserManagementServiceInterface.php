<?php

namespace App\Interfaces\Services;

interface UserManagementServiceInterface
{
    /**
     * Get users with filtering and pagination
     */
    public function getUsers(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Verify a user's email
     */
    public function verifyUser(int $userId): bool;

    /**
     * Unverify a user's email
     */
    public function unverifyUser(int $userId): bool;

    /**
     * Get user statistics
     */
    public function getUserStats(): array;
}
