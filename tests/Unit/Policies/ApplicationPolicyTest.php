<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Application;
use App\Models\Vendor;
use App\Policies\ApplicationPolicy;
use Tests\TestCase;

class ApplicationPolicyTest extends TestCase
{
    private ApplicationPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ApplicationPolicy();
    }

    public function test_admin_can_view_any_applications()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $result = $this->policy->viewAny($admin);

        $this->assertTrue($result);
    }

    public function test_non_admin_cannot_view_any_applications()
    {
        $vendor = User::factory()->create(['role' => 'vendor']);

        $result = $this->policy->viewAny($vendor);

        $this->assertFalse($result);
    }

    public function test_admin_can_view_any_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create();

        $result = $this->policy->view($admin, $application);

        $this->assertTrue($result);
    }

    public function test_user_can_view_own_application()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $result = $this->policy->view($user, $application);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_others_application()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        
        $otherUser = User::factory()->create(['role' => 'vendor']);
        $otherVendor = Vendor::factory()->create(['user_id' => $otherUser->id]);
        $application = Application::factory()->create(['vendor_id' => $otherVendor->id]);

        $result = $this->policy->view($user, $application);

        $this->assertFalse($result);
    }

    public function test_unverified_vendor_can_create_application()
    {
        $vendor = User::factory()->create(['role' => 'vendor', 'verified' => false]);

        $result = $this->policy->create($vendor);

        $this->assertTrue($result);
    }

    public function test_verified_vendor_cannot_create_application()
    {
        $vendor = User::factory()->create(['role' => 'vendor', 'verified' => true]);

        $result = $this->policy->create($vendor);

        $this->assertFalse($result);
    }

    public function test_non_vendor_cannot_create_application()
    {
        $retailer = User::factory()->create(['role' => 'retailer']);

        $result = $this->policy->create($retailer);

        $this->assertFalse($result);
    }

    public function test_admin_can_update_any_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create();

        $result = $this->policy->update($admin, $application);

        $this->assertTrue($result);
    }

    public function test_user_can_update_own_pending_application()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending'
        ]);

        $result = $this->policy->update($user, $application);

        $this->assertTrue($result);
    }

    public function test_user_can_update_own_scored_application()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'scored'
        ]);

        $result = $this->policy->update($user, $application);

        $this->assertTrue($result);
    }

    public function test_user_cannot_update_approved_application()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'approved'
        ]);

        $result = $this->policy->update($user, $application);

        $this->assertFalse($result);
    }

    public function test_only_admin_can_delete_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendorUser = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $vendorUser->id]);
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $this->assertTrue($this->policy->delete($admin, $application));
        $this->assertFalse($this->policy->delete($vendorUser, $application));
    }

    public function test_admin_can_approve_meeting_completed_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create(['status' => 'meeting_completed']);

        $result = $this->policy->approve($admin, $application);

        $this->assertTrue($result);
    }

    public function test_admin_can_approve_scored_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create(['status' => 'scored']);

        $result = $this->policy->approve($admin, $application);

        $this->assertTrue($result);
    }

    public function test_admin_cannot_approve_already_approved_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create(['status' => 'approved']);

        $result = $this->policy->approve($admin, $application);

        $this->assertFalse($result);
    }

    public function test_admin_can_schedule_meeting_for_scored_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create(['status' => 'scored']);

        $result = $this->policy->scheduleMeeting($admin, $application);

        $this->assertTrue($result);
    }

    public function test_admin_can_schedule_meeting_for_pending_application()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create(['status' => 'pending']);

        $result = $this->policy->scheduleMeeting($admin, $application);

        $this->assertTrue($result);
    }

    public function test_admin_can_download_any_pdf()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::factory()->create();

        $result = $this->policy->downloadPdf($admin, $application);

        $this->assertTrue($result);
    }

    public function test_user_can_download_own_pdf()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create(['vendor_id' => $vendor->id]);

        $result = $this->policy->downloadPdf($user, $application);

        $this->assertTrue($result);
    }

    public function test_user_cannot_download_others_pdf()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        
        $otherUser = User::factory()->create(['role' => 'vendor']);
        $otherVendor = Vendor::factory()->create(['user_id' => $otherUser->id]);
        $application = Application::factory()->create(['vendor_id' => $otherVendor->id]);

        $result = $this->policy->downloadPdf($user, $application);

        $this->assertFalse($result);
    }
}
