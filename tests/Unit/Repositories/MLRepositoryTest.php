<?php

namespace Tests\Unit\Repositories;

use App\Repositories\MLRepository;
use App\Repositories\MLDataRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class MLRepositoryTest extends TestCase
{
    protected $mlDataRepository;
    protected $mlRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock repository
        $this->mlDataRepository = $this->createMock(MLDataRepository::class);

        // Create repository with explicit API URL for testing
        $this->mlRepository = new MLRepository($this->mlDataRepository, 'http://localhost:8000');

        // Mock Http facade
        Http::fake();
    }

    public function testGetCustomerSegmentsSendsCorrectDataToMLMicroservice()
    // Create mock retailer data
    $retailerData = collect([
        [
            'id' => 1,
            'male_female_ratio' => 0.8,
            'city' => 'New York',
            'urban_rural_classification' => 'urban',
            'total_sales' => 12000
        ],
        [
            'id' => 2,
            'male_female_ratio' => 1.2,
            'city' => 'Chicago',
            'urban_rural_classification' => 'urban',
            'total_sales' => 8000
        ]
    ]);

    // Mock API response
    $expectedResponse = [
        'retailer_segments' => [1 => 0, 2 => 1],
        'segment_descriptions' => ['0' => 'High-value urban retailers', '1' => 'Medium-sized suburban businesses'],
        'segment_distribution' => ['0' => 1, '1' => 1]
    ];

    // Mock data repository
    $this->mlDataRepository->shouldReceive('getRetailerDemographicsData')
        ->once()
        ->andReturn($retailerData);

    // Mock HTTP facade
    Http::fake([
        '*/segment-customers' => Http::response($expectedResponse, 200)
    ]);

    // Call the method
    $result = $this->mlRepository->getCustomerSegments();

    // Assert HTTP request was made with correct data
    Http::assertSent(function ($request) use ($retailerData) {
        return $request->url() == 'http://localhost:8000/segment-customers' &&
               $request->data()['retailers'] == $retailerData->toArray();
    });

    // Verify the result
    expect($result)->toBe($expectedResponse);
});

test('getCustomerSegments handles empty retailer data', function () {
    // Mock empty retailer data
    $this->mlDataRepository->shouldReceive('getRetailerDemographicsData')
        ->once()
        ->andReturn(collect([]));

    // Call the method
    $result = $this->mlRepository->getCustomerSegments();

    // Verify no HTTP request is made for empty data
    Http::assertNothingSent();

    // Verify null result
    expect($result)->toBeNull();
});

test('getCustomerSegments handles API errors gracefully', function () {
    // Create mock retailer data
    $retailerData = collect([
        [
            'id' => 1,
            'male_female_ratio' => 0.8,
            'city' => 'New York',
            'total_sales' => 12000
        ]
    ]);

    // Mock data repository
    $this->mlDataRepository->shouldReceive('getRetailerDemographicsData')
        ->once()
        ->andReturn($retailerData);

    // Mock HTTP facade to return error
    Http::fake([
        '*/segment-customers' => Http::response('Internal Server Error', 500)
    ]);

    // Call the method
    $result = $this->mlRepository->getCustomerSegments();

    // Verify null result on error
    expect($result)->toBeNull();
});

test('getSalesForecast sends correct data to ML microservice', function () {
    // Create mock sales data
    $salesData = collect([
        ['date' => '2025-01-01', 'amount' => 5000],
        ['date' => '2025-01-02', 'amount' => 5500],
        ['date' => '2025-01-03', 'amount' => 6000]
    ]);

    // Mock API response
    $expectedResponse = [
        'forecast_dates' => ['2025-01-04', '2025-01-05'],
        'forecast_values' => [6500, 7000],
        'forecast_lower_bound' => [6000, 6500],
        'forecast_upper_bound' => [7000, 7500]
    ];

    // Mock data repository
    $this->mlDataRepository->shouldReceive('getAktinaSalesData')
        ->with(365)
        ->once()
        ->andReturn($salesData);

    // Mock HTTP facade
    Http::fake([
        '*/predict-sales' => Http::response($expectedResponse, 200)
    ]);

    // Call the method
    $result = $this->mlRepository->getSalesForecast(30);

    // Assert HTTP request was made with correct data
    Http::assertSent(function ($request) use ($salesData) {
        return $request->url() == 'http://localhost:8000/predict-sales' &&
               $request->data()['sales'] == $salesData->toArray() &&
               $request->data()['horizon_days'] == 30;
    });

    // Verify the result
    expect($result)->toBe($expectedResponse);
});

test('getSalesForecast handles empty sales data', function () {
    // Mock empty sales data
    $this->mlDataRepository->shouldReceive('getAktinaSalesData')
        ->with(365)
        ->once()
        ->andReturn(collect([]));

    // Call the method
    $result = $this->mlRepository->getSalesForecast(30);

    // Verify no HTTP request is made for empty data
    Http::assertNothingSent();

    // Verify null result
    expect($result)->toBeNull();
});

test('getSalesForecast handles API errors gracefully', function () {
    // Create mock sales data
    $salesData = collect([
        ['date' => '2025-01-01', 'amount' => 5000],
        ['date' => '2025-01-02', 'amount' => 5500],
    ]);

    // Mock data repository
    $this->mlDataRepository->shouldReceive('getAktinaSalesData')
        ->with(365)
        ->once()
        ->andReturn($salesData);

    // Mock HTTP facade to return error
    Http::fake([
        '*/predict-sales' => Http::response('Internal Server Error', 500)
    ]);

    // Call the method
    $result = $this->mlRepository->getSalesForecast(30);

    // Verify null result on error
    expect($result)->toBeNull();
});

test('isServiceHealthy checks health endpoint', function () {
    // Mock HTTP facade
    Http::fake([
        '*/health' => Http::response(['status' => 'healthy'], 200)
    ]);

    // Call the method
    $result = $this->mlRepository->isServiceHealthy();

    // Assert HTTP request was made to health endpoint
    Http::assertSent(function ($request) {
        return $request->url() == 'http://localhost:8000/health';
    });

    // Verify result
    expect($result)->toBeTrue();
});

test('isServiceHealthy returns false for unhealthy service', function () {
    // Mock HTTP facade to simulate service unavailable
    Http::fake([
        '*/health' => Http::response('', 500)
    ]);

    // Call the method
    $result = $this->mlRepository->isServiceHealthy();

    // Verify result
    expect($result)->toBeFalse();
});

test('isServiceHealthy handles connection errors', function () {
    // Mock HTTP facade to throw exception
    Http::fake(function () {
        throw new \Exception('Connection refused');
    });

    // Call the method
    $result = $this->mlRepository->isServiceHealthy();

    // Verify result
    expect($result)->toBeFalse();
});
