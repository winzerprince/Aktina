<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\VerificationService;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Retailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class VerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VerificationService $verificationService;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake notifications to avoid database issues
        Notification::fake();

        $this->verificationService = app(VerificationService::class);
    }

    public function test_checks_if_user_is_fully_verified()
    {
        $user = User::factory()->create(['verified' => true, 'role' => 'admin']);

        $result = $this->verificationService->isUserFullyVerified($user);

        $this->assertTrue($result);
    }

    public function test_checks_unverified_user()
    {
        $user = User::factory()->create(['verified' => false, 'role' => 'vendor']);

        $result = $this->verificationService->isUserFullyVerified($user);

        $this->assertFalse($result);
    }

    public function test_gets_verification_requirements_for_vendor()
    {
        $requirements = $this->verificationService->getVerificationRequirements('vendor');

        $this->assertArrayHasKey('email_verification', $requirements);
        $this->assertArrayHasKey('application_submission', $requirements);
        $this->assertArrayHasKey('application_approval', $requirements);
    }

    public function test_gets_verification_requirements_for_retailer()
    {
        $requirements = $this->verificationService->getVerificationRequirements('retailer');

        $this->assertArrayHasKey('email_verification', $requirements);
        $this->assertArrayHasKey('demographics_completion', $requirements);
    }

    public function test_marks_vendor_as_verified()
    {
        $user = User::factory()->create(['role' => 'vendor', 'verified' => false, 'email_verified_at' => null]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $this->verificationService->markAsVerified($user);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_marks_retailer_as_verified()
    {
        $user = User::factory()->create(['role' => 'retailer', 'verified' => false, 'email_verified_at' => null]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id]);

        $this->verificationService->markAsVerified($user);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_completes_retailer_demographics()
    {
        $user = User::factory()->create(['role' => 'retailer', 'verified' => false, 'email_verified_at' => now()]);
        $retailer = Retailer::factory()->create(['user_id' => $user->id, 'demographics_completed' => true]);

        // Since retailer has completed demographics and email is verified, they should be fully verified
        $result = $this->verificationService->completeRetailerDemographics($user);

        $this->assertTrue($result);
    }
}
