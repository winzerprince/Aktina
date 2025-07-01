<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    public function test_admin_can_view_applications_index()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/applications');

        $response->assertStatus(200);
        $response->assertSee('Applications');
    }

    public function test_non_admin_cannot_access_admin_applications()
    {
        $vendor = User::factory()->create(['role' => 'vendor']);

        $response = $this->actingAs($vendor)->get('/admin/applications');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_individual_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->actingAs($admin)->get("/admin/applications/{$application->id}");

        $response->assertStatus(200);
        $response->assertSee($application->application_reference);
    }

    public function test_admin_can_schedule_meeting()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'scored'
        ]);

        $meetingDate = now()->addDays(7)->format('Y-m-d H:i');

        $response = $this->actingAs($admin)
            ->post("/admin/applications/{$application->id}/schedule", [
                'meeting_date' => $meetingDate
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'meeting_scheduled'
        ]);
    }

    public function test_admin_cannot_schedule_meeting_in_past()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $pastDate = now()->subDays(1)->format('Y-m-d H:i');

        $response = $this->actingAs($admin)
            ->post("/admin/applications/{$application->id}/schedule", [
                'meeting_date' => $pastDate
            ]);

        $response->assertSessionHasErrors(['meeting_date']);
    }

    public function test_admin_can_complete_meeting()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'meeting_scheduled'
        ]);

        $meetingNotes = 'Great candidate, very promising application.';

        $response = $this->actingAs($admin)
            ->post("/admin/applications/{$application->id}/complete-meeting", [
                'meeting_notes' => $meetingNotes
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'meeting_completed',
            'meeting_notes' => $meetingNotes
        ]);
    }

    public function test_admin_cannot_complete_meeting_without_notes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'meeting_scheduled'
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/applications/{$application->id}/complete-meeting", [
                'meeting_notes' => ''
            ]);

        $response->assertSessionHasErrors(['meeting_notes']);
    }

    public function test_admin_can_approve_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'meeting_completed'
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/applications/{$application->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'approved'
        ]);

        // User should become verified
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_verified' => true
        ]);
    }

    public function test_admin_can_reject_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'scored'
        ]);

        $rejectionReason = 'Does not meet our current requirements.';

        $response = $this->actingAs($admin)
            ->post("/admin/applications/{$application->id}/reject", [
                'rejection_reason' => $rejectionReason
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason
        ]);
    }

    public function test_admin_cannot_reject_without_reason()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->actingAs($admin)
            ->post("/admin/applications/{$application->id}/reject", [
                'rejection_reason' => ''
            ]);

        $response->assertSessionHasErrors(['rejection_reason']);
    }

    public function test_admin_can_download_application_pdf()
    {
        Storage::disk('private')->put('test-application.pdf', 'fake pdf content');

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'pdf_path' => 'test-application.pdf'
        ]);

        $response = $this->actingAs($admin)
            ->get("/admin/applications/{$application->id}/download-pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=application_' . $application->id . '.pdf');
    }

    public function test_admin_cannot_download_missing_pdf()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'pdf_path' => 'non-existent.pdf'
        ]);

        $response = $this->actingAs($admin)
            ->get("/admin/applications/{$application->id}/download-pdf");

        $response->assertStatus(404);
    }

    public function test_vendor_can_download_own_pdf()
    {
        Storage::disk('private')->put('test-application.pdf', 'fake pdf content');

        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'pdf_path' => 'test-application.pdf'
        ]);

        $response = $this->actingAs($user)
            ->get("/admin/applications/{$application->id}/download-pdf");

        $response->assertStatus(200);
    }

    public function test_vendor_cannot_download_others_pdf()
    {
        Storage::disk('private')->put('test-application.pdf', 'fake pdf content');

        $user1 = User::factory()->create(['role' => 'vendor']);
        $user2 = User::factory()->create(['role' => 'vendor']);
        $vendor2 = Vendor::factory()->create(['user_id' => $user2->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor2->id,
            'pdf_path' => 'test-application.pdf'
        ]);

        $response = $this->actingAs($user1)
            ->get("/admin/applications/{$application->id}/download-pdf");

        $response->assertStatus(403);
    }

    public function test_admin_rate_limiting_on_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        // Create multiple applications and try to process them rapidly
        for ($i = 0; $i < 35; $i++) {
            $application = Application::factory()->create(['vendor_id' => $vendor->id]);

            $response = $this->actingAs($admin)
                ->post("/admin/applications/{$application->id}/approve");

            if ($i >= 30) {
                // Should hit rate limit after 30 actions per minute
                $response->assertStatus(429);
                break;
            }
        }
    }
}
