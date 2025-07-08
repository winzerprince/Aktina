<?php

namespace App\Jobs;

use App\Services\MLService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshMLPredictions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MLService $mlService): void
    {
        Log::info('Starting ML predictions refresh job');

        try {
            // Clear the cache
            $mlService->clearCache();

            // Trigger new predictions to warm up the cache
            $mlService->getCustomerSegments();
            $mlService->getSalesForecast();

            Log::info('ML predictions refreshed successfully');
        } catch (\Exception $e) {
            Log::error('Error refreshing ML predictions: ' . $e->getMessage());
        }
    }
}
