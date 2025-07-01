<?php

use App\Models\User;
use App\Models\Vendor;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

describe('Java Server Integration', function () {
    beforeEach(function () {
        Storage::fake('private');
    });

    it('sends PDF to Java server for processing', function () {
        // Mock the HTTP response from Java server
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([
                'success' => true,
                'score' => 85,
                'message' => 'PDF processed successfully'
            ], 200)
        ]);

        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING,
            'pdf_path' => 'vendor-applications/test.pdf'
        ]);

        // Create a test PDF file
        Storage::disk('private')->put('vendor-applications/test.pdf', '%PDF-1.4 test content');

        $service = app(ApplicationService::class);
        $result = $service->processPdfWithJavaServer($application);

        expect($result)->toBeTrue();
        expect($application->fresh()->score)->toBe(85);
        expect($application->fresh()->status)->toBe(Application::STATUS_SCORED);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://localhost:8080/api/process-pdf' &&
                   $request->hasHeader('Content-Type', 'multipart/form-data');
        });
    });

    it('handles Java server errors gracefully', function () {
        // Mock a failed response from Java server
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([
                'success' => false,
                'error' => 'Invalid PDF format'
            ], 400)
        ]);

        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING,
            'pdf_path' => 'vendor-applications/test.pdf'
        ]);

        Storage::disk('private')->put('vendor-applications/test.pdf', 'invalid content');

        $service = app(ApplicationService::class);
        $result = $service->processPdfWithJavaServer($application);

        expect($result)->toBeFalse();
        expect($application->fresh()->status)->toBe(Application::STATUS_PENDING);
    });

    it('handles Java server timeout', function () {
        // Mock a timeout response
        Http::fake([
            'http://localhost:8080/api/process-pdf' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
            }
        ]);

        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING,
            'pdf_path' => 'vendor-applications/test.pdf'
        ]);

        Storage::disk('private')->put('vendor-applications/test.pdf', '%PDF-1.4 test content');

        $service = app(ApplicationService::class);
        $result = $service->processPdfWithJavaServer($application);

        expect($result)->toBeFalse();
        expect($application->fresh()->status)->toBe(Application::STATUS_PENDING);
    });

    it('processes callback from Java server', function () {
        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);

        // Simulate callback from Java server
        $response = $this->postJson('/api/pdf-processing-callback', [
            'application_id' => $application->id,
            'success' => true,
            'score' => 92,
            'processing_details' => [
                'financial_score' => 90,
                'quality_score' => 95,
                'compliance_score' => 90
            ]
        ]);

        $response->assertOk();

        $application->refresh();
        expect($application->score)->toBe(92);
        expect($application->status)->toBe(Application::STATUS_SCORED);
    });

    it('validates callback data', function () {
        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);

        // Send invalid callback data
        $response = $this->postJson('/api/pdf-processing-callback', [
            'application_id' => $application->id,
            'success' => true,
            'score' => 150, // Invalid score (over 100)
        ]);

        $response->assertStatus(422);
        expect($application->fresh()->score)->toBeNull();
    });

    it('handles callback for non-existent application', function () {
        $response = $this->postJson('/api/pdf-processing-callback', [
            'application_id' => 99999,
            'success' => true,
            'score' => 85
        ]);

        $response->assertStatus(404);
    });
});

describe('End-to-End Verification Process', function () {
    beforeEach(function () {
        Storage::fake('private');
    });

    it('completes full vendor verification workflow', function () {
        // Mock Java server responses
        Http::fake([
            'http://localhost:8080/api/process-pdf' => Http::response([
                'success' => true,
                'score' => 88,
                'message' => 'PDF processed successfully'
            ], 200)
        ]);

        // 1. Create unverified vendor
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        // 2. Submit application
        $file = UploadedFile::fake()->create('application.pdf', 1000, 'application/pdf');
        $service = app(ApplicationService::class);
        $application = $service->submitApplication($vendor, $file);

        expect($application->status)->toBe(Application::STATUS_PENDING);

        // 3. Java server processes PDF
        $service->processPdfWithJavaServer($application);
        expect($application->fresh()->status)->toBe(Application::STATUS_SCORED);
        expect($application->fresh()->score)->toBe(88);

        // 4. Admin schedules meeting
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $service->scheduleMeeting($application, now()->addDays(7));
        expect($application->fresh()->status)->toBe(Application::STATUS_MEETING_SCHEDULED);

        // 5. Admin completes meeting
        $service->completeMeeting($application, 'Good meeting, vendor shows promise');
        expect($application->fresh()->status)->toBe(Application::STATUS_MEETING_COMPLETED);

        // 6. Admin approves application
        $service->approveApplication($application);
        expect($application->fresh()->status)->toBe(Application::STATUS_APPROVED);
        expect($user->fresh()->is_verified)->toBeTrue();
    });

    it('handles rejected vendor application', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_SCORED,
            'score' => 45 // Low score
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $service = app(ApplicationService::class);
        $service->rejectApplication($application, 'Score too low for approval');

        expect($application->fresh()->status)->toBe(Application::STATUS_REJECTED);
        expect($user->fresh()->is_verified)->toBeFalse();
    });

    it('completes retailer verification workflow', function () {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);

        // Submit demographics
        $retailer->update([
            'business_name' => 'Test Retail Business',
            'business_type' => 'retail',
            'demographics_completed' => true
        ]);

        // Trigger verification
        $verificationService = app(\App\Interfaces\Services\VerificationServiceInterface::class);
        $verificationService->completeRetailerDemographics($user);

        expect($user->fresh()->is_verified)->toBeTrue();
    });
});
