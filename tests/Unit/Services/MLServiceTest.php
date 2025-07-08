<?php

use App\Services\MLService;
use App\Repositories\MLRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mockery\Matcher\Pattern;

beforeEach(function () {
    // Create mock MLRepository
    $this->mlRepository = mock(MLRepository::class);

    // Create MLService instance with mocked repository
    $this->mlService = new MLService($this->mlRepository);

    // Reset cache facade between tests
    \Mockery::close();

    // Mock the Cache facade by default
    Cache::spy();

    // Mock the Log facade by default
    Log::shouldReceive('warning')->byDefault();
});

test('getCustomerSegments returns cached data', function () {
    // Sample data
    $expectedResponse = [
        'retailer_segments' => [1 => 0, 2 => 1],
        'segment_descriptions' => ['0' => 'High-value urban retailers', '1' => 'Medium-sized suburban businesses'],
        'segment_distribution' => ['0' => 5, '1' => 10]
    ];

    // Setup cache behavior
    Cache::shouldReceive('remember')
        ->once()
        ->with('ml_customer_segments', 86400, \Mockery::any())
        ->andReturnUsing(function ($key, $ttl, $callback) use ($expectedResponse) {
            return $expectedResponse;
        });

    // Repository should never be called since cache is mocked to return data
    $this->mlRepository->shouldNotReceive('getCustomerSegments');

    // Call the method and verify result
    $result = $this->mlService->getCustomerSegments();
    expect($result)->toBe($expectedResponse);
});

test('getCustomerSegments handles failure gracefully', function () {
    // Default empty response for failure case
    $defaultResponse = [
        'retailer_segments' => [],
        'segment_descriptions' => [],
        'segment_distribution' => []
    ];

    // Setup cache to execute the callback
    Cache::shouldReceive('remember')
        ->once()
        ->with('ml_customer_segments', 86400, \Mockery::any())
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Mock repository to return null (failure)
    $this->mlRepository->shouldReceive('getCustomerSegments')
        ->once()
        ->andReturnNull();

    // Expect a warning log
    Log::shouldReceive('warning')
        ->with('Failed to get customer segments from ML microservice')
        ->once();

    // Call the method
    $result = $this->mlService->getCustomerSegments();

    // Verify we get the default empty response structure
    expect($result)->toBeArray()
        ->and($result['retailer_segments'])->toBeArray()
        ->and($result['segment_descriptions'])->toBeArray()
        ->and($result['segment_distribution'])->toBeArray();
});

test('getSalesForecast returns cached data', function () {
    // Sample forecast data
    $expectedResponse = [
        'forecast_dates' => ['2025-07-08', '2025-07-09'],
        'forecast_values' => [1000, 1200],
        'forecast_lower_bound' => [900, 1000],
        'forecast_upper_bound' => [1100, 1300]
    ];

    // Setup cache behavior
    Cache::shouldReceive('remember')
        ->once()
        ->with('ml_sales_forecast_90', 86400, \Mockery::any())
        ->andReturnUsing(function ($key, $ttl, $callback) use ($expectedResponse) {
            return $expectedResponse;
        });

    // Repository should never be called since cache is mocked to return data
    $this->mlRepository->shouldNotReceive('getSalesForecast');

    // Call the method and verify result
    $result = $this->mlService->getSalesForecast(90);
    expect($result)->toBe($expectedResponse);
});

test('getSalesForecast handles failure gracefully', function () {
    // Default empty response for failure case
    $defaultResponse = [
        'forecast_dates' => [],
        'forecast_values' => [],
        'forecast_lower_bound' => [],
        'forecast_upper_bound' => []
    ];

    // Setup cache to execute the callback
    Cache::shouldReceive('remember')
        ->once()
        ->with('ml_sales_forecast_90', 86400, \Mockery::any())
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Mock repository to return null (failure)
    $this->mlRepository->shouldReceive('getSalesForecast')
        ->once()
        ->with(90)
        ->andReturnNull();

    // Expect a warning log
    Log::shouldReceive('warning')
        ->with('Failed to get sales forecast from ML microservice')
        ->once();

    // Call the method
    $result = $this->mlService->getSalesForecast(90);

    // Verify we get the default empty response structure
    expect($result)->toBeArray()
        ->and($result['forecast_dates'])->toBeArray()
        ->and($result['forecast_values'])->toBeArray()
        ->and($result['forecast_lower_bound'])->toBeArray()
        ->and($result['forecast_upper_bound'])->toBeArray();
});

test('getCustomerSegmentChartData transforms data correctly', function () {
    // Mock getCustomerSegments response
    $segmentsData = [
        'segment_descriptions' => ['0' => 'Segment A', '1' => 'Segment B'],
        'segment_distribution' => ['0' => 5, '1' => 10],
        'retailer_segments' => [1 => 0, 2 => 1]
    ];

    // Use partial mock to only mock getCustomerSegments
    $partialMock = Mockery::mock(MLService::class, [$this->mlRepository])->makePartial();
    $partialMock->shouldReceive('getCustomerSegments')
        ->once()
        ->andReturn($segmentsData);

    $result = $partialMock->getCustomerSegmentChartData();
    expect($result)->toBeArray()
        ->and($result['labels'])->toBe(['Segment A', 'Segment B'])
        ->and($result['series'])->toBe([5, 10]);
});

test('getCustomerSegmentChartData handles empty data', function () {
    // Mock getCustomerSegments to return empty data
    $partialMock = Mockery::mock(MLService::class, [$this->mlRepository])->makePartial();
    $partialMock->shouldReceive('getCustomerSegments')
        ->once()
        ->andReturn(['segment_distribution' => []]);

    $result = $partialMock->getCustomerSegmentChartData();
    expect($result)->toBeArray()
        ->and($result['labels'])->toBe([])
        ->and($result['series'])->toBe([]);
});

test('getSalesForecastChartData transforms data correctly', function () {
    // Mock getSalesForecast response
    $forecastData = [
        'forecast_dates' => ['2025-07-08', '2025-07-09'],
        'forecast_values' => [1000, 1200],
        'forecast_lower_bound' => [900, 1000],
        'forecast_upper_bound' => [1100, 1300]
    ];

    // Use partial mock to only mock getSalesForecast
    $partialMock = Mockery::mock(MLService::class, [$this->mlRepository])->makePartial();
    $partialMock->shouldReceive('getSalesForecast')
        ->with(90)
        ->once()
        ->andReturn($forecastData);

    $result = $partialMock->getSalesForecastChartData(90);
    expect($result)->toBeArray()
        ->and($result['dates'])->toBe(['2025-07-08', '2025-07-09'])
        ->and($result['values'])->toBe([1000, 1200])
        ->and($result['lower'])->toBe([900, 1000])
        ->and($result['upper'])->toBe([1100, 1300]);
});

test('getSalesForecastChartData handles empty data', function () {
    // Mock getSalesForecast to return empty data
    $partialMock = Mockery::mock(MLService::class, [$this->mlRepository])->makePartial();
    $partialMock->shouldReceive('getSalesForecast')
        ->with(90)
        ->once()
        ->andReturn(['forecast_dates' => []]);

    $result = $partialMock->getSalesForecastChartData(90);
    expect($result)->toBeArray()
        ->and($result['dates'])->toBe([])
        ->and($result['values'])->toBe([])
        ->and($result['lower'])->toBe([])
        ->and($result['upper'])->toBe([]);
});

test('isServiceHealthy returns cached status', function () {
    // Setup Cache mock for this test
    Cache::shouldReceive('remember')
        ->with('ml_service_health', 300, Mockery::any())
        ->andReturnUsing(function ($key, $ttl, $callback) {
            static $called = false;
            if (!$called) {
                $called = true;
                return $callback();
            }
            return true;
        });

    // Mock repository response
    $this->mlRepository->shouldReceive('isServiceHealthy')
        ->once()
        ->andReturnTrue();

    // First call should hit the repository
    $result = $this->mlService->isServiceHealthy();
    expect($result)->toBeTrue();

    // Second call should use the cache
    $this->mlRepository->shouldReceive('isServiceHealthy')->never();
    $result = $this->mlService->isServiceHealthy();
    expect($result)->toBeTrue();
});

test('clearCache removes all ML related cache keys', function () {
    // Mock Cache for this specific test
    Cache::shouldReceive('put')->zeroOrMoreTimes();

    Cache::shouldReceive('has')
        ->with('ml_customer_segments')
        ->andReturn(true, false);

    Cache::shouldReceive('has')
        ->with('ml_service_health')
        ->andReturn(true, false);

    Cache::shouldReceive('has')
        ->with('ml_sales_forecast_90')
        ->andReturn(true, false);

    // Should call forget for each cache key
    Cache::shouldReceive('forget')
        ->with('ml_customer_segments')
        ->once();

    Cache::shouldReceive('forget')
        ->with('ml_service_health')
        ->once();

    Cache::shouldReceive('forget')
        ->with(Mockery::pattern('/^ml_sales_forecast_\d+$/'))
        ->once();

    // Verify cache has data before clearing
    expect(Cache::has('ml_customer_segments'))->toBeTrue();
    expect(Cache::has('ml_service_health'))->toBeTrue();
    expect(Cache::has('ml_sales_forecast_90'))->toBeTrue();

    // Clear cache
    $this->mlService->clearCache();

    // Verify cache is cleared
    expect(Cache::has('ml_customer_segments'))->toBeFalse();
    expect(Cache::has('ml_service_health'))->toBeFalse();
    expect(Cache::has('ml_sales_forecast_90'))->toBeFalse();
});
