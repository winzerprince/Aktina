<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Livewire\Orders\OrderList;
use App\Models\Order;
use App\Models\User;
use App\Models\Resource;
use App\Services\EnhancedOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class OrderListTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $retailer;
    protected $vendor;
    protected $productionManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with different roles
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'company_name' => 'Aktina Technologies'
        ]);

        $this->retailer = User::factory()->create([
            'role' => 'retailer',
            'company_name' => 'Test Retail Co.'
        ]);

        $this->vendor = User::factory()->create([
            'role' => 'vendor',
            'company_name' => 'Test Vendor Inc.'
        ]);

        $this->productionManager = User::factory()->create([
            'role' => 'production_manager',
            'company_name' => 'Aktina Technologies'
        ]);

        // Create test resources
        Resource::factory()->count(5)->create();
    }

    #[Test]
    public function it_renders_successfully()
    {
        $this->actingAs($this->admin);

        Livewire::test(OrderList::class)
            ->assertStatus(200)
            ->assertSee('Order Management')
            ->assertSee('Create Order')
            ->assertSee('Reset Filters');
    }

    #[Test]
    public function it_displays_orders_for_admin()
    {
        $this->actingAs($this->admin);

        // Create test orders
        $orders = Order::factory()->count(3)->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->assertSee('Order #' . $orders[0]->id)
            ->assertSee('Order #' . $orders[1]->id)
            ->assertSee('Order #' . $orders[2]->id);
    }

    #[Test]
    public function it_filters_orders_by_user_role()
    {
        $this->actingAs($this->retailer);

        // Create orders - some involving the retailer, some not
        $retailerOrder = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $otherOrder = Order::factory()->create([
            'buyer_id' => $this->admin->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->assertSee('Order #' . $retailerOrder->id)
            ->assertDontSee('Order #' . $otherOrder->id);
    }

    #[Test]
    public function it_can_filter_orders_by_status()
    {
        $this->actingAs($this->admin);

        $pendingOrder = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $completedOrder = Order::factory()->create([
            'status' => Order::STATUS_COMPLETE,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->set('statusFilter', 'pending')
            ->assertSee('Order #' . $pendingOrder->id)
            ->assertDontSee('Order #' . $completedOrder->id);
    }

    #[Test]
    public function it_can_search_orders()
    {
        $this->actingAs($this->admin);

        $searchableOrder = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $otherOrder = Order::factory()->create([
            'buyer_id' => $this->admin->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->set('search', $searchableOrder->id)
            ->assertSee('Order #' . $searchableOrder->id)
            ->assertDontSee('Order #' . $otherOrder->id);
    }

    #[Test]
    public function it_can_sort_orders_by_different_fields()
    {
        $this->actingAs($this->admin);

        // Create orders with different prices
        $cheapOrder = Order::factory()->create([
            'price' => 100.00,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $expensiveOrder = Order::factory()->create([
            'price' => 500.00,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        // Test sorting by price ascending
        $component = Livewire::test(OrderList::class)
            ->call('sortBy', 'price');

        $this->assertEquals('price', $component->get('sortBy'));
        $this->assertEquals('asc', $component->get('sortDirection'));

        // Test sorting by price descending
        $component->call('sortBy', 'price');
        $this->assertEquals('desc', $component->get('sortDirection'));
    }

    #[Test]
    public function it_can_view_order_details()
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->call('viewOrder', $order->id)
            ->assertSet('selectedOrder.id', $order->id);
    }

    #[Test]
    public function it_can_open_create_order_modal()
    {
        $this->actingAs($this->admin);

        Livewire::test(OrderList::class)
            ->call('showCreateOrderModal')
            ->assertSet('showCreateModal', true)
            ->assertSet('buyer_id', '')
            ->assertSet('seller_id', '')
            ->assertSet('items', []);
    }

    #[Test]
    public function it_can_close_create_order_modal()
    {
        $this->actingAs($this->admin);

        Livewire::test(OrderList::class)
            ->set('showCreateModal', true)
            ->call('closeCreateModal')
            ->assertSet('showCreateModal', false);
    }

    #[Test]
    public function it_can_add_items_to_order()
    {
        $this->actingAs($this->admin);

        $resource = Resource::factory()->create();

        Livewire::test(OrderList::class)
            ->set('newItem.resource_id', $resource->id)
            ->set('newItem.quantity', 5)
            ->set('newItem.unit_price', 100.00)
            ->call('addItem')
            ->assertCount('items', 1)
            ->assertSet('items.0.resource_id', $resource->id)
            ->assertSet('items.0.quantity', 5)
            ->assertSet('items.0.unit_price', 100.00);
    }

    #[Test]
    public function it_can_remove_items_from_order()
    {
        $this->actingAs($this->admin);

        $resource = Resource::factory()->create();

        Livewire::test(OrderList::class)
            ->set('items', [
                [
                    'resource_id' => $resource->id,
                    'resource_name' => $resource->name,
                    'quantity' => 5,
                    'unit_price' => 100.00,
                    'total_price' => 500.00
                ]
            ])
            ->call('removeItem', 0)
            ->assertCount('items', 0);
    }

    #[Test]
    public function it_validates_order_creation_data()
    {
        $this->actingAs($this->admin);

        Livewire::test(OrderList::class)
            ->set('buyer_id', '')
            ->set('seller_id', '')
            ->set('items', [])
            ->call('createOrder')
            ->assertHasErrors([
                'buyer_id' => 'required',
                'seller_id' => 'required',
                'items' => 'required'
            ]);
    }

    #[Test]
    public function it_can_edit_pending_orders()
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [
                ['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00]
            ]
        ]);

        Livewire::test(OrderList::class)
            ->call('showEditOrderModal', $order->id)
            ->assertSet('showEditModal', true)
            ->assertSet('selectedOrder.id', $order->id)
            ->assertSet('buyer_id', $order->buyer_id)
            ->assertSet('seller_id', $order->seller_id);
    }

    #[Test]
    public function it_cannot_edit_non_pending_orders()
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'status' => Order::STATUS_ACCEPTED,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $component = Livewire::test(OrderList::class)
            ->call('showEditOrderModal', $order->id)
            ->assertSet('showEditModal', false);

        // Assert that the modal is not shown and order is not loaded
        $this->assertFalse($component->get('showEditModal'));
        $this->assertNull($component->get('selectedOrder'));
    }

    #[Test]
    public function it_can_delete_pending_orders()
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->call('deleteOrder', $order->id);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    #[Test]
    public function it_cannot_delete_non_pending_orders()
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'status' => 'accepted',
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->call('deleteOrder', $order->id);

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    #[Test]
    public function it_resets_filters_correctly()
    {
        $this->actingAs($this->admin);

        Livewire::test(OrderList::class)
            ->set('statusFilter', 'pending')
            ->set('userFilter', $this->retailer->id)
            ->set('search', 'test')
            ->set('sortBy', 'price')
            ->call('resetFilters')
            ->assertSet('statusFilter', 'all')
            ->assertSet('userFilter', '')
            ->assertSet('search', '')
            ->assertSet('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc');
    }

    #[Test]
    public function it_returns_correct_status_colors()
    {
        $this->actingAs($this->admin);

        $component = new OrderList();

        $this->assertEquals('bg-yellow-100 text-yellow-800', $component->getStatusColor('pending'));
        $this->assertEquals('bg-blue-100 text-blue-800', $component->getStatusColor('accepted'));
        $this->assertEquals('bg-green-100 text-green-800', $component->getStatusColor('completed'));
        $this->assertEquals('bg-red-100 text-red-800', $component->getStatusColor('rejected'));
        $this->assertEquals('bg-gray-100 text-gray-800', $component->getStatusColor('unknown'));
    }

    #[Test]
    public function it_calculates_total_value_correctly()
    {
        $this->actingAs($this->admin);

        $items = [
            ['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00, 'total_price' => 500.00],
            ['resource_id' => 2, 'quantity' => 3, 'unit_price' => 50.00, 'total_price' => 150.00]
        ];

        $component = Livewire::test(OrderList::class)
            ->set('items', $items);

        $this->assertEquals(650.00, $component->get('totalValue'));
    }

    #[Test]
    public function it_paginates_orders_correctly()
    {
        $this->actingAs($this->admin);

        // Create more orders than the pagination limit
        Order::factory()->count(20)->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $component = Livewire::test(OrderList::class);

        // Should show pagination links
        $this->assertCount(15, $component->get('orders')); // Assuming 15 per page
    }

    #[Test]
    public function it_displays_company_names_correctly()
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        Livewire::test(OrderList::class)
            ->assertSee($this->retailer->company_name)
            ->assertSee($this->vendor->company_name);
    }
}
