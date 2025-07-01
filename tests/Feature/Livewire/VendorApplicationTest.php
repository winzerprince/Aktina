<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Verification\VendorApplication;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class VendorApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    public function test_renders_successfully()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(VendorApplication::class)
            ->assertStatus(200)
            ->assertSee('Upload Application PDF');
    }

    public function test_can_upload_valid_pdf()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');

        Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertSet('isUploading', false)
            ->assertSee('Application submitted successfully');

        $this->assertDatabaseHas('applications', [
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);
    }

    public function test_rejects_invalid_file_type()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('application.txt', 1024, 'text/plain');

        Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['pdfFile'])
            ->assertSee('Only PDF files are allowed');
    }

    public function test_rejects_oversized_file()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('application.pdf', 11264, 'application/pdf'); // 11MB

        Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['pdfFile'])
            ->assertSee('must not exceed 10MB');
    }

    public function test_prevents_unauthorized_submission()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => true]); // Already verified
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');

        Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['authorization'])
            ->assertSee('not authorized to submit applications');
    }

    public function test_prevents_non_vendor_submission()
    {
        $user = User::factory()->create(['role' => 'retailer']); // Wrong role

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');

        Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['authorization']);
    }

    public function test_displays_existing_application_status()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'scored',
            'score' => 85
        ]);

        $this->actingAs($user);

        Livewire::test(VendorApplication::class)
            ->assertSee('scored')
            ->assertSee('85')
            ->assertSee('Your application has been processed');
    }

    public function test_displays_meeting_information()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $meetingDate = now()->addDays(7);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'meeting_scheduled',
            'meeting_date' => $meetingDate
        ]);

        $this->actingAs($user);

        Livewire::test(VendorApplication::class)
            ->assertSee('meeting_scheduled')
            ->assertSee($meetingDate->format('M j, Y'));
    }

    public function test_shows_upload_form_when_no_application()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(VendorApplication::class)
            ->assertSee('Upload Application PDF')
            ->assertSee('Select PDF file');
    }

    public function test_handles_upload_errors_gracefully()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Mock file validation service to throw exception
        $this->app->instance(
            \App\Services\FileValidationService::class,
            \Mockery::mock(\App\Services\FileValidationService::class, function ($mock) {
                $mock->shouldReceive('validatePdfFile')->andReturn(['Validation failed']);
            })
        );

        $file = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');

        Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->call('submitApplication')
            ->assertHasErrors(['upload'])
            ->assertSee('Failed to submit application');
    }

    public function test_upload_progress_tracking()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');

        $component = Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file);

        // Check that isUploading is set during upload
        $component->call('submitApplication');

        // After completion, isUploading should be false
        $component->assertSet('isUploading', false);
    }

    public function test_prevents_double_submission()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');

        $component = Livewire::test(VendorApplication::class)
            ->set('pdfFile', $file)
            ->set('isUploading', true); // Simulate upload in progress

        $component->call('submitApplication');

        // Should not process if already uploading
        $component->assertSet('isUploading', true);
    }
}
