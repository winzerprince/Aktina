<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\Resource;
use App\Models\Warehouse;
use App\Services\EnhancedOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $retailer;
    protected $vendor;
    protected $productionManager;
    protected $warehouse;
    protected $resources;
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->retailer = User::factory()->create([
            'role' => 'retailer',
            'company_name' => 'Test Retail Co.',
            'email' => 'retailer@test.com'
        ]);

        $this->vendor = User::factory()->create([
            'role' => 'vendor',
            'company_name' => 'Test Vendor Inc.',
            'email' => 'vendor@test.com'
        ]);

        $this->productionManager = User::factory()->create([
            'role' => 'production_manager',
            'company_name' => 'Aktina Technologies',
            'email' => 'pm@aktina.com'
        ]);

        $this->warehouse = Warehouse::factory()->create();
        $this->resources = Resource::factory()->count(3)->create();

        $this->orderService = app(EnhancedOrderService::class);
    }

    #[Test]
    public function complete_order_workflow_from_creation_to_completion()
    {
        Event::fake();
        Notification::fake();

        // Step 1: Create Order
        $orderData = [
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [
                [
                    'resource_id' => $this->resources[0]->id,
                    'quantity' => 5,
                    'unit_price' => 100.00
                ],
                [
                    'resource_id' => $this->resources[1]->id,
                    'quantity' => 3,
                    'unit_price' => 50.00
                ]
            ],
            'notes' => 'Integration test order',
            'delivery_address' => '123 Test Street, Test City',
            'expected_delivery_date' => '2025-08-15'
        ];

        $order = $this->orderService->createOrder($orderData);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        $this->assertEquals(650.00, $order->price); // (5*100) + (3*50)
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'status' => Order::STATUS_PENDING
        ]);

        // Step 2: Vendor accepts the order
        $this->actingAs($this->vendor);
        $response = $this->post(route('orders.accept', $order));

        $response->assertStatus(302);
        $order->refresh();
        $this->assertEquals(Order::STATUS_ACCEPTED, $order->status);

        // Step 3: Production Manager approves for processing
        $this->actingAs($this->productionManager);
        $response = $this->post(route('orders.approve', $order));

        $response->assertStatus(302);
        $order->refresh();
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);
        $this->assertEquals($this->productionManager->id, $order->approver_id);
        $this->assertNotNull($order->approved_at);

        // Step 4: Start fulfillment process
        $response = $this->post(route('orders.start-fulfillment', $order));

        $response->assertStatus(302);
        $order->refresh();
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);
        $this->assertNotNull($order->fulfillment_started_at);

        // Step 5: Mark as fulfilled and ready for shipping
        $response = $this->post(route('orders.fulfill', $order));

        $response->assertStatus(302);
        $order->refresh();
        $this->assertEquals(Order::STATUS_FULFILLED, $order->status);

        // Step 6: Ship the order
        $response = $this->post(route('orders.ship', $order), [
            'tracking_number' => 'TRK123456789',
            'carrier' => 'FedEx'
        ]);

        $response->assertStatus(302);
        $order->refresh();
        $this->assertEquals(Order::STATUS_SHIPPED, $order->status);
        $this->assertNotNull($order->shipped_at);

        // Step 7: Mark as delivered
        $response = $this->post(route('orders.deliver', $order));

        $response->assertStatus(302);
        $order->refresh();
        $this->assertEquals(Order::STATUS_DELIVERED, $order->status);

        // Step 8: Complete the order
        $response = $this->post(route('orders.complete', $order));

        $response->assertStatus(302);
        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETE, $order->status);
        $this->assertNotNull($order->completed_at);

        // Verify all status transitions were logged
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $order->id,
            'subject_type' => Order::class,
            'description' => 'Order created'
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $order->id,
            'subject_type' => Order::class,
            'description' => 'Order completed'
        ]);
    }

    #[Test]
    public function order_can_be_rejected_by_vendor()
    {
        Event::fake();

        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $this->actingAs($this->vendor);
        $response = $this->post(route('orders.reject', $order), [
            'rejection_reason' => 'Out of stock'
        ]);

        $response->assertStatus(302);
        $order->refresh();

        $this->assertEquals(Order::STATUS_REJECTED, $order->status);
        $this->assertNotNull($order->rejected_at);
        $this->assertEquals('Out of stock', $order->rejection_reason);
    }

    #[Test]
    public function order_can_be_cancelled_by_buyer_when_pending()
    {
        Event::fake();

        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $this->actingAs($this->retailer);
        $response = $this->post(route('orders.cancel', $order), [
            'cancellation_reason' => 'Changed requirements'
        ]);

        $response->assertStatus(302);
        $order->refresh();

        $this->assertEquals(Order::STATUS_CANCELLED, $order->status);
        $this->assertEquals('Changed requirements', $order->cancellation_reason);
    }

    #[Test]
    public function order_cannot_be_cancelled_after_acceptance()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_ACCEPTED,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $this->actingAs($this->retailer);
        $response = $this->post(route('orders.cancel', $order));

        $response->assertStatus(400); // Bad request
        $order->refresh();
        $this->assertEquals(Order::STATUS_ACCEPTED, $order->status);
    }

    #[Test]
    public function fulfillment_failure_is_handled_correctly()
    {
        Event::fake();

        $order = Order::factory()->create([
            'status' => Order::STATUS_PROCESSING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'fulfillment_started_at' => now()
        ]);

        $this->actingAs($this->productionManager);
        $response = $this->post(route('orders.fail-fulfillment', $order), [
            'failure_reason' => 'Equipment malfunction'
        ]);

        $response->assertStatus(302);
        $order->refresh();

        $this->assertEquals(Order::STATUS_FULFILLMENT_FAILED, $order->status);
        $this->assertNotNull($order->fulfillment_failed_at);
        $this->assertEquals('Equipment malfunction', $order->fulfillment_failure_reason);
    }

    #[Test]
    public function partial_fulfillment_is_tracked_correctly()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PROCESSING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [
                ['resource_id' => 1, 'quantity' => 10, 'unit_price' => 100.00],
                ['resource_id' => 2, 'quantity' => 5, 'unit_price' => 50.00]
            ]
        ]);

        $this->actingAs($this->productionManager);
        $response = $this->post(route('orders.partial-fulfill', $order), [
            'fulfilled_items' => [
                ['resource_id' => 1, 'quantity' => 7], // Partially fulfilled
                ['resource_id' => 2, 'quantity' => 5]  // Fully fulfilled
            ]
        ]);

        $response->assertStatus(302);
        $order->refresh();

        $this->assertEquals(Order::STATUS_PARTIALLY_FULFILLED, $order->status);
        $this->assertNotNull($order->fulfillment_data);
        $this->assertArrayHasKey('fulfilled_items', $order->fulfillment_data);
    }

    #[Test]
    public function order_can_be_returned_after_delivery()
    {
        Event::fake();

        $order = Order::factory()->create([
            'status' => Order::STATUS_DELIVERED,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'completed_at' => now()->subDays(5)
        ]);

        $this->actingAs($this->retailer);
        $response = $this->post(route('orders.return', $order), [
            'return_reason' => 'Defective items',
            'return_items' => [
                ['resource_id' => 1, 'quantity' => 2]
            ]
        ]);

        $response->assertStatus(302);
        $order->refresh();

        $this->assertEquals(Order::STATUS_RETURNED, $order->status);
        $this->assertEquals('Defective items', $order->return_reason);
    }

    #[Test]
    public function inventory_is_reserved_during_order_processing()
    {
        Event::fake();

        $resource = $this->resources[0];

        // Set initial inventory
        $resource->update(['quantity' => 100]);

        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [
                ['resource_id' => $resource->id, 'quantity' => 10, 'unit_price' => 100.00]
            ]
        ]);

        // Accept order - should reserve inventory
        $this->actingAs($this->vendor);
        $this->post(route('orders.accept', $order));

        $resource->refresh();
        $this->assertEquals(90, $resource->available_quantity); // Reserved 10 units
        $this->assertEquals(10, $resource->reserved_quantity);
    }

    #[Test]
    public function inventory_is_released_when_order_is_cancelled()
    {
        Event::fake();

        $resource = $this->resources[0];

        // Set initial inventory
        $resource->update([
            'quantity' => 100,
            'available_quantity' => 90,
            'reserved_quantity' => 10
        ]);

        $order = Order::factory()->create([
            'status' => Order::STATUS_ACCEPTED,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [
                ['resource_id' => $resource->id, 'quantity' => 10, 'unit_price' => 100.00]
            ]
        ]);

        // Cancel order - should release reserved inventory
        $this->actingAs($this->retailer);
        $this->post(route('orders.cancel', $order), [
            'cancellation_reason' => 'No longer needed'
        ]);

        $resource->refresh();
        $this->assertEquals(100, $resource->available_quantity); // Released 10 units
        $this->assertEquals(0, $resource->reserved_quantity);
    }

    #[Test]
    public function backorder_is_created_for_insufficient_inventory()
    {
        Event::fake();

        $resource = $this->resources[0];
        $resource->update(['quantity' => 5]); // Only 5 available

        $orderData = [
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [
                [
                    'resource_id' => $resource->id,
                    'quantity' => 10, // Requesting more than available
                    'unit_price' => 100.00
                ]
            ]
        ];

        $order = $this->orderService->createOrder($orderData);

        $order->refresh();
        $this->assertTrue($order->is_backorder);
        $this->assertNotNull($order->backorder_data);
        $this->assertEquals(5, $order->backorder_data['shortage_quantity']);
    }

    #[Test]
    public function order_metrics_are_calculated_correctly()
    {
        // Create orders with different statuses and dates
        Order::factory()->count(5)->create([
            'status' => Order::STATUS_PENDING,
            'created_at' => now()->subDays(7)
        ]);

        Order::factory()->count(3)->create([
            'status' => Order::STATUS_COMPLETE,
            'price' => 1000.00,
            'created_at' => now()->subDays(7)
        ]);

        Order::factory()->count(2)->create([
            'status' => Order::STATUS_CANCELLED,
            'created_at' => now()->subDays(7)
        ]);

        $metrics = $this->orderService->getOrderMetrics();

        $this->assertEquals(10, $metrics['total_orders']);
        $this->assertEquals(5, $metrics['pending_orders']);
        $this->assertEquals(3, $metrics['completed_orders']);
        $this->assertEquals(3000.00, $metrics['total_completed_value']);
        $this->assertEquals(1000.00, $metrics['average_order_value']);
    }
}
