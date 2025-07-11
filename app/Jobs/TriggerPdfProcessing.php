<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TriggerPdfProcessing implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $applicationId
    ) {}

    public function handle(): void
    {
        try {
            $application = Application::findOrFail($this->applicationId);

            if (!$application->pdf_path) {
                throw new \Exception("No PDF file found for application {$this->applicationId}");
            }

            // Get the PDF file path
            $pdfPath = Storage::path($application->pdf_path);

            if (!file_exists($pdfPath)) {
                throw new \Exception("PDF file not found at path: {$pdfPath}");
            }

            // Prepare the request to Java microservice
            $javaServerUrl = config('services.java_server.url', 'http://localhost:8081');
            $response = Http::attach(
                'file', file_get_contents($pdfPath), 'application.pdf'
            )->post("{$javaServerUrl}/api/process-pdf", [
                'applicationId' => $this->applicationId,
                'vendorId' => $application->vendor_id,
                'callbackUrl' => route('api.callbacks.pdf-processing', ['applicationId' => $this->applicationId])
            ]);

            if ($response->successful()) {
                $application->processed_by_java_server = true;
                $application->processing_date = now();
                $application->save();

                Log::info("PDF processing triggered successfully", [
                    'application_id' => $this->applicationId,
                    'java_server_response' => $response->json()
                ]);
            } else {
                throw new \Exception("Java server responded with error: " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("Failed to trigger PDF processing", [
                'application_id' => $this->applicationId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
