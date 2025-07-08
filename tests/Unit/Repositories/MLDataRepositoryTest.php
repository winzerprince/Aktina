<?php

use App\Repositories\MLDataRepository;
use App\Models\Retailer;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mlDataRepository = new MLDataRepository();
});

test('getRetailerDemographicsData returns correct data structure', function () {
    // Create test users
    $user1 = User::factory()->create(['name' => 'Test Retailer 1']);
    $user2 = User::factory()->create(['name' => 'Test Retailer 2']);

    // Create test retailers
    $retailer1 = Retailer::factory()->create([
        'user_id' => $user1->id,
        'male_female_ratio' => 1.2,
        'city' => 'New York',
        'urban_rural_classification' => 'urban',
        'customer_age_class' => 'adult',
        'customer_income_bracket' => 'high',
        'customer_education_level' => 'high',
        'business_type' => 'Electronics',
        'annual_revenue' => '$500,000',
        'employee_count' => '10-50',
        'years_in_business' => 5
    ]);

    $retailer2 = Retailer::factory()->create([
        'user_id' => $user2->id,
        'male_female_ratio' => 0.8,
        'city' => 'Chicago',
        'urban_rural_classification' => 'suburban',
        'customer_age_class' => 'youth',
        'customer_income_bracket' => 'medium',
        'customer_education_level' => 'mid',
        'business_type' => 'Clothing',
        'annual_revenue' => '$200,000',
        'employee_count' => '1-10',
        'years_in_business' => 2
    ]);

    // Create Aktina user
    $aktinaUser = User::factory()->create(['name' => 'Aktina']);

    // Create orders from Aktina to retailers
    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $user1->id,
        'price' => 12000
    ]);

    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $user2->id,
        'price' => 8000
    ]);

    // Get data
    $result = $this->mlDataRepository->getRetailerDemographicsData();

    // Assert result structure
    expect($result)->toBeCollection()
        ->toHaveCount(2);

    // Find retailer 1 in results
    $retailer1Data = $result->firstWhere('id', $retailer1->id);
    expect($retailer1Data)->toBeTruthy();
    expect($retailer1Data['male_female_ratio'])->toBe(1.2);
    expect($retailer1Data['city'])->toBe('New York');
    expect($retailer1Data['urban_rural_classification'])->toBe('urban');
    expect($retailer1Data['total_sales'])->toBe(12000);

    // Find retailer 2 in results
    $retailer2Data = $result->firstWhere('id', $retailer2->id);
    expect($retailer2Data)->toBeTruthy();
    expect($retailer2Data['male_female_ratio'])->toBe(0.8);
    expect($retailer2Data['city'])->toBe('Chicago');
    expect($retailer2Data['urban_rural_classification'])->toBe('suburban');
    expect($retailer2Data['total_sales'])->toBe(8000);
});

test('getRetailerDemographicsData handles missing Aktina user', function () {
    // Create test users
    $user = User::factory()->create(['name' => 'Test Retailer']);

    // Create test retailers
    Retailer::factory()->create([
        'user_id' => $user->id,
        'male_female_ratio' => 1.2,
        'city' => 'New York'
    ]);

    // Get data (with no Aktina user)
    $result = $this->mlDataRepository->getRetailerDemographicsData();

    // Assert result structure
    expect($result)->toBeCollection();

    // Verify retailer exists but has 0 total_sales
    $retailerData = $result->first();
    expect($retailerData['total_sales'])->toBe(0);
});

test('getAktinaSalesData returns correct data structure', function () {
    // Create Aktina user
    $aktinaUser = User::factory()->create(['name' => 'Aktina']);

    // Create buyer users
    $buyer1 = User::factory()->create();
    $buyer2 = User::factory()->create();

    // Create orders with specific dates
    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $buyer1->id,
        'price' => 5000,
        'created_at' => Carbon::now()->subDays(5)
    ]);

    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $buyer2->id,
        'price' => 7000,
        'created_at' => Carbon::now()->subDays(4)
    ]);

    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $buyer1->id,
        'price' => 3000,
        'created_at' => Carbon::now()->subDays(3)
    ]);

    // Get data for last 10 days
    $result = $this->mlDataRepository->getAktinaSalesData(10);

    // Assert result structure
    expect($result)->toBeCollection();
    expect($result)->toHaveCount(3);

    // Check data format
    $firstEntry = $result->first();
    expect($firstEntry)->toHaveKey('date');
    expect($firstEntry)->toHaveKey('amount');
    expect($firstEntry['amount'])->toBeNumeric();
});

test('getAktinaSalesData handles missing Aktina user', function () {
    // Get data without creating Aktina user
    $result = $this->mlDataRepository->getAktinaSalesData(10);

    // Assert empty result
    expect($result)->toBeCollection();
    expect($result)->toBeEmpty();
});

test('getRetailerSalesVolumeCategories categorizes correctly', function () {
    // Create test users
    $user1 = User::factory()->create(['name' => 'Test Retailer 1']);
    $user2 = User::factory()->create(['name' => 'Test Retailer 2']);
    $user3 = User::factory()->create(['name' => 'Test Retailer 3']);

    // Create test retailers
    $retailer1 = Retailer::factory()->create(['user_id' => $user1->id]);
    $retailer2 = Retailer::factory()->create(['user_id' => $user2->id]);
    $retailer3 = Retailer::factory()->create(['user_id' => $user3->id]);

    // Create Aktina user
    $aktinaUser = User::factory()->create(['name' => 'Aktina']);

    // Create orders from Aktina to retailers with different volumes
    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $user1->id,
        'price' => 15000  // high
    ]);

    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $user2->id,
        'price' => 7000   // medium
    ]);

    Order::factory()->create([
        'seller_id' => $aktinaUser->id,
        'buyer_id' => $user3->id,
        'price' => 3000   // low
    ]);

    // Get categories
    $result = $this->mlDataRepository->getRetailerSalesVolumeCategories();

    // Assert categories are correct
    expect($result->get($retailer1->id))->toBe('high');
    expect($result->get($retailer2->id))->toBe('medium');
    expect($result->get($retailer3->id))->toBe('low');
});
