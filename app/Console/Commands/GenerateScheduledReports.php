<?php

namespace App\Console\Commands;

use App\Services\ReportSchedulerService;
use Illuminate\Console\Command;

class GenerateScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate 
                          {--cleanup : Clean up old reports}
                          {--stats : Show report statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate scheduled reports (daily, weekly, monthly)';

    /**
     * Execute the console command.
     */
    public function handle(ReportSchedulerService $reportScheduler): int
    {
        $this->info('Starting scheduled report generation...');

        try {
            if ($this->option('cleanup')) {
                $this->info('Cleaning up old reports...');
                $cleanupResults = $reportScheduler->cleanupOldReports();
                $this->info("Cleanup completed: {$cleanupResults['deleted']} files deleted, {$cleanupResults['errors']} errors");
                return Command::SUCCESS;
            }

            if ($this->option('stats')) {
                $this->info('Retrieving report statistics...');
                $stats = $reportScheduler->getReportStatistics();
                
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Total Reports', $stats['total_reports'] ?? 0],
                        ['Total Size (MB)', $stats['total_size_mb'] ?? 0],
                        ['Reports This Month', $stats['reports_by_month'][now()->format('Y/m')] ?? 0],
                    ]
                );

                if (!empty($stats['reports_by_type'])) {
                    $this->info('Reports by Type:');
                    foreach ($stats['reports_by_type'] as $type => $count) {
                        $this->line("  - {$type}: {$count}");
                    }
                }

                return Command::SUCCESS;
            }

            // Generate scheduled reports
            $results = $reportScheduler->generateScheduledReports();

            $this->info('Report generation completed:');

            foreach ($results as $frequency => $reports) {
                if ($frequency === 'error') {
                    $this->error("Error: {$reports}");
                    continue;
                }

                $this->info("=== {$frequency} Reports ===");
                foreach ($reports as $type => $result) {
                    if ($result['success']) {
                        $this->info("✓ {$type}: Generated successfully");
                        $this->line("  CSV: {$result['csv_path']}");
                        $this->line("  PDF: {$result['pdf_path']}");
                        $this->line("  Recipients: " . count($result['sent_to']));
                    } else {
                        $this->error("✗ {$type}: {$result['error']}");
                    }
                }
            }

            $this->info('All scheduled reports processed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to generate reports: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
