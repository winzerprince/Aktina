<?php

namespace App\Services;

use App\Models\User;
use App\Services\ReportGeneratorService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportSchedulerService
{
    protected ReportGeneratorService $reportGenerator;

    public function __construct(ReportGeneratorService $reportGenerator)
    {
        $this->reportGenerator = $reportGenerator;
    }

    /**
     * Schedule and generate automated reports
     */
    public function generateScheduledReports(): array
    {
        $results = [];

        try {
            // Daily reports
            $results['daily'] = $this->generateDailyReports();
            
            // Weekly reports (Mondays)
            if (now()->isMonday()) {
                $results['weekly'] = $this->generateWeeklyReports();
            }
            
            // Monthly reports (1st of month)
            if (now()->day === 1) {
                $results['monthly'] = $this->generateMonthlyReports();
            }

            Log::info('Scheduled reports generated successfully', $results);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate scheduled reports: ' . $e->getMessage());
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Generate daily reports
     */
    private function generateDailyReports(): array
    {
        $results = [];
        $yesterday = now()->subDay();
        
        $filters = [
            'start_date' => $yesterday->format('Y-m-d'),
            'end_date' => $yesterday->format('Y-m-d')
        ];

        // Daily sales report
        $results['sales'] = $this->generateAndStoreReport('sales', $filters, 'daily');
        
        // Daily orders report
        $results['orders'] = $this->generateAndStoreReport('orders', $filters, 'daily');
        
        // Daily inventory status
        $results['inventory'] = $this->generateAndStoreReport('inventory', $filters, 'daily');

        return $results;
    }

    /**
     * Generate weekly reports
     */
    private function generateWeeklyReports(): array
    {
        $results = [];
        $lastWeek = now()->subWeek();
        
        $filters = [
            'start_date' => $lastWeek->startOfWeek()->format('Y-m-d'),
            'end_date' => $lastWeek->endOfWeek()->format('Y-m-d')
        ];

        // Weekly comprehensive report
        $results['comprehensive'] = $this->generateAndStoreReport('comprehensive', $filters, 'weekly');
        
        // Weekly production report
        $results['production'] = $this->generateAndStoreReport('production', $filters, 'weekly');

        return $results;
    }

    /**
     * Generate monthly reports
     */
    private function generateMonthlyReports(): array
    {
        $results = [];
        $lastMonth = now()->subMonth();
        
        $filters = [
            'start_date' => $lastMonth->startOfMonth()->format('Y-m-d'),
            'end_date' => $lastMonth->endOfMonth()->format('Y-m-d')
        ];

        // Monthly reports for all types
        $reportTypes = ['sales', 'inventory', 'users', 'orders', 'production', 'comprehensive'];
        
        foreach ($reportTypes as $type) {
            $results[$type] = $this->generateAndStoreReport($type, $filters, 'monthly');
        }

        return $results;
    }

    /**
     * Generate and store a report
     */
    private function generateAndStoreReport(string $type, array $filters, string $frequency): array
    {
        try {
            // Generate CSV report
            $csvData = $this->reportGenerator->generateCSVData($type, $filters);
            $csvPath = $this->storeReport($csvData['filename'], $this->arrayToCSV($csvData['headers'], $csvData['data']), 'csv');
            
            // Generate PDF report
            $pdfData = $this->reportGenerator->generatePDFData($type, $filters);
            $pdfContent = $this->generateSimplePDFContent($pdfData);
            $pdfPath = $this->storeReport($pdfData['filename'], $pdfContent, 'pdf');

            // Send to relevant users
            $this->sendReportToUsers($type, $frequency, $csvPath, $pdfPath, $filters);

            $recipients = $this->getReportRecipients($type, $frequency);
            return [
                'success' => true,
                'csv_path' => $csvPath,
                'pdf_path' => $pdfPath,
                'sent_to' => $recipients
            ];

        } catch (\Exception $e) {
            Log::error("Failed to generate {$frequency} {$type} report: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Store report file
     */
    private function storeReport(string $filename, string $content, string $type): string
    {
        $directory = 'reports/' . now()->format('Y/m');
        $path = $directory . '/' . $filename;
        
        Storage::disk('local')->put($path, $content);
        
        return $path;
    }

    /**
     * Send report to relevant users
     */
    private function sendReportToUsers(string $type, string $frequency, string $csvPath, string $pdfPath, array $filters): void
    {
        $recipients = $this->getReportRecipients($type, $frequency);
        
        foreach ($recipients as $user) {
            try {
                // In a real implementation, you'd send actual emails
                // Mail::to($user->email)->send(new ReportNotification($type, $frequency, $csvPath, $pdfPath, $filters));
                
                // For simulation, just log it
                if (is_object($user) && isset($user->email)) {
                    $userEmail = $user->email;
                    $userId = $user->id ?? 'unknown';
                } else {
                    $userEmail = $user['email'] ?? 'unknown@example.com';
                    $userId = $user['id'] ?? 'unknown';
                }
                
                Log::info("Report sent to {$userEmail}", [
                    'type' => $type,
                    'frequency' => $frequency,
                    'user_id' => $userId
                ]);
                
            } catch (\Exception $e) {
                $userEmail = is_object($user) && isset($user->email) ? $user->email : 'unknown@example.com';
                Log::error("Failed to send report to {$userEmail}: " . $e->getMessage());
            }
        }
    }

    /**
     * Get report recipients based on type and frequency
     */
    private function getReportRecipients(string $type, string $frequency): array
    {
        $recipientRoles = [];

        switch ($type) {
            case 'sales':
            case 'orders':
                $recipientRoles = ['admin', 'vendor', 'production_manager'];
                break;
                
            case 'inventory':
                $recipientRoles = ['admin', 'production_manager', 'supplier'];
                break;
                
            case 'users':
                $recipientRoles = ['admin', 'hr_manager'];
                break;
                
            case 'production':
                $recipientRoles = ['admin', 'production_manager'];
                break;
                
            case 'comprehensive':
                if ($frequency === 'monthly') {
                    $recipientRoles = ['admin'];
                } else {
                    $recipientRoles = ['admin', 'production_manager'];
                }
                break;
                
            default:
                $recipientRoles = ['admin'];
        }
        
        // Just return basic data structure rather than User objects
        return User::whereIn('role', $recipientRoles)
            ->get(['id', 'name', 'email'])
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ];
            })
            ->toArray();

        return $recipients->toArray();
    }

    /**
     * Clean up old reports
     */
    public function cleanupOldReports(int $daysToKeep = 90): array
    {
        $results = ['deleted' => 0, 'errors' => 0];
        
        try {
            $cutoffDate = now()->subDays($daysToKeep);
            $reportDirectory = 'reports';
            
            if (Storage::disk('local')->exists($reportDirectory)) {
                $files = Storage::disk('local')->allFiles($reportDirectory);
                
                foreach ($files as $file) {
                    $lastModified = Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file));
                    
                    if ($lastModified->isBefore($cutoffDate)) {
                        try {
                            Storage::disk('local')->delete($file);
                            $results['deleted']++;
                        } catch (\Exception $e) {
                            $results['errors']++;
                            Log::error("Failed to delete old report file {$file}: " . $e->getMessage());
                        }
                    }
                }
            }
            
            Log::info("Report cleanup completed", $results);
            
        } catch (\Exception $e) {
            Log::error('Report cleanup failed: ' . $e->getMessage());
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Get report statistics
     */
    public function getReportStatistics(): array
    {
        try {
            $reportDirectory = 'reports';
            $stats = [
                'total_reports' => 0,
                'total_size' => 0,
                'reports_by_month' => [],
                'reports_by_type' => []
            ];
            
            if (Storage::disk('local')->exists($reportDirectory)) {
                $files = Storage::disk('local')->allFiles($reportDirectory);
                
                foreach ($files as $file) {
                    $stats['total_reports']++;
                    $stats['total_size'] += Storage::disk('local')->size($file);
                    
                    // Extract month from path
                    $pathParts = explode('/', $file);
                    if (count($pathParts) >= 3) {
                        $month = $pathParts[1] . '/' . $pathParts[2];
                        $stats['reports_by_month'][$month] = ($stats['reports_by_month'][$month] ?? 0) + 1;
                    }
                    
                    // Extract type from filename
                    $filename = basename($file);
                    $type = explode('_', $filename)[0] ?? 'unknown';
                    $stats['reports_by_type'][$type] = ($stats['reports_by_type'][$type] ?? 0) + 1;
                }
            }
            
            $stats['total_size_mb'] = round($stats['total_size'] / 1024 / 1024, 2);
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Failed to get report statistics: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Helper methods
     */
    private function arrayToCSV(array $headers, array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        fputcsv($output, $headers);
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }

    private function generateSimplePDFContent(array $pdfData): string
    {
        $content = "=== {$pdfData['title']} ===\n\n";
        $content .= "Generated: {$pdfData['metadata']['generated_at']}\n";
        $content .= "Generated by: {$pdfData['metadata']['generated_by']}\n";
        $content .= "Date Range: {$pdfData['metadata']['date_range']}\n";
        $content .= "Total Records: {$pdfData['metadata']['total_records']}\n\n";
        
        if (isset($pdfData['data']['summary'])) {
            $content .= "=== SUMMARY ===\n";
            foreach ($pdfData['data']['summary'] as $key => $value) {
                $content .= ucwords(str_replace('_', ' ', $key)) . ": " . $value . "\n";
            }
            $content .= "\n";
        }
        
        $content .= "=== DETAILED DATA ===\n";
        $content .= json_encode($pdfData['data'], JSON_PRETTY_PRINT);
        
        return $content;
    }
}
