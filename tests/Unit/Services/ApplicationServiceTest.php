<?php

namespace Tests\Unit\Services;

use App\Services\ApplicationService;
use App\Services\FileValidationService;
use App\Interfaces\Repositories\ApplicationRepositoryInterface;
use App\Models\Application;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\VendorApplicationReceived;
use App\Notifications\AdminNewApplicationSubmitted;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class ApplicationServiceTest extends TestCase
{
    private ApplicationService $service;
    private $mockRepository;
    private $mockFileValidationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(ApplicationRepositoryInterface::class);
        $this->mockFileValidationService = Mockery::mock(FileValidationService::class);

        $this->service = new ApplicationService(
            $this->mockRepository,
            $this->mockFileValidationService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_submits_application_successfully()
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        // Mock file validation
        $this->mockFileValidationService
            ->shouldReceive('validatePdfFile')
            ->once()
            ->with($file)
            ->andReturn([]);

        $this->mockFileValidationService
            ->shouldReceive('generateSecureFilename')
            ->once()
            ->with('vendor_application', $user->id)
            ->andReturn('vendor_application_123_456.pdf');

        $this->mockFileValidationService
            ->shouldReceive('storeFileSecurely')
            ->once()
            ->with($file, 'vendor-applications', 'vendor_application_123_456.pdf')
            ->andReturn('vendor-applications/vendor_application_123_456.pdf');

        // Mock repository
        $application = new Application([
            'id' => 1,
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_PENDING,
            'pdf_path' => 'vendor-applications/vendor_application_123_456.pdf',
            'application_reference' => 'APP-ABC123'
        ]);

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($application);

        $result = $this->service->submitApplication($vendor, $file);

        $this->assertInstanceOf(Application::class, $result);
        $this->assertEquals(Application::STATUS_PENDING, $result->status);

        // Verify notifications were sent
        Notification::assertSentTo($user, VendorApplicationReceived::class);
    }

    public function test_submits_application_fails_with_invalid_file()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        // Mock file validation to return errors
        $this->mockFileValidationService
            ->shouldReceive('validatePdfFile')
            ->once()
            ->with($file)
            ->andReturn(['File validation failed']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File validation failed: File validation failed');

        $this->service->submitApplication($vendor, $file);
    }

    public function test_submits_application_fails_with_storage_error()
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        // Mock file validation to pass
        $this->mockFileValidationService
            ->shouldReceive('validatePdfFile')
            ->once()
            ->andReturn([]);

        $this->mockFileValidationService
            ->shouldReceive('generateSecureFilename')
            ->once()
            ->andReturn('vendor_application_123_456.pdf');

        // Mock storage to fail
        $this->mockFileValidationService
            ->shouldReceive('storeFileSecurely')
            ->once()
            ->andReturn(false);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to store the uploaded file securely.');

        $this->service->submitApplication($vendor, $file);
    }

    public function test_schedules_meeting_successfully()
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_SCORED
        ]);
        $meetingDate = now()->addDays(7)->format('Y-m-d H:i:s');

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($application, [
                'status' => Application::STATUS_MEETING_SCHEDULED,
                'meeting_schedule' => $meetingDate
            ])
            ->andReturnUsing(function() use ($application, $meetingDate) {
                $application->meeting_schedule = $meetingDate;
                $application->status = Application::STATUS_MEETING_SCHEDULED;
                return true;
            });

        $result = $this->service->scheduleMeeting($application, $meetingDate);

        $this->assertTrue($result);
    }

    public function test_completes_meeting_successfully()
    {
        $application = Application::factory()->create(['status' => Application::STATUS_MEETING_SCHEDULED]);
        $notes = 'Meeting went well. Good candidate.';

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($application, [
                'status' => Application::STATUS_MEETING_COMPLETED,
                'meeting_notes' => $notes
            ])
            ->andReturn(true);

        $result = $this->service->completeMeeting($application, $notes);

        $this->assertTrue($result);
    }

    public function test_approves_application_successfully()
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_MEETING_COMPLETED
        ]);

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($application, ['status' => Application::STATUS_APPROVED])
            ->andReturn(true);

        $result = $this->service->approveApplication($application);

        $this->assertTrue($result);
    }

    public function test_rejects_application_successfully()
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => Application::STATUS_SCORED
        ]);

        $rejectionReason = 'Does not meet requirements.';

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($application, [
                'status' => Application::STATUS_REJECTED,
                'meeting_notes' => 'Rejection reason: ' . $rejectionReason
            ])
            ->andReturn(true);

        $result = $this->service->rejectApplication($application, $rejectionReason);

        $this->assertTrue($result);
    }
}
