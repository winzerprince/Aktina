<?php

namespace App\Livewire\Sales;

use App\Interfaces\Services\OrderServiceInterface;
use App\Models\Product;
use App\Models\User;
use Livewire\Component;
use Mary\Traits\Toast;

class OrderCreate extends Component
{
    use Toast;

    public $buyers = [];
    public $sellers = [];
    public $products = [];

    public $selectedBuyer = null;
    public $selectedSeller = null;
    public $selectedItems = [
        ['product_id' => '', 'quantity' => 1, 'price' => 0]
    ];

    public $totalPrice = 0;
    public $stockLevels = [];

    public function mount()
    {
        // Get buyers (retailers and vendors)
        $this->buyers = User::whereIn('role', ['retailer', 'vendor'])->get();

        // Get sellers (admin, production managers)
        $this->sellers = User::whereIn('role', ['admin', 'production_manager'])
                         ->where('company_name', 'Aktina')
                         ->get();

        // Available products
        $this->products = Product::all();
    }

    public function addItem()
    {
        $this->selectedItems[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
    }

    public function removeItem($index)
    {
        if (count($this->selectedItems) > 1) {
            unset($this->selectedItems[$index]);
            $this->selectedItems = array_values($this->selectedItems);
            $this->calculateTotal();
        } else {
            $this->error('Order must have at least one item.');
        }
    }

    public function updatedSelectedItems()
    {
        $this->calculateTotal();
        $this->checkStockLevels();
    }

    public function calculateTotal()
    {
        $this->totalPrice = 0;
        foreach ($this->selectedItems as $item) {
            if (isset($item['product_id']) && !empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $itemPrice = $product->price * $item['quantity'];
                    $this->totalPrice += $itemPrice;

                    // Update the price in the item for display
                    foreach ($this->selectedItems as $key => $selectedItem) {
                        if ($selectedItem['product_id'] == $item['product_id']) {
                            $this->selectedItems[$key]['price'] = $product->price;
                        }
                    }
                }
            }
        }
    }

    public function checkStockLevels()
    {
        $items = $this->cleanupItems($this->selectedItems);
        $orderService = app(OrderServiceInterface::class);
        $this->stockLevels = $orderService->checkStockAvailability($items);
    }

    private function cleanupItems($items)
    {
        // Filter out incomplete items and format for processing
        $cleanedItems = [];
        foreach ($items as $item) {
            if (!empty($item['product_id']) && $item['quantity'] > 0) {
                $cleanedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ];
            }
        }
        return $cleanedItems;
    }

    public function createOrder()
    {
        $this->validate([
            'selectedBuyer' => 'required',
            'selectedSeller' => 'required',
            'selectedItems.*.product_id' => 'required',
            'selectedItems.*.quantity' => 'required|numeric|min:1',
        ], [
            'selectedBuyer.required' => 'Please select a buyer.',
            'selectedSeller.required' => 'Please select a seller.',
            'selectedItems.*.product_id.required' => 'Please select a product.',
            'selectedItems.*.quantity.required' => 'Please enter a quantity.',
            'selectedItems.*.quantity.numeric' => 'Quantity must be a number.',
            'selectedItems.*.quantity.min' => 'Quantity must be at least 1.',
        ]);

        // Check stock availability first
        $this->checkStockLevels();

        // Check if any items are out of stock
        $outOfStock = false;
        foreach ($this->stockLevels as $stockCheck) {
            if (!$stockCheck['in_stock']) {
                $outOfStock = true;
                break;
            }
        }

        if ($outOfStock) {
            $this->error('Cannot create order. Some items are out of stock.');
            return;
        }

        try {
            $orderService = app(OrderServiceInterface::class);

            $orderData = [
                'buyer_id' => $this->selectedBuyer,
                'seller_id' => $this->selectedSeller,
                'price' => $this->totalPrice,
                'items' => $this->cleanupItems($this->selectedItems),
                'status' => 'pending',
            ];

            $order = $orderService->processNewOrder($orderData);

            if ($order) {
                $this->success('Order created successfully!');
                return redirect()->route('orders.show', $order->id);
            } else {
                $this->error('Failed to create order.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sales.order-create', [
            'buyerOptions' => $this->buyers,
            'sellerOptions' => $this->sellers,
            'productOptions' => $this->products,
        ]);
    }
}
