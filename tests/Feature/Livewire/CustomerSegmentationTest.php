<?php

use App\Livewire\CustomerSegmentation;
use App\Services\MLService;
use Livewire\Livewire;

test('CustomerSegmentation component loads segment data on mount', function () {
    // Mock MLService
    $mlService = mock(MLService::class);

    // Create test chart data
    $chartData = [
        'labels' => ['Segment A', 'Segment B'],
        'series' => [10, 20]
    ];

    // Set expectations
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnTrue();
    $mlService->shouldReceive('getCustomerSegmentChartData')->once()->andReturn($chartData);

    // Bind mock to container
    $this->instance(MLService::class, $mlService);

    // Test component
    Livewire::test(CustomerSegmentation::class)
        ->assertSet('isLoading', false)
        ->assertSet('serviceError', false)
        ->assertSet('segmentData', $chartData);
});

test('CustomerSegmentation component handles service errors gracefully', function () {
    // Mock MLService
    $mlService = mock(MLService::class);

    // Set expectations - service is unhealthy
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnFalse();
    $mlService->shouldReceive('getCustomerSegmentChartData')->never();

    // Bind mock to container
    $this->instance(MLService::class, $mlService);

    // Test component
    Livewire::test(CustomerSegmentation::class)
        ->assertSet('isLoading', false)
        ->assertSet('serviceError', true);
});

test('CustomerSegmentation component refreshes data when loadSegmentData is called', function () {
    // Mock MLService
    $mlService = mock(MLService::class);

    // Create test chart data
    $chartData = [
        'labels' => ['Segment A', 'Segment B'],
        'series' => [10, 20]
    ];

    // Set expectations for initial load
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnTrue();
    $mlService->shouldReceive('getCustomerSegmentChartData')->once()->andReturn($chartData);

    // Set expectations for refresh
    $updatedChartData = [
        'labels' => ['Segment A', 'Segment B', 'Segment C'],
        'series' => [10, 15, 25]
    ];
    $mlService->shouldReceive('isServiceHealthy')->once()->andReturnTrue();
    $mlService->shouldReceive('getCustomerSegmentChartData')->once()->andReturn($updatedChartData);

    // Bind mock to container
    $this->instance(MLService::class, $mlService);

    // Test component
    Livewire::test(CustomerSegmentation::class)
        ->assertSet('segmentData', $chartData)
        ->call('loadSegmentData', $mlService)
        ->assertSet('segmentData', $updatedChartData);
});
