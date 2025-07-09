<?php

namespace App\Livewire\Retailer;

use App\Interfaces\Services\RetailerOrderServiceInterface;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class RetailerOrderCreate extends Component
{
    // Selected seller
    public $sellerId = null;

    // Order data
    public $items = [];
    public $deliveryAddress = '';
    public $notes = '';
    public $expectedDeliveryDate = null;

    // UI state
    public $step = 1; // 1: Select Seller, 2: Add Items, 3: Review & Submit
    public $availableSellers = [];
    public $availableProducts = [];
    public $selectedProductId = null;
    public $quantity = 1;
    public $errorMessage = '';
    public $successMessage = '';

    // Computed values
    public $totalAmount = 0;

    // Listeners
    protected $listeners = ['productSelected', 'sellerSelected'];

    public function mount()
    {
        // Load available sellers when component mounts
        $this->loadAvailableSellers();

        // Initialize with empty item
        $this->addEmptyItem();
    }

    public function loadAvailableSellers()
    {
        // Get all vendors/sellers
        $this->availableSellers = User::where('role', 'vendor')->get(['id', 'name', 'company_name']);
    }

    public function sellerSelected($sellerId)
    {
        $this->sellerId = $sellerId;

        // Reset items when seller changes
        $this->items = [];
        $this->addEmptyItem();

        // Load products from this seller
        $this->loadSellerProducts();

        // Move to step 2
        $this->step = 2;
    }

    public function loadSellerProducts()
    {
        if (!$this->sellerId) {
            return;
        }

        // Get products from the selected seller
        $this->availableProducts = Product::where('seller_id', $this->sellerId)
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->get(['id', 'name', 'description', 'price']);
    }

    public function addEmptyItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
        ];
    }

    public function productSelected($index, $productId)
    {
        $this->items[$index]['product_id'] = $productId;
        $this->calculateTotal();
    }

    public function updateQuantity($index, $quantity)
    {
        $this->items[$index]['quantity'] = max(1, $quantity);
        $this->calculateTotal();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();

        // Always ensure at least one item
        if (empty($this->items)) {
            $this->addEmptyItem();
        }
    }

    public function calculateTotal()
    {
        $this->totalAmount = 0;

        foreach ($this->items as $item) {
            if (empty($item['product_id'])) {
                continue;
            }

            $product = $this->availableProducts->firstWhere('id', $item['product_id']);
            if ($product) {
                $this->totalAmount += $product->price * $item['quantity'];
            }
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            if (!$this->sellerId) {
                $this->errorMessage = 'Please select a seller to continue.';
                return;
            }
            $this->step = 2;
            $this->errorMessage = '';
        } elseif ($this->step === 2) {
            // Validate items
            $hasValidItems = false;
            foreach ($this->items as $item) {
                if (!empty($item['product_id']) && $item['quantity'] > 0) {
                    $hasValidItems = true;
                    break;
                }
            }

            if (!$hasValidItems) {
                $this->errorMessage = 'Please add at least one product to your order.';
                return;
            }

            // Clean up empty items
            $this->items = collect($this->items)->filter(function ($item) {
                return !empty($item['product_id']) && $item['quantity'] > 0;
            })->values()->toArray();

            $this->calculateTotal();
            $this->step = 3;
            $this->errorMessage = '';
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->errorMessage = '';
        }
    }

    public function submitOrder()
    {
        try {
            // Validate delivery address
            if (empty($this->deliveryAddress)) {
                $this->errorMessage = 'Please provide a delivery address.';
                return;
            }

            // Prepare order data
            $orderData = [
                'seller_id' => $this->sellerId,
                'items' => $this->items,
                'delivery_address' => $this->deliveryAddress,
                'notes' => $this->notes,
                'expected_delivery_date' => $this->expectedDeliveryDate,
            ];

            // Create the order
            $retailerOrderService = app(RetailerOrderServiceInterface::class);
            $order = $retailerOrderService->createOrder(auth()->user(), $orderData);

            // Reset form and show success message
            $this->reset(['items', 'deliveryAddress', 'notes', 'expectedDeliveryDate', 'step', 'errorMessage']);
            $this->successMessage = "Order #{$order->id} has been created successfully!";
            $this->step = 1;
            $this->addEmptyItem();

            // Emit event for parent components
            $this->dispatch('orderCreated', $order->id);

        } catch (ValidationException $e) {
            $this->errorMessage = collect($e->errors())->flatten()->first();
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred while creating your order. Please try again.';
            \Illuminate\Support\Facades\Log::error('Order creation error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.retailer.retailer-order-create', [
            'sellers' => $this->availableSellers,
            'products' => $this->availableProducts,
        ]);
    }
}
