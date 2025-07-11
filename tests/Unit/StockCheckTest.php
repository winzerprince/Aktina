<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
        $this->artisan('db:seed', ['--class' => 'ProductSeeder']);
    }

    /** @test */
    public function stock_check_returns_correct_availability_for_vendor()
    {
        $vendor = User::where('email', 'vendor@gmail.com')->first();
        $this->actingAs($vendor);

        $product = Product::first();
        $vendorQuantity = $product->getCompanyQuantity('Vendor Company');

        $orderRepository = new OrderRepository();

        // Test with quantity within available stock
        $items = [
            [
                'product_id' => $product->id,
                'quantity' => min(5, $vendorQuantity) // Use minimum to ensure we don't exceed available
            ]
        ];

        $stockCheck = $orderRepository->checkProductStockLevels($items);

        $this->assertCount(1, $stockCheck);
        $this->assertTrue($stockCheck[0]['in_stock']);
        $this->assertEquals($vendorQuantity, $stockCheck[0]['available']);
        $this->assertFalse($stockCheck[0]['has_warning']);
    }

    /** @test */
    public function stock_check_shows_warning_when_quantity_exceeds_available()
    {
        $vendor = User::where('email', 'vendor@gmail.com')->first();
        $this->actingAs($vendor);

        $product = Product::first();
        $vendorQuantity = $product->getCompanyQuantity('Vendor Company');

        $orderRepository = new OrderRepository();

        // Test with quantity exceeding available stock
        $items = [
            [
                'product_id' => $product->id,
                'quantity' => $vendorQuantity + 10 // More than available
            ]
        ];

        $stockCheck = $orderRepository->checkProductStockLevels($items);

        $this->assertCount(1, $stockCheck);
        $this->assertFalse($stockCheck[0]['in_stock']);
        $this->assertEquals($vendorQuantity, $stockCheck[0]['available']);
        $this->assertTrue($stockCheck[0]['has_warning']);
    }

    /** @test */
    public function stock_check_handles_nonexistent_product()
    {
        $vendor = User::where('email', 'vendor@gmail.com')->first();
        $this->actingAs($vendor);

        $orderRepository = new OrderRepository();

        $items = [
            [
                'product_id' => 99999, // Non-existent product
                'quantity' => 5
            ]
        ];

        $stockCheck = $orderRepository->checkProductStockLevels($items);

        $this->assertCount(1, $stockCheck);
        $this->assertFalse($stockCheck[0]['in_stock']);
        $this->assertEquals(0, $stockCheck[0]['available']);
        $this->assertFalse($stockCheck[0]['has_warning']);
    }

    /** @test */
    public function product_company_quantity_method_works()
    {
        $product = Product::first();

        // Test getting quantity for existing company
        $vendorQuantity = $product->getCompanyQuantity('Vendor Company');
        $this->assertGreaterThan(0, $vendorQuantity);

        // Test getting quantity for non-existent company
        $nonExistentQuantity = $product->getCompanyQuantity('Non-Existent Company');
        $this->assertEquals(0, $nonExistentQuantity);
    }
}
