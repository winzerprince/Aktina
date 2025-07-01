<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JavaServerIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    public function test_can_send_pdf_to_java_server_for_processing()
    {
        // Mock the HTTP request to Java server
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([
                'success' => true,
                'score' => 85,
                'details' => [
                    'businessScore' => 30,
                    'financialScore' => 25,
                    'qualityScore' => 30
                ],
                'reasoning' => 'Strong application with good business plan'
            ], 200)
        ]);

        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending',
            'pdf_path' => 'vendor-applications/test.pdf'
        ]);

        // Store a fake PDF file
        Storage::disk('private')->put('vendor-applications/test.pdf', 'fake pdf content');

        // Simulate the service calling the Java server
        $response = Http::timeout(30)->post('http://localhost:8080/api/process-pdf', [
            'applicationId' => $application->id,
            'filePath' => $application->pdf_path,
            'callbackUrl' => route('api.application.callback')
        ]);

        $this->assertTrue($response->successful());
        $this->assertEquals(85, $response->json('score'));

        // Verify the request was made with correct data
        Http::assertSent(function ($request) use ($application) {
            return $request->url() == 'http://localhost:8080/api/process-pdf' &&
                   $request['applicationId'] == $application->id &&
                   $request['filePath'] == $application->pdf_path;
        });
    }

    public function test_handles_java_server_timeout()
    {
        // Mock timeout response
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([], 408) // Timeout
        ]);

        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);

        $response = Http::timeout(5)->post('http://localhost:8080/api/process-pdf', [
            'applicationId' => $application->id,
            'filePath' => $application->pdf_path,
            'callbackUrl' => route('api.application.callback')
        ]);

        $this->assertFalse($response->successful());
        $this->assertEquals(408, $response->status());
    }

    public function test_handles_java_server_error_response()
    {
        // Mock error response
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([
                'success' => false,
                'error' => 'Failed to process PDF: Invalid file format'
            ], 400)
        ]);

        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);

        $response = Http::post('http://localhost:8080/api/process-pdf', [
            'applicationId' => $application->id,
            'filePath' => $application->pdf_path,
            'callbackUrl' => route('api.application.callback')
        ]);

        $this->assertFalse($response->successful());
        $this->assertFalse($response->json('success'));
        $this->assertStringContains('Invalid file format', $response->json('error'));
    }

    public function test_processes_java_server_callback()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);

        // Simulate callback from Java server
        $callbackData = [
            'applicationId' => $application->id,
            'success' => true,
            'score' => 87,
            'details' => [
                'businessScore' => 32,
                'financialScore' => 28,
                'qualityScore' => 27
            ],
            'reasoning' => 'Excellent application with comprehensive business plan'
        ];

        $response = $this->postJson('/api/application/callback', $callbackData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify application was updated
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'scored',
            'score' => 87
        ]);
    }

    public function test_handles_invalid_callback_data()
    {
        $response = $this->postJson('/api/application/callback', [
            'applicationId' => 999, // Non-existent application
            'success' => true,
            'score' => 85
        ]);

        $response->assertStatus(404);
        $response->assertJson(['success' => false, 'error' => 'Application not found']);
    }

    public function test_validates_callback_authentication()
    {
        // Test without proper authentication token
        $response = $this->postJson('/api/application/callback', [
            'applicationId' => 1,
            'success' => true,
            'score' => 85
        ]);

        $response->assertStatus(401);
    }

    public function test_end_to_end_pdf_processing_workflow()
    {
        // Mock successful Java server response
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([
                'success' => true,
                'message' => 'PDF processing started'
            ], 202) // Accepted for processing
        ]);

        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        // Store a fake PDF file
        $pdfContent = '%PDF-1.4 fake pdf content for testing';
        Storage::disk('private')->put('vendor-applications/test.pdf', $pdfContent);

        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending',
            'pdf_path' => 'vendor-applications/test.pdf'
        ]);

        // 1. Send to Java server for processing
        $response = Http::post('http://localhost:8080/api/process-pdf', [
            'applicationId' => $application->id,
            'filePath' => $application->pdf_path,
            'callbackUrl' => url('/api/application/callback')
        ]);

        $this->assertTrue($response->successful());

        // 2. Simulate Java server callback with results
        $callbackData = [
            'applicationId' => $application->id,
            'success' => true,
            'score' => 92,
            'details' => [
                'businessScore' => 34,
                'financialScore' => 30,
                'qualityScore' => 28
            ],
            'reasoning' => 'Outstanding application with excellent financials'
        ];

        $callbackResponse = $this->postJson('/api/application/callback', $callbackData);
        $callbackResponse->assertStatus(200);

        // 3. Verify final state
        $application->refresh();
        $this->assertEquals('scored', $application->status);
        $this->assertEquals(92, $application->score);

        // 4. Verify notifications were sent
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'App\Notifications\VendorApplicationScored'
        ]);
    }

    public function test_java_server_connection_failure()
    {
        // Mock connection failure
        Http::fake([
            'http://localhost:8080/api/process-pdf' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
            }
        ]);

        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);

        try {
            Http::timeout(5)->post('http://localhost:8080/api/process-pdf', [
                'applicationId' => $application->id,
                'filePath' => $application->pdf_path,
                'callbackUrl' => route('api.application.callback')
            ]);
            $this->fail('Expected ConnectionException was not thrown');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->assertStringContains('Connection refused', $e->getMessage());
        }
    }

    public function test_pdf_processing_with_large_file()
    {
        // Mock response for large file processing
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([
                'success' => true,
                'message' => 'Large PDF queued for processing'
            ], 202)
        ]);

        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        // Create a larger fake PDF
        $largePdfContent = '%PDF-1.4' . str_repeat("\nPage content here", 1000);
        Storage::disk('private')->put('vendor-applications/large.pdf', $largePdfContent);

        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending',
            'pdf_path' => 'vendor-applications/large.pdf'
        ]);

        $response = Http::timeout(60)->post('http://localhost:8080/api/process-pdf', [
            'applicationId' => $application->id,
            'filePath' => $application->pdf_path,
            'callbackUrl' => route('api.application.callback')
        ]);

        $this->assertTrue($response->successful());
        $this->assertStringContains('queued for processing', $response->json('message'));
    }
}
