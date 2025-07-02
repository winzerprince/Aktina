<?php

namespace App\Livewire\Shared;

use App\Services\ReportGeneratorService;
use Livewire\Component;
use Illuminate\Support\Facades\Response;

class ReportDownload extends Component
{
    public $reportType = 'sales';
    public $format = 'csv';
    public $startDate;
    public $endDate;
    public $role = '';
    public $loading = false;

    public $availableReports = [
        'sales' => 'Sales Report',
        'inventory' => 'Inventory Report',
        'users' => 'Users Report',
        'orders' => 'Orders Report',
        'production' => 'Production Report',
        'comprehensive' => 'Comprehensive Report'
    ];

    public $availableRoles = [
        '' => 'All Roles',
        'admin' => 'Admin',
        'vendor' => 'Vendor',
        'retailer' => 'Retailer',
        'supplier' => 'Supplier',
        'production_manager' => 'Production Manager',
        'hr_manager' => 'HR Manager'
    ];

    protected $rules = [
        'reportType' => 'required|string',
        'format' => 'required|in:csv,pdf',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate'
    ];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function generateReport()
    {
        $this->validate();
        
        $this->loading = true;
        
        try {
            $filters = [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'role' => $this->role ?: null
            ];

            $reportService = app(ReportGeneratorService::class);

            if ($this->format === 'csv') {
                $this->downloadCSV($reportService, $filters);
            } else {
                $this->downloadPDF($reportService, $filters);
            }

            $this->dispatch('reportGenerated', [
                'type' => $this->reportType,
                'format' => $this->format,
                'success' => true
            ]);

        } catch (\Exception $e) {
            $this->dispatch('reportError', [
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function downloadCSV(ReportGeneratorService $reportService, array $filters)
    {
        $csvData = $reportService->generateCSVData($this->reportType, $filters);
        
        $csvContent = $this->arrayToCSV($csvData['headers'], $csvData['data']);
        
        return Response::streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $csvData['filename'], [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $csvData['filename']
        ]);
    }

    public function downloadPDF(ReportGeneratorService $reportService, array $filters)
    {
        $pdfData = $reportService->generatePDFData($this->reportType, $filters);
        
        // For now, we'll generate a simple text-based PDF content
        // In a real implementation, you'd use a library like DomPDF or TCPDF
        $pdfContent = $this->generateSimplePDFContent($pdfData);
        
        return Response::streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $pdfData['filename'], [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=' . $pdfData['filename']
        ]);
    }

    public function previewReport()
    {
        $this->validate();
        
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'role' => $this->role ?: null
        ];

        $reportService = app(ReportGeneratorService::class);
        $reportData = $reportService->generateAnalyticsReport($this->reportType, $filters);
        
        $this->dispatch('reportPreview', [
            'type' => $this->reportType,
            'data' => $reportData
        ]);
    }

    public function resetFilters()
    {
        $this->reportType = 'sales';
        $this->format = 'csv';
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->role = '';
    }

    private function arrayToCSV(array $headers, array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($output, $headers);
        
        // Add data rows
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
        // Simple text-based PDF content
        // In production, you'd use a proper PDF library
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

    public function render()
    {
        return view('livewire.shared.report-download');
    }
}
