<?php

namespace Tests\Feature\Orders;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $retailer;
    protected $vendor;
    protected $productionManager;

    protected function setUp(): void
    {
        parent::setUp();

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

        Resource::factory()->count(5)->create();
    }

    #[Test]
    public function admin_can_view_order_list()
    {
        $orders = Order::factory()->count(3)->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders'));

        $response->assertStatus(200);
        $response->assertSee('Order Management');
        foreach ($orders as $order) {
            $response->assertSee('Order #' . $order->id);
        }
    }

    #[Test]
    public function retailer_can_only_see_their_orders()
    {
        // Create orders involving the retailer
        $retailerOrder = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        // Create order not involving the retailer
        $otherOrder = Order::factory()->create([
            'buyer_id' => $this->admin->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->retailer)
            ->get(route('retailer.orders'));

        $response->assertStatus(200);
        $response->assertSee('Order #' . $retailerOrder->id);
        $response->assertDontSee('Order #' . $otherOrder->id);
    }

    #[Test]
    public function vendor_can_only_see_their_orders()
    {
        // Create orders involving the vendor
        $vendorOrder = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        // Create order not involving the vendor
        $otherVendor = User::factory()->create(['role' => 'vendor']);
        $otherOrder = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $otherVendor->id
        ]);

        $response = $this->actingAs($this->vendor)
            ->get(route('vendor.orders'));

        $response->assertStatus(200);
        $response->assertSee('Order #' . $vendorOrder->id);
        $response->assertDontSee('Order #' . $otherOrder->id);
    }

    #[Test]
    public function order_creation_requires_authentication()
    {
        $response = $this->post(route('orders.store'), [
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00]]
        ]);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_can_create_order()
    {
        $resource = Resource::factory()->create();

        $orderData = [
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'items' => [
                [
                    'resource_id' => $resource->id,
                    'quantity' => 5,
                    'unit_price' => 100.00
                ]
            ],
            'notes' => 'Test order',
            'delivery_address' => '123 Test St'
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('orders.store'), $orderData);

        $response->assertStatus(302); // Redirect after creation
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'status' => Order::STATUS_PENDING
        ]);
    }

    #[Test]
    public function order_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('orders.store'), []);

        $response->assertSessionHasErrors([
            'buyer_id',
            'seller_id',
            'items'
        ]);
    }

    #[Test]
    public function order_creation_validates_buyer_exists()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('orders.store'), [
                'buyer_id' => 99999, // Non-existent user
                'seller_id' => $this->vendor->id,
                'items' => [['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00]]
            ]);

        $response->assertSessionHasErrors(['buyer_id']);
    }

    #[Test]
    public function order_creation_validates_seller_exists()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('orders.store'), [
                'buyer_id' => $this->retailer->id,
                'seller_id' => 99999, // Non-existent user
                'items' => [['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00]]
            ]);

        $response->assertSessionHasErrors(['seller_id']);
    }

    #[Test]
    public function order_creation_validates_items_structure()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('orders.store'), [
                'buyer_id' => $this->retailer->id,
                'seller_id' => $this->vendor->id,
                'items' => [
                    [
                        'resource_id' => '', // Empty
                        'quantity' => 0, // Invalid
                        'unit_price' => -10 // Invalid
                    ]
                ]
            ]);

        $response->assertSessionHasErrors([
            'items.0.resource_id',
            'items.0.quantity',
            'items.0.unit_price'
        ]);
    }

    #[Test]
    public function admin_can_view_order_details()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id,
            'notes' => 'Test order notes'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('Order #' . $order->id);
        $response->assertSee($this->retailer->company_name);
        $response->assertSee($this->vendor->company_name);
        $response->assertSee('Test order notes');
    }

    #[Test]
    public function user_cannot_view_unauthorized_order_details()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->admin->id, // Not the retailer
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->retailer)
            ->get(route('orders.show', $order));

        $response->assertStatus(403); // Forbidden
    }

    #[Test]
    public function admin_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('orders.update', $order), [
                'status' => Order::STATUS_ACCEPTED
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_ACCEPTED
        ]);
    }

    #[Test]
    public function production_manager_can_approve_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->productionManager)
            ->post(route('orders.approve', $order));

        $response->assertStatus(302);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_ACCEPTED,
            'approver_id' => $this->productionManager->id
        ]);
    }

    #[Test]
    public function vendor_can_accept_their_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->vendor)
            ->post(route('orders.accept', $order));

        $response->assertStatus(302);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_ACCEPTED
        ]);
    }

    #[Test]
    public function vendor_cannot_accept_other_vendor_orders()
    {
        $otherVendor = User::factory()->create(['role' => 'vendor']);

        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $otherVendor->id
        ]);

        $response = $this->actingAs($this->vendor)
            ->post(route('orders.accept', $order));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_delete_pending_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('orders.destroy', $order));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    #[Test]
    public function admin_cannot_delete_non_pending_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_SHIPPED,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('orders.destroy', $order));

        $response->assertStatus(400); // Bad request
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    #[Test]
    public function order_status_transitions_are_logged()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $this->actingAs($this->admin)
            ->patch(route('orders.update', $order), [
                'status' => Order::STATUS_ACCEPTED
            ]);

        // Check if status change is logged (assuming you have activity logging)
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $order->id,
            'subject_type' => Order::class,
            'description' => 'Order status updated'
        ]);
    }

    #[Test]
    public function order_displays_correct_company_information()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->retailer->id,
            'seller_id' => $this->vendor->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('orders.show', $order));

        $response->assertSee($this->retailer->company_name); // Buyer company
        $response->assertSee($this->vendor->company_name);   // Seller company
        $response->assertSee($this->retailer->name);         // Individual names as secondary
        $response->assertSee($this->vendor->name);
    }

    #[Test]
    public function order_filtering_works_correctly()
    {
        // Create orders with different statuses
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

        // Test status filtering
        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders') . '?status=pending');

        $response->assertSee('Order #' . $pendingOrder->id);
        $response->assertDontSee('Order #' . $completedOrder->id);
    }
}
