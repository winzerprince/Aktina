<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EnhancedOrderService;
use App\Interfaces\Repositories\EnhancedOrderRepositoryInterface;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Models\Order;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class EnhancedOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EnhancedOrderService $orderService;
    protected $orderRepository;
    protected $inventoryService;
    protected $warehouseService;
    protected User $buyer;
    protected User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->buyer = User::factory()->create([
            'role' => 'retailer',
            'company_name' => 'Test Retailer'
        ]);

        $this->seller = User::factory()->create([
            'role' => 'vendor',
            'company_name' => 'Test Vendor'
        ]);

        // Create mocks
        $this->orderRepository = Mockery::mock(EnhancedOrderRepositoryInterface::class);
        $this->inventoryService = Mockery::mock(InventoryServiceInterface::class);
        $this->warehouseService = Mockery::mock(WarehouseServiceInterface::class);

        // Create service instance
        $this->orderService = new EnhancedOrderService(
            $this->orderRepository,
            $this->inventoryService,
            $this->warehouseService
        );
    }

    #[Test]
    public function it_can_create_an_order_with_valid_data()
    {
        $resource = Resource::factory()->create(['name' => 'Test Resource']);

        $orderData = [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'items' => [
                [
                    'resource_id' => $resource->id,
                    'quantity' => 5,
                    'unit_price' => 100.00
                ]
            ],
            'delivery_address' => '123 Test St',
            'expected_delivery_date' => '2025-08-01'
        ];

        // Mock inventory availability check
        $this->inventoryService
            ->shouldReceive('getStockLevel')
            ->once()
            ->with(Mockery::type(Resource::class))
            ->andReturn(10); // sufficient stock

        // Mock warehouse assignment
        $this->warehouseService
            ->shouldReceive('assignOptimalWarehouse')
            ->once()
            ->andReturn(1);

        // Mock inventory reservation
        $this->inventoryService
            ->shouldReceive('reserveInventory')
            ->once()
            ->with(Mockery::type('int'), Mockery::type('int'), Mockery::type('string'))
            ->andReturn(true);

        // Mock order creation
        $expectedOrder = new Order([
            'id' => 1,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_PENDING,
            'items' => $orderData['items'],
            'price' => 500.00
        ]);

        // Mock the relationships
        $expectedOrder->setRelation('buyer', $this->buyer);
        $expectedOrder->setRelation('seller', $this->seller);

        $this->orderRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($expectedOrder);

        $result = $this->orderService->createOrder($orderData);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals($this->buyer->id, $result->buyer_id);
        $this->assertEquals($this->seller->id, $result->seller_id);
        $this->assertEquals(Order::STATUS_PENDING, $result->status);
    }

    #[Test]
    public function it_throws_exception_when_inventory_is_insufficient()
    {
        $resource = Resource::factory()->create(['name' => 'Test Resource']);

        $orderData = [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'items' => [
                [
                    'resource_id' => $resource->id,
                    'quantity' => 5,
                    'unit_price' => 100.00
                ]
            ]
        ];

        // Mock inventory unavailability - return less stock than needed
        $this->inventoryService
            ->shouldReceive('getStockLevel')
            ->once()
            ->with(Mockery::type(Resource::class))
            ->andReturn(2); // insufficient stock

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient inventory for order items');

        $this->orderService->createOrder($orderData);
    }

    #[Test]
    public function it_calculates_order_value_correctly()
    {
        $resource1 = Resource::factory()->create(['unit_cost' => 100.00]);
        $resource2 = Resource::factory()->create(['unit_cost' => 50.00]);

        $items = [
            ['resource_id' => $resource1->id, 'quantity' => 5, 'unit_price' => 100.00],
            ['resource_id' => $resource2->id, 'quantity' => 3, 'unit_price' => 50.00]
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->orderService);
        $method = $reflection->getMethod('calculateOrderValue');
        $method->setAccessible(true);

        $result = $method->invoke($this->orderService, $items);

        $this->assertEquals(650.00, $result); // (5 * 100) + (3 * 50)
    }

    #[Test]
    public function it_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING
        ]);

        $updatedOrder = $order->replicate();
        $updatedOrder->status = Order::STATUS_ACCEPTED;

        $this->orderRepository
            ->shouldReceive('updateOrderStatus')
            ->once()
            ->with($order->id, Order::STATUS_ACCEPTED)
            ->andReturn($updatedOrder);

        $result = $this->orderService->updateOrderStatus($order->id, Order::STATUS_ACCEPTED);

        $this->assertEquals(Order::STATUS_ACCEPTED, $result->status);
    }

    #[Test]
    public function it_can_get_orders_for_user()
    {
        $orders = new Collection([
            Order::factory()->make(['buyer_id' => $this->buyer->id]),
            Order::factory()->make(['buyer_id' => $this->buyer->id])
        ]);

        $this->orderRepository
            ->shouldReceive('getOrdersByUser')
            ->once()
            ->with($this->buyer->id)
            ->andReturn($orders);

        $result = $this->orderService->getOrdersByUser($this->buyer->id);

        $this->assertCount(2, $result);
        $this->assertEquals($orders, $result);
    }

    #[Test]
    public function it_can_get_orders_by_status()
    {
        $orders = new Collection([
            Order::factory()->make(['status' => Order::STATUS_PENDING]),
            Order::factory()->make(['status' => Order::STATUS_PENDING])
        ]);

        $this->orderRepository
            ->shouldReceive('getOrdersByStatus')
            ->once()
            ->with(Order::STATUS_PENDING)
            ->andReturn($orders);

        $result = $this->orderService->getOrdersByStatus(Order::STATUS_PENDING);

        $this->assertCount(2, $result);
        $this->assertEquals($orders, $result);
    }

    #[Test]
    public function it_can_get_order_analytics()
    {
        $analytics = [
            'total_orders' => 100,
            'pending_orders' => 25,
            'completed_orders' => 60,
            'cancelled_orders' => 15,
            'total_revenue' => 50000.00
        ];

        $this->orderRepository
            ->shouldReceive('getOrderAnalytics')
            ->once()
            ->andReturn($analytics);

        $result = $this->orderService->getOrderAnalytics();

        $this->assertEquals($analytics, $result);
    }

    #[Test]
    public function it_validates_order_data_before_creation()
    {
        $invalidOrderData = [
            'buyer_id' => null, // Invalid - required
            'seller_id' => $this->seller->id,
            'items' => [] // Invalid - empty items
        ];

        $this->expectException(\Exception::class);

        $this->orderService->createOrder($invalidOrderData);
    }

    #[Test]
    public function it_can_cancel_pending_order()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING
        ]);

        $cancelledOrder = $order->replicate();
        $cancelledOrder->status = Order::STATUS_CANCELLED;

        $this->orderRepository
            ->shouldReceive('find')
            ->once()
            ->with($order->id)
            ->andReturn($order);

        $this->orderRepository
            ->shouldReceive('updateOrderStatus')
            ->once()
            ->with($order->id, Order::STATUS_CANCELLED, Mockery::type('array'))
            ->andReturn($cancelledOrder);

        $result = $this->orderService->cancelOrder($order->id, 'Customer request');

        $this->assertEquals(Order::STATUS_CANCELLED, $result->status);
    }

    #[Test]
    public function it_cannot_cancel_non_pending_order()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_SHIPPED
        ]);

        $this->orderRepository
            ->shouldReceive('find')
            ->once()
            ->with($order->id)
            ->andReturn($order);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only pending orders can be cancelled');

        $this->orderService->cancelOrder($order->id, 'Test reason');
    }

    #[Test]
    public function it_can_approve_order()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING
        ]);

        $approvedOrder = $order->replicate();
        $approvedOrder->status = Order::STATUS_ACCEPTED;

        $this->orderRepository
            ->shouldReceive('updateOrderStatus')
            ->once()
            ->with($order->id, Order::STATUS_ACCEPTED, Mockery::type('array'))
            ->andReturn($approvedOrder);

        $result = $this->orderService->approveOrder($order->id, $this->seller->id);

        $this->assertEquals(Order::STATUS_ACCEPTED, $result->status);
    }

    #[Test]
    public function it_can_reject_order()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING
        ]);

        $rejectedOrder = $order->replicate();
        $rejectedOrder->status = Order::STATUS_REJECTED;

        $this->orderRepository
            ->shouldReceive('updateOrderStatus')
            ->once()
            ->with($order->id, Order::STATUS_REJECTED, Mockery::type('array'))
            ->andReturn($rejectedOrder);

        $result = $this->orderService->rejectOrder($order->id, $this->seller->id, 'Out of stock');

        $this->assertEquals(Order::STATUS_REJECTED, $result->status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
