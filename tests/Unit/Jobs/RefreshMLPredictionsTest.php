<?php

namespace Tests\Unit\Jobs;

use App\Jobs\RefreshMLPredictions;
use App\Services\MLService;
use Exception;
use Mockery;
use Tests\TestCase;

class RefreshMLPredictionsTest extends TestCase
{
    /**
     * Test that the job clears cache and fetches new predictions
     */
    public function test_refresh_ml_predictions_job_clears_cache_and_fetches_new_predictions(): void
    {
        // Mock MLService
        $mlService = Mockery::mock(MLService::class);

        // Set expectations
        $mlService->shouldReceive('clearCache')->once();
        $mlService->shouldReceive('getCustomerSegments')->once();
        $mlService->shouldReceive('getSalesForecast')->once();

        // Run job with mocked service
        (new RefreshMLPredictions())->handle($mlService);
    }

    /**
     * Test that the job handles exceptions gracefully
     */
    public function test_refresh_ml_predictions_job_handles_exceptions_gracefully(): void
    {
        // Mock MLService
        $mlService = Mockery::mock(MLService::class);

        // Set expectations - clearCache works but other methods throw exceptions
        $mlService->shouldReceive('clearCache')->once();
        $mlService->shouldReceive('getCustomerSegments')
            ->once()
            ->andThrow(new Exception('API Connection Error'));

        // Binding won't call getSalesForecast if getCustomerSegments fails
        $mlService->shouldReceive('getSalesForecast')->never();

        // Run job - should not throw exception
        (new RefreshMLPredictions())->handle($mlService);

        // If we reach here, the test passes (no exception was thrown)
        $this->assertTrue(true);
    }
}
