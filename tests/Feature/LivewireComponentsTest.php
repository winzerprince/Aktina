<?php

use App\Livewire\Verification\VendorApplication;
use App\Livewire\Verification\RetailerDemographics;
use App\Livewire\Admin\ApplicationsTable;
use App\Livewire\Admin\VerificationDashboard;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Retailer;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

describe('VendorApplication Livewire Component', function () {
    beforeEach(function () {
        Storage::fake('private');
    });

    it('renders without errors for vendor users', function () {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(VendorApplication::class)
            ->assertStatus(200);
    });

    it('displays existing application status', function () {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);

        Livewire::actingAs($user)
            ->test(VendorApplication::class)
            ->assertSee('pending')
            ->assertSee($application->application_reference);
    });

    it('uploads PDF file successfully', function () {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('application.pdf', 1000, 'application/pdf');

        Livewire::actingAs($user)
            ->test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertDispatched('application-submitted');

        $this->assertDatabaseHas('applications', [
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);
    });

    it('validates file type', function () {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('document.txt', 1000, 'text/plain');

        Livewire::actingAs($user)
            ->test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['pdfFile']);
    });

    it('enforces file size limits', function () {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('large.pdf', 15000, 'application/pdf'); // 15MB

        Livewire::actingAs($user)
            ->test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['pdfFile']);
    });
});

describe('RetailerDemographics Livewire Component', function () {
    it('renders without errors for retailer users', function () {
        $user = User::factory()->create(['role' => 'retailer']);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(RetailerDemographics::class)
            ->assertStatus(200);
    });

    it('loads existing demographics data', function () {
        $user = User::factory()->create(['role' => 'retailer']);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'business_name' => 'Existing Business',
            'demographics_completed' => true
        ]);

        Livewire::actingAs($user)
            ->test(RetailerDemographics::class)
            ->assertSet('business_name', 'Existing Business')
            ->assertSet('isCompleted', true);
    });

    it('submits demographics successfully', function () {
        $user = User::factory()->create(['role' => 'retailer']);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);

        Livewire::actingAs($user)
            ->test(RetailerDemographics::class)
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
            ->set('primary_products', 'Electronics')
            ->set('target_market', 'Young professionals')
            ->set('business_address', [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
                'postal_code' => '12345',
                'country' => 'United States'
            ])
            ->call('submitDemographics')
            ->assertHasNoErrors()
            ->assertSet('isCompleted', true);

        expect($retailer->fresh()->demographics_completed)->toBeTrue();
    });

    it('validates required fields', function () {
        $user = User::factory()->create(['role' => 'retailer']);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(RetailerDemographics::class)
            ->set('business_name', '') // Empty required field
            ->call('submitDemographics')
            ->assertHasErrors(['business_name']);
    });

    it('validates data formats', function () {
        $user = User::factory()->create(['role' => 'retailer']);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(RetailerDemographics::class)
            ->set('business_name', 'Valid Business')
            ->set('business_type', 'retail')
            ->set('phone', 'invalid-phone')
            ->set('website', 'not-a-url')
            ->set('years_in_business', -5) // Invalid negative number
            ->call('submitDemographics')
            ->assertHasErrors(['phone', 'website', 'years_in_business']);
    });
});

describe('ApplicationsTable Livewire Component', function () {
    it('renders for admin users', function () {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(ApplicationsTable::class)
            ->assertStatus(200);
    });

    it('displays applications in table', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::factory()->create();
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'application_reference' => 'APP-TEST123'
        ]);

        Livewire::actingAs($admin)
            ->test(ApplicationsTable::class)
            ->assertSee('APP-TEST123');
    });

    it('filters applications by status', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::factory()->create();
        $pendingApp = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING,
            'application_reference' => 'APP-PENDING'
        ]);
        $approvedApp = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_APPROVED,
            'application_reference' => 'APP-APPROVED'
        ]);

        Livewire::actingAs($admin)
            ->test(ApplicationsTable::class)
            ->set('statusFilter', Application::STATUS_PENDING)
            ->assertSee('APP-PENDING')
            ->assertDontSee('APP-APPROVED');
    });
});

describe('VerificationDashboard Livewire Component', function () {
    it('renders for admin users', function () {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(VerificationDashboard::class)
            ->assertStatus(200);
    });

    it('displays verification statistics', function () {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create test data
        $vendor = Vendor::factory()->create();
        Application::factory()->count(3)->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING
        ]);
        Application::factory()->count(2)->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_APPROVED
        ]);

        Livewire::actingAs($admin)
            ->test(VerificationDashboard::class)
            ->assertSee('3') // Pending count
            ->assertSee('2'); // Approved count
    });
});
