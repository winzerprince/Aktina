<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VendorOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
        $this->artisan('db:seed', ['--class' => 'ProductSeeder']);
    }

    /** @test */
    public function vendor_can_see_their_orders()
    {
        $vendor = User::where('email', 'vendor@gmail.com')->first();
        $retailer = User::where('email', 'retailer@gmail.com')->first();

        // Create an order where vendor is the seller
        $order = Order::factory()->create([
            'buyer_id' => $retailer->id,
            'seller_id' => $vendor->id,
            'status' => 'pending'
        ]);

        // Login as vendor
        $this->actingAs($vendor);

        // Visit vendor orders page
        $response = $this->get('/vendor/orders');
        $response->assertStatus(200);

        // Check that the Livewire component is present
        $response->assertSeeLivewire('vendor.vendor-order-management');
    }

    /** @test */
    public function vendor_has_product_inventory()
    {
        $product = Product::first();
        $this->assertNotNull($product);

        // Check that Vendor Company has inventory
        $vendorQuantity = $product->getCompanyQuantity('Vendor Company');
        $this->assertGreaterThan(0, $vendorQuantity);
    }

    /** @test */
    public function stock_check_uses_company_quantities()
    {
        $vendor = User::where('email', 'vendor@gmail.com')->first();
        $this->actingAs($vendor);

        $product = Product::first();
        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 5 // Less than vendor's inventory
            ]
        ];

        $orderRepository = app(\App\Interfaces\Repositories\OrderRepositoryInterface::class);
        $stockCheck = $orderRepository->checkProductStockLevels($items);

        $this->assertTrue($stockCheck[0]['in_stock']);
        $this->assertGreaterThanOrEqual(5, $stockCheck[0]['available']);
    }

    /** @test */
    public function vendor_can_create_orders_with_available_stock()
    {
        $vendor = User::where('email', 'vendor@gmail.com')->first();
        $retailer = User::where('email', 'retailer@gmail.com')->first();
        $product = Product::first();

        $orderData = [
            'buyer_id' => $retailer->id,
            'seller_id' => $vendor->id,
            'price' => 100.00,
            'items' => json_encode([
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 50.00,
                    'total' => 100.00
                ]
            ]),
            'status' => 'pending'
        ];

        $order = Order::create($orderData);
        $this->assertNotNull($order);
        $this->assertEquals($vendor->id, $order->seller_id);
        $this->assertEquals($retailer->id, $order->buyer_id);
    }

    /** @test */
    public function vendor_orders_appear_in_management_component()
    {
        $vendor = User::where('email', 'vendor@gmail.com')->first();
        $retailer = User::where('email', 'retailer@gmail.com')->first();

        // Create multiple orders for the vendor
        Order::factory(3)->create([
            'buyer_id' => $retailer->id,
            'seller_id' => $vendor->id,
        ]);

        $this->actingAs($vendor);

        // Test the Livewire component
        $component = \Livewire\Livewire::test(\App\Livewire\Vendor\VendorOrderManagement::class);

        // Check that orders are loaded
        $component->assertViewHas('orders');

        // Check that the orders collection is not empty
        $orders = $component->viewData('orders');
        $this->assertGreaterThan(0, $orders->count());

        // Verify all orders belong to the vendor
        foreach ($orders as $order) {
            $this->assertEquals($vendor->id, $order->seller_id);
        }
    }
}
