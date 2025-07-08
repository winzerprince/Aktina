<?php

use App\Livewire\SalesPrediction;
use App\Services\MLService;
use Livewire\Livewire;

test('SalesPrediction component loads forecast data on mount', function () {
    // Mock MLService
    $mlService = mock(MLService::class);

    // Create test forecast data
    $forecastData = [
        'dates' => ['2025-07-08', '2025-07-09'],
        'values' => [1000, 1200],
        'lower' => [900, 1000],
        'upper' => [1100, 1300]
    ];

    // Set expectations
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnTrue();
    $mlService->shouldReceive('getSalesForecastChartData')->with(90)->once()->andReturn($forecastData);

    // Bind mock to container
    $this->instance(MLService::class, $mlService);

    // Test component
    Livewire::test(SalesPrediction::class)
        ->assertSet('isLoading', false)
        ->assertSet('serviceError', false)
        ->assertSet('forecastData', $forecastData);
});

test('SalesPrediction component handles service errors gracefully', function () {
    // Mock MLService
    $mlService = mock(MLService::class);

    // Set expectations - service is unhealthy
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnFalse();
    $mlService->shouldReceive('getSalesForecastChartData')->never();

    // Bind mock to container
    $this->instance(MLService::class, $mlService);

    // Test component
    Livewire::test(SalesPrediction::class)
        ->assertSet('isLoading', false)
        ->assertSet('serviceError', true);
});

test('SalesPrediction component refreshes data when loadForecastData is called', function () {
    // Mock MLService
    $mlService = mock(MLService::class);

    // Create test forecast data
    $forecastData = [
        'dates' => ['2025-07-08', '2025-07-09'],
        'values' => [1000, 1200],
        'lower' => [900, 1000],
        'upper' => [1100, 1300]
    ];

    // Set expectations for initial load
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnTrue();
    $mlService->shouldReceive('getSalesForecastChartData')->with(90)->once()->andReturn($forecastData);

    // Set expectations for refresh
    $updatedForecastData = [
        'dates' => ['2025-07-08', '2025-07-09', '2025-07-10'],
        'values' => [1000, 1200, 1400],
        'lower' => [900, 1000, 1200],
        'upper' => [1100, 1300, 1600]
    ];
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnTrue();
    $mlService->shouldReceive('getSalesForecastChartData')->with(90)->once()->andReturn($updatedForecastData);

    // Bind mock to container
    $this->instance(MLService::class, $mlService);

    // Test component
    Livewire::test(SalesPrediction::class)
        ->assertSet('forecastData', $forecastData)
        ->call('loadForecastData', $mlService)
        ->assertSet('forecastData', $updatedForecastData);
});

test('SalesPrediction component updates forecast data when horizon changes', function () {
    // Mock MLService
    $mlService = mock(MLService::class);

    // Create test forecast data for default horizon (90)
    $forecastData90 = [
        'dates' => ['2025-07-08', '2025-07-09'],
        'values' => [1000, 1200],
        'lower' => [900, 1000],
        'upper' => [1100, 1300]
    ];

    // Create test forecast data for new horizon (30)
    $forecastData30 = [
        'dates' => ['2025-07-08'],
        'values' => [1000],
        'lower' => [900],
        'upper' => [1100]
    ];

    // Set expectations - only need isServiceHealthy once since we mock the method calls
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnTrue();
    $mlService->shouldReceive('getSalesForecastChartData')->with(90)->once()->andReturn($forecastData90);
    $mlService->shouldReceive('getSalesForecastChartData')->with(30)->once()->andReturn($forecastData30);

    // Bind mock to container and allow re-binding
    $this->partialMock(MLService::class, function ($mock) use ($mlService) {
        $mock->shouldReceive('isServiceHealthy')->andReturn($mlService->isServiceHealthy());
        $mock->shouldReceive('getSalesForecastChartData')->andReturnUsing(function ($horizon) use ($mlService) {
            return $mlService->getSalesForecastChartData($horizon);
        });
    });

    // Test component
    Livewire::test(SalesPrediction::class)
        ->assertSet('forecastData', $forecastData90)
        ->set('horizon', 30)
        ->assertSet('forecastData', $forecastData30);
});
