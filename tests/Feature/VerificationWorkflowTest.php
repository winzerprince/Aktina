<?php

use App\Models\User;
use App\Models\Vendor;
use App\Models\Retailer;
use App\Models\Application;
use App\Notifications\VendorApplicationReceived;
use App\Notifications\UserVerificationComplete;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

describe('Vendor Verification Workflow', function () {
    beforeEach(function () {
        Storage::fake('private');
        Notification::fake();
    });

    it('redirects unverified vendors to verification page', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/verification/vendor');
    });

    it('allows vendor to submit PDF application', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/verification/vendor');
        $response->assertOk();

        // Test Livewire component for PDF upload
        $file = UploadedFile::fake()->create('application.pdf', 1000, 'application/pdf');

        Volt::test('verification.vendor-application')
            ->actingAs($user)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasNoErrors();

        // Check application was created
        $this->assertDatabaseHas('applications', [
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);

        // Check notification was sent
        Notification::assertSentTo($user, VendorApplicationReceived::class);
    });

    it('prevents file upload with invalid file types', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('document.txt', 1000, 'text/plain');

        Volt::test('verification.vendor-application')
            ->actingAs($user)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['pdfFile']);
    });

    it('enforces file size limits', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('large.pdf', 15000, 'application/pdf'); // 15MB

        Volt::test('verification.vendor-application')
            ->actingAs($user)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['pdfFile']);
    });

    it('shows application status after submission', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);

        $response = $this->actingAs($user)->get('/verification/vendor');

        $response->assertOk();
        $response->assertSee('pending');
        $response->assertSee($application->application_reference);
    });

    it('marks vendor as verified when application is approved', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_MEETING_COMPLETED
        ]);

        // Simulate admin approval
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $application->update(['status' => Application::STATUS_APPROVED]);

        // Trigger verification
        app(\App\Interfaces\Services\VerificationServiceInterface::class)->markAsVerified($user);

        expect($user->fresh()->is_verified)->toBeTrue();
        Notification::assertSentTo($user, UserVerificationComplete::class);
    });
});

describe('Retailer Verification Workflow', function () {
    beforeEach(function () {
        Notification::fake();
    });

    it('redirects unverified retailers to verification page', function () {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/verification/retailer');
    });

    it('allows retailer to submit demographics form', function () {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);

        $response = $this->actingAs($user)->get('/verification/retailer');
        $response->assertOk();

        // Test Livewire component for demographics
        Volt::test('verification.retailer-demographics')
            ->actingAs($user)
            ->set('business_name', 'Test Business')
            ->set('business_type', 'retail')
            ->set('business_registration_number', 'REG123456')
            ->set('tax_id', 'TAX123456')
            ->set('contact_person', 'John Doe')
            ->set('phone', '+1-555-123-4567')
            ->set('website', 'https://example.com')
            ->set('annual_revenue', '100k_500k')
            ->set('employee_count', '6_10')
            ->set('years_in_business', 5)
            ->set('primary_products', 'Electronics and gadgets')
            ->set('target_market', 'Young professionals')
            ->set('business_address', [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
                'postal_code' => '12345',
                'country' => 'United States'
            ])
            ->call('submitDemographics')
            ->assertHasNoErrors();

        // Check retailer was updated
        expect($retailer->fresh()->demographics_completed)->toBeTrue();
        expect($user->fresh()->is_verified)->toBeTrue();
        Notification::assertSentTo($user, UserVerificationComplete::class);
    });

    it('validates required fields in demographics form', function () {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);

        Volt::test('verification.retailer-demographics')
            ->actingAs($user)
            ->set('business_name', '') // Missing required field
            ->call('submitDemographics')
            ->assertHasErrors(['business_name']);
    });

    it('validates business data format', function () {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);

        Volt::test('verification.retailer-demographics')
            ->actingAs($user)
            ->set('business_name', 'Valid Business')
            ->set('phone', 'invalid-phone-format')
            ->set('website', 'not-a-url')
            ->call('submitDemographics')
            ->assertHasErrors(['phone', 'website']);
    });
});

describe('Admin Verification Management', function () {
    beforeEach(function () {
        Notification::fake();
    });

    it('allows admin to view applications list', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->actingAs($admin)->get('/admin/applications');

        $response->assertOk();
        $response->assertSee($application->application_reference);
    });

    it('allows admin to view individual application', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->actingAs($admin)->get("/admin/applications/{$application->id}");

        $response->assertOk();
        $response->assertSee($application->application_reference);
    });

    it('allows admin to approve applications', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_MEETING_COMPLETED
        ]);

        $response = $this->actingAs($admin)->post("/admin/applications/{$application->id}/approve");

        $response->assertRedirect();
        expect($application->fresh()->status)->toBe(Application::STATUS_APPROVED);
    });

    it('allows admin to reject applications', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);

        $response = $this->actingAs($admin)->post("/admin/applications/{$application->id}/reject", [
            'rejection_reason' => 'Insufficient documentation'
        ]);

        $response->assertRedirect();
        expect($application->fresh()->status)->toBe(Application::STATUS_REJECTED);
    });

    it('prevents non-admin from accessing admin routes', function () {
        $vendor = User::factory()->create(['role' => 'vendor']);

        $response = $this->actingAs($vendor)->get('/admin/applications');

        $response->assertStatus(403);
    });
});
