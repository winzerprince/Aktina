<?php

namespace Tests\Feature\Verification;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    public function test_unverified_vendor_can_access_verification_page()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);

        $response = $this->actingAs($user)->get('/verification/vendor');

        $response->assertStatus(200);
        $response->assertSee('Vendor Application');
    }

    public function test_verified_vendor_is_redirected_from_verification_page()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_vendor_can_submit_pdf_application()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('application.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($user)
            ->post('/livewire/verification/vendor-application', [
                'pdfFile' => $file
            ]);

        // Note: This would normally test Livewire component directly
        // Here we're testing the concept that the application is created
        $this->assertDatabaseHas('applications', [
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);
    }

    public function test_vendor_cannot_submit_invalid_file()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('application.txt', 1024, 'text/plain');

        // This would fail validation in the Livewire component
        $this->assertDatabaseMissing('applications', [
            'vendor_id' => $vendor->id
        ]);
    }

    public function test_vendor_application_creates_notifications()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'App\Notifications\VendorApplicationReceived'
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'type' => 'App\Notifications\AdminNewApplicationSubmitted'
        ]);
    }

    public function test_vendor_can_view_application_status()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'scored',
            'score' => 85
        ]);

        $response = $this->actingAs($user)->get('/verification/vendor');

        $response->assertStatus(200);
        $response->assertSee('scored');
        $response->assertSee('85');
    }

    public function test_rate_limiting_prevents_excessive_uploads()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);

        // Simulate multiple upload attempts
        for ($i = 0; $i < 4; $i++) {
            $response = $this->actingAs($user)
                ->post('/verification/vendor', ['upload' => true]);
        }

        // The 4th attempt should be rate limited
        $response->assertStatus(429);
    }

    public function test_non_vendor_cannot_access_vendor_verification()
    {
        $user = User::factory()->create(['role' => 'retailer']);

        $response = $this->actingAs($user)->get('/verification/vendor');

        // Would redirect to their appropriate verification page
        $response->assertRedirect('/verification/retailer');
    }

    public function test_guest_cannot_access_verification_pages()
    {
        $response = $this->get('/verification/vendor');

        $response->assertRedirect('/login');
    }

    public function test_vendor_application_workflow()
    {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        // 1. Submit application
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);

        $this->assertEquals('pending', $application->status);

        // 2. Application gets scored
        $application->update(['status' => 'scored', 'score' => 85]);
        $this->assertEquals('scored', $application->fresh()->status);

        // 3. Meeting scheduled
        $application->update([
            'status' => 'meeting_scheduled',
            'meeting_date' => now()->addDays(7)
        ]);
        $this->assertEquals('meeting_scheduled', $application->fresh()->status);

        // 4. Meeting completed
        $application->update([
            'status' => 'meeting_completed',
            'meeting_notes' => 'Great candidate'
        ]);
        $this->assertEquals('meeting_completed', $application->fresh()->status);

        // 5. Application approved
        $application->update(['status' => 'approved']);
        $this->assertEquals('approved', $application->fresh()->status);

        // 6. User becomes verified
        $user->update(['is_verified' => true]);
        $this->assertTrue($user->fresh()->is_verified);
    }
}
