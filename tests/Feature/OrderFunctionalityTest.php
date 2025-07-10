<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Employee;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Sales\OrderDetail;

class OrderFunctionalityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $seller;
    protected $buyer;
    protected $products;
    protected $employees;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->seller = User::factory()->create([
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'company_name' => 'Aktina',
            'verified' => true,
        ]);

        $this->buyer = User::factory()->create([
            'email' => 'vendor@gmail.com',
            'role' => 'vendor',
            'company_name' => 'Vendor Company',
            'verified' => true,
        ]);

        // Create test products
        $this->products = collect([
            Product::factory()->create([
                'name' => 'Test Product 1',
                'msrp' => 100.00,
                'owner_id' => $this->seller->id,
            ]),
            Product::factory()->create([
                'name' => 'Test Product 2',
                'msrp' => 200.00,
                'owner_id' => $this->seller->id,
            ]),
        ]);

        // Create test employees
        $this->employees = collect([
            Employee::factory()->create([
                'name' => 'Test Employee 1',
                'role' => 'worker',
                'status' => Employee::STATUS_AVAILABLE,
                'current_activity' => Employee::ACTIVITY_NONE,
            ]),
            Employee::factory()->create([
                'name' => 'Test Employee 2',
                'role' => 'supervisor',
                'status' => Employee::STATUS_AVAILABLE,
                'current_activity' => Employee::ACTIVITY_NONE,
            ]),
        ]);
    }

    /** @test */
    public function test_order_creation_with_valid_data()
    {
        $orderData = [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'items' => [
                [
                    'product_id' => $this->products->first()->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $this->products->last()->id,
                    'quantity' => 1,
                ],
            ],
            'price' => 400.00, // (100 * 2) + (200 * 1)
            'status' => Order::STATUS_PENDING,
        ];

        $orderService = app(OrderService::class);
        $order = $orderService->processNewOrder($orderData);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        $this->assertEquals(400.00, $order->price);
        $this->assertEquals($this->buyer->id, $order->buyer_id);
        $this->assertEquals($this->seller->id, $order->seller_id);

        $items = $order->getItemsAsArray();
        $this->assertCount(2, $items);
        $this->assertEquals($this->products->first()->id, $items[0]['product_id']);
        $this->assertEquals(2, $items[0]['quantity']);
    }

    /** @test */
    public function test_order_acceptance_updates_status()
    {
        // Create an order
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_PENDING,
            'price' => 300.00,
            'items' => [
                [
                    'product_id' => $this->products->first()->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $orderService = app(OrderService::class);
        $result = $orderService->acceptOrder($order->id);

        $this->assertTrue($result);

        $order->refresh();
        $this->assertEquals(Order::STATUS_ACCEPTED, $order->status);
    }

    /** @test */
    public function test_employee_assignment_to_order()
    {
        // Create an accepted order
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_ACCEPTED,
            'price' => 150.00,
            'items' => [
                [
                    'product_id' => $this->products->first()->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $orderService = app(OrderService::class);
        $employeeIds = $this->employees->pluck('id')->toArray();

        $result = $orderService->assignEmployeesToOrder($order->id, $employeeIds);

        $this->assertTrue($result);

        // Check employees are assigned
        foreach ($this->employees as $employee) {
            $employee->refresh();
            $this->assertEquals(Employee::STATUS_UNAVAILABLE, $employee->status);
            $this->assertEquals(Employee::ACTIVITY_ORDER, $employee->current_activity);
            $this->assertEquals($order->id, $employee->order_id);
        }
    }

    /** @test */
    public function test_order_completion_workflow()
    {
        // Create an accepted order with assigned employees
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_ACCEPTED,
            'price' => 250.00,
            'items' => [
                [
                    'product_id' => $this->products->last()->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        // Assign employees
        foreach ($this->employees as $employee) {
            $employee->assignToOrder($order);
        }

        $orderService = app(OrderService::class);
        $result = $orderService->completeOrder($order->id);

        $this->assertTrue($result);

        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETE, $order->status);
    }

    /** @test */
    public function test_livewire_order_detail_component_loads_correctly()
    {
        // Create an order
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_PENDING,
            'price' => 100.00,
            'items' => [
                [
                    'product_id' => (string) $this->products->first()->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->actingAs($this->seller);

        $component = Livewire::test(OrderDetail::class, ['id' => $order->id])
            ->assertSet('orderId', $order->id)
            ->assertSet('order.id', $order->id)
            ->assertSet('order.status', Order::STATUS_PENDING)
            ->assertSee('Order #' . $order->id)
            ->assertSee($this->products->first()->name)
            ->assertSee('$100.00');

        $this->assertTrue($component->get('order') instanceof Order);
    }

    /** @test */
    public function test_livewire_accept_order_functionality()
    {
        // Create a pending order
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_PENDING,
            'price' => 200.00,
            'items' => [
                [
                    'product_id' => (string) $this->products->last()->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->actingAs($this->seller);

        Livewire::test(OrderDetail::class, ['id' => $order->id])
            ->call('acceptOrder')
            ->assertHasNoErrors()
            ->assertSet('order.status', Order::STATUS_ACCEPTED);

        $order->refresh();
        $this->assertEquals(Order::STATUS_ACCEPTED, $order->status);
    }

    /** @test */
    public function test_livewire_employee_assignment_functionality()
    {
        // Create an accepted order
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_ACCEPTED,
            'price' => 300.00,
            'items' => [
                [
                    'product_id' => (string) $this->products->first()->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $this->actingAs($this->seller);

        $component = Livewire::test(OrderDetail::class, ['id' => $order->id])
            ->set('selectedEmployees', $this->employees->pluck('id')->toArray())
            ->call('assignEmployees')
            ->assertHasNoErrors();

        // Verify employees were assigned
        foreach ($this->employees as $employee) {
            $employee->refresh();
            $this->assertEquals(Employee::STATUS_UNAVAILABLE, $employee->status);
            $this->assertEquals(Employee::ACTIVITY_ORDER, $employee->current_activity);
            $this->assertEquals($order->id, $employee->order_id);
        }
    }

    /** @test */
    public function test_order_item_pricing_display()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_PENDING,
            'price' => 500.00,
            'items' => [
                [
                    'product_id' => (string) $this->products->first()->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => (string) $this->products->last()->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->actingAs($this->seller);

        Livewire::test(OrderDetail::class, ['id' => $order->id])
            ->assertSee('Test Product 1')
            ->assertSee('Test Product 2')
            ->assertSee('$100.00') // Product 1 price
            ->assertSee('$200.00') // Product 2 price and total for qty 1
            ->assertSee('$500.00'); // Total order price
    }

    /** @test */
    public function test_order_cannot_be_accepted_twice()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => Order::STATUS_ACCEPTED, // Already accepted
            'price' => 100.00,
            'items' => [
                [
                    'product_id' => (string) $this->products->first()->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $orderService = app(OrderService::class);
        $result = $orderService->acceptOrder($order->id);

        // Should still return true but status shouldn't change
        $this->assertTrue($result);

        $order->refresh();
        $this->assertEquals(Order::STATUS_ACCEPTED, $order->status);
    }

    /** @test */
    public function test_complete_order_flow_integration()
    {
        $this->actingAs($this->seller);

        // Step 1: Create order
        $orderData = [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'items' => [
                [
                    'product_id' => $this->products->first()->id,
                    'quantity' => 2,
                ],
            ],
            'price' => 200.00,
            'status' => Order::STATUS_PENDING,
        ];

        $orderService = app(OrderService::class);
        $order = $orderService->processNewOrder($orderData);

        $this->assertEquals(Order::STATUS_PENDING, $order->status);

        // Step 2: Accept order via Livewire
        $component = Livewire::test(OrderDetail::class, ['id' => $order->id])
            ->call('acceptOrder')
            ->assertHasNoErrors();

        $order->refresh();
        $this->assertEquals(Order::STATUS_ACCEPTED, $order->status);

        // Step 3: Assign employees via Livewire
        $component->set('selectedEmployees', [$this->employees->first()->id])
            ->call('assignEmployees')
            ->assertHasNoErrors();

        $this->employees->first()->refresh();
        $this->assertEquals(Employee::STATUS_UNAVAILABLE, $this->employees->first()->status);

        // Step 4: Complete order
        $component->call('completeOrder')
            ->assertHasNoErrors();

        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETE, $order->status);
    }
}
