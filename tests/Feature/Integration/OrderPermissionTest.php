<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class OrderPermissionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $retailer1;
    protected $retailer2;
    protected $vendor1;
    protected $vendor2;
    protected $productionManager;
    protected $hrManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'company_name' => 'Aktina Technologies'
        ]);

        $this->retailer1 = User::factory()->create([
            'role' => 'retailer',
            'company_name' => 'Retail Co 1'
        ]);

        $this->retailer2 = User::factory()->create([
            'role' => 'retailer',
            'company_name' => 'Retail Co 2'
        ]);

        $this->vendor1 = User::factory()->create([
            'role' => 'vendor',
            'company_name' => 'Vendor Co 1'
        ]);

        $this->vendor2 = User::factory()->create([
            'role' => 'vendor',
            'company_name' => 'Vendor Co 2'
        ]);

        $this->productionManager = User::factory()->create([
            'role' => 'production_manager',
            'company_name' => 'Aktina Technologies'
        ]);

        $this->hrManager = User::factory()->create([
            'role' => 'hr_manager',
            'company_name' => 'Aktina Technologies'
        ]);
    }

    #[Test]
    public function admin_can_view_all_orders()
    {
        $order1 = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        $order2 = Order::factory()->create([
            'buyer_id' => $this->retailer2->id,
            'seller_id' => $this->vendor2->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders'));

        $response->assertStatus(200);
        $response->assertSee('Order #' . $order1->id);
        $response->assertSee('Order #' . $order2->id);
    }

    #[Test]
    public function retailer_can_only_view_their_orders()
    {
        $retailer1Order = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        $retailer2Order = Order::factory()->create([
            'buyer_id' => $this->retailer2->id,
            'seller_id' => $this->vendor1->id
        ]);

        $response = $this->actingAs($this->retailer1)
            ->get(route('retailer.orders'));

        $response->assertStatus(200);
        $response->assertSee('Order #' . $retailer1Order->id);
        $response->assertDontSee('Order #' . $retailer2Order->id);
    }

    #[Test]
    public function vendor_can_only_view_orders_they_are_selling()
    {
        $vendor1Order = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        $vendor2Order = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor2->id
        ]);

        $response = $this->actingAs($this->vendor1)
            ->get(route('vendor.orders'));

        $response->assertStatus(200);
        $response->assertSee('Order #' . $vendor1Order->id);
        $response->assertDontSee('Order #' . $vendor2Order->id);
    }

    #[Test]
    public function retailer_cannot_view_other_retailer_order_details()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->retailer2->id, // Different retailer
            'seller_id' => $this->vendor1->id
        ]);

        $response = $this->actingAs($this->retailer1)
            ->get(route('orders.show', $order));

        $response->assertStatus(403); // Forbidden
    }

    #[Test]
    public function vendor_cannot_view_other_vendor_order_details()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor2->id // Different vendor
        ]);

        $response = $this->actingAs($this->vendor1)
            ->get(route('orders.show', $order));

        $response->assertStatus(403); // Forbidden
    }

    #[Test]
    public function production_manager_can_view_all_orders()
    {
        $order1 = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        $order2 = Order::factory()->create([
            'buyer_id' => $this->retailer2->id,
            'seller_id' => $this->vendor2->id
        ]);

        $response = $this->actingAs($this->productionManager)
            ->get(route('production.orders'));

        $response->assertStatus(200);
        $response->assertSee('Order #' . $order1->id);
        $response->assertSee('Order #' . $order2->id);
    }

    #[Test]
    public function only_admin_and_production_manager_can_create_orders()
    {
        $orderData = [
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id,
            'items' => [['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00]]
        ];

        // Admin can create
        $response = $this->actingAs($this->admin)
            ->post(route('orders.store'), $orderData);
        $response->assertStatus(302); // Redirect on success

        // Production manager can create
        $response = $this->actingAs($this->productionManager)
            ->post(route('orders.store'), $orderData);
        $response->assertStatus(302);

        // Retailer cannot create
        $response = $this->actingAs($this->retailer1)
            ->post(route('orders.store'), $orderData);
        $response->assertStatus(403);

        // Vendor cannot create
        $response = $this->actingAs($this->vendor1)
            ->post(route('orders.store'), $orderData);
        $response->assertStatus(403);

        // HR Manager cannot create
        $response = $this->actingAs($this->hrManager)
            ->post(route('orders.store'), $orderData);
        $response->assertStatus(403);
    }

    #[Test]
    public function only_vendor_can_accept_their_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Correct vendor can accept
        $response = $this->actingAs($this->vendor1)
            ->post(route('orders.accept', $order));
        $response->assertStatus(302);

        // Reset order status
        $order->update(['status' => Order::STATUS_PENDING]);

        // Different vendor cannot accept
        $response = $this->actingAs($this->vendor2)
            ->post(route('orders.accept', $order));
        $response->assertStatus(403);

        // Retailer cannot accept
        $response = $this->actingAs($this->retailer1)
            ->post(route('orders.accept', $order));
        $response->assertStatus(403);
    }

    #[Test]
    public function only_vendor_can_reject_their_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Correct vendor can reject
        $response = $this->actingAs($this->vendor1)
            ->post(route('orders.reject', $order), ['rejection_reason' => 'Out of stock']);
        $response->assertStatus(302);

        // Reset order status
        $order->update(['status' => Order::STATUS_PENDING]);

        // Different vendor cannot reject
        $response = $this->actingAs($this->vendor2)
            ->post(route('orders.reject', $order), ['rejection_reason' => 'Out of stock']);
        $response->assertStatus(403);
    }

    #[Test]
    public function only_buyer_can_cancel_their_pending_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Correct buyer can cancel
        $response = $this->actingAs($this->retailer1)
            ->post(route('orders.cancel', $order), ['cancellation_reason' => 'Changed mind']);
        $response->assertStatus(302);

        // Reset order status
        $order->update(['status' => Order::STATUS_PENDING]);

        // Different buyer cannot cancel
        $response = $this->actingAs($this->retailer2)
            ->post(route('orders.cancel', $order), ['cancellation_reason' => 'Changed mind']);
        $response->assertStatus(403);

        // Vendor cannot cancel
        $response = $this->actingAs($this->vendor1)
            ->post(route('orders.cancel', $order), ['cancellation_reason' => 'Changed mind']);
        $response->assertStatus(403);
    }

    #[Test]
    public function only_production_manager_can_approve_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_ACCEPTED,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Production manager can approve
        $response = $this->actingAs($this->productionManager)
            ->post(route('orders.approve', $order));
        $response->assertStatus(302);

        // Reset order status
        $order->update(['status' => Order::STATUS_ACCEPTED, 'approver_id' => null]);

        // Admin cannot approve (business rule)
        $response = $this->actingAs($this->admin)
            ->post(route('orders.approve', $order));
        $response->assertStatus(403);

        // Retailer cannot approve
        $response = $this->actingAs($this->retailer1)
            ->post(route('orders.approve', $order));
        $response->assertStatus(403);

        // Vendor cannot approve
        $response = $this->actingAs($this->vendor1)
            ->post(route('orders.approve', $order));
        $response->assertStatus(403);
    }

    #[Test]
    public function only_admin_can_delete_orders()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Admin can delete
        $response = $this->actingAs($this->admin)
            ->delete(route('orders.destroy', $order));
        $response->assertStatus(302);

        // Create new order for other tests
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Production manager cannot delete
        $response = $this->actingAs($this->productionManager)
            ->delete(route('orders.destroy', $order));
        $response->assertStatus(403);

        // Retailer cannot delete
        $response = $this->actingAs($this->retailer1)
            ->delete(route('orders.destroy', $order));
        $response->assertStatus(403);

        // Vendor cannot delete
        $response = $this->actingAs($this->vendor1)
            ->delete(route('orders.destroy', $order));
        $response->assertStatus(403);
    }

    #[Test]
    public function hr_manager_cannot_access_order_functionality()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Cannot view order list
        $response = $this->actingAs($this->hrManager)
            ->get(route('admin.orders'));
        $response->assertStatus(403);

        // Cannot view order details
        $response = $this->actingAs($this->hrManager)
            ->get(route('orders.show', $order));
        $response->assertStatus(403);

        // Cannot create orders
        $response = $this->actingAs($this->hrManager)
            ->post(route('orders.store'), [
                'buyer_id' => $this->retailer1->id,
                'seller_id' => $this->vendor1->id,
                'items' => [['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00]]
            ]);
        $response->assertStatus(403);
    }

    #[Test]
    public function guest_users_cannot_access_order_functionality()
    {
        $order = Order::factory()->create([
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Cannot view order list
        $response = $this->get(route('admin.orders'));
        $response->assertRedirect(route('login'));

        // Cannot view order details
        $response = $this->get(route('orders.show', $order));
        $response->assertRedirect(route('login'));

        // Cannot create orders
        $response = $this->post(route('orders.store'), [
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id,
            'items' => [['resource_id' => 1, 'quantity' => 5, 'unit_price' => 100.00]]
        ]);
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function user_can_only_edit_orders_they_are_involved_in()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        $updateData = [
            'notes' => 'Updated notes',
            'delivery_address' => 'Updated address'
        ];

        // Buyer can edit their order
        $response = $this->actingAs($this->retailer1)
            ->patch(route('orders.update', $order), $updateData);
        $response->assertStatus(302);

        // Seller can edit their order
        $response = $this->actingAs($this->vendor1)
            ->patch(route('orders.update', $order), $updateData);
        $response->assertStatus(302);

        // Other retailer cannot edit
        $response = $this->actingAs($this->retailer2)
            ->patch(route('orders.update', $order), $updateData);
        $response->assertStatus(403);

        // Other vendor cannot edit
        $response = $this->actingAs($this->vendor2)
            ->patch(route('orders.update', $order), $updateData);
        $response->assertStatus(403);
    }

    #[Test]
    public function only_authorized_users_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'buyer_id' => $this->retailer1->id,
            'seller_id' => $this->vendor1->id
        ]);

        // Admin can update status
        $response = $this->actingAs($this->admin)
            ->patch(route('orders.update-status', $order), ['status' => Order::STATUS_ACCEPTED]);
        $response->assertStatus(302);

        // Reset order
        $order->update(['status' => Order::STATUS_PENDING]);

        // Production manager can update status
        $response = $this->actingAs($this->productionManager)
            ->patch(route('orders.update-status', $order), ['status' => Order::STATUS_PROCESSING]);
        $response->assertStatus(302);

        // Reset order
        $order->update(['status' => Order::STATUS_PENDING]);

        // Retailer cannot update status directly
        $response = $this->actingAs($this->retailer1)
            ->patch(route('orders.update-status', $order), ['status' => Order::STATUS_ACCEPTED]);
        $response->assertStatus(403);

        // Vendor cannot update status directly
        $response = $this->actingAs($this->vendor1)
            ->patch(route('orders.update-status', $order), ['status' => Order::STATUS_ACCEPTED]);
        $response->assertStatus(403);
    }
}
