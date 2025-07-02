<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface ReportServiceInterface
{
    public function generatePDFReport(string $reportType, array $data, array $options = []);
    
    public function generateCSVExport(string $reportType, array $data, array $options = []);
    
    public function scheduleReport(User $user, string $reportType, string $frequency, array $options = []);
    
    public function getAvailableReports(User $user);
    
    public function getReportHistory(User $user, int $limit = 20);
    
    public function downloadReport(int $reportId, User $user);
    
    public function deleteReport(int $reportId, User $user): bool;
}
