<?php

namespace App\Livewire\Sales;

use App\Interfaces\Services\ResourceOrderServiceInterface;
use App\Models\Resource;
use App\Models\User;
use Livewire\Component;

class ResourceOrderCreate extends Component
{


    public $suppliers = [];
    public $aktinaUsers = [];
    public $resources = [];

    public $selectedSupplier = null;
    public $selectedBuyer = null;
    public $selectedItems = [
        ['resource_id' => '', 'quantity' => 1, 'price' => 0]
    ];

    public $totalPrice = 0;
    public $stockLevels = [];

    public function mount()
    {
        // Check if user is from Aktina company
        if (!auth()->user() || auth()->user()->company_name !== 'Aktina') {
            abort(403, 'Access denied. This section is only available to Aktina employees.');
        }

        // Get suppliers
        $this->suppliers = User::where('role', 'supplier')->get();

        // Get Aktina buyers (admin, production_manager)
        $this->aktinaUsers = User::whereIn('role', ['admin', 'production_manager'])
                            ->where('company_name', 'Aktina')
                            ->get();

        // Available resources
        $this->resources = Resource::all();
    }

    public function addItem()
    {
        $this->selectedItems[] = ['resource_id' => '', 'quantity' => 1, 'price' => 0];
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
            if (isset($item['resource_id']) && !empty($item['resource_id'])) {
                $resource = Resource::find($item['resource_id']);
                if ($resource) {
                    $itemPrice = $resource->price * $item['quantity'];
                    $this->totalPrice += $itemPrice;

                    // Update the price in the item for display
                    foreach ($this->selectedItems as $key => $selectedItem) {
                        if ($selectedItem['resource_id'] == $item['resource_id']) {
                            $this->selectedItems[$key]['price'] = $resource->price;
                        }
                    }
                }
            }
        }
    }

    public function checkStockLevels()
    {
        $items = $this->cleanupItems($this->selectedItems);
        $resourceOrderService = app(ResourceOrderServiceInterface::class);
        $this->stockLevels = $resourceOrderService->checkResourceStockAvailability($items);
    }

    private function cleanupItems($items)
    {
        // Filter out incomplete items and format for processing
        $cleanedItems = [];
        foreach ($items as $item) {
            if (!empty($item['resource_id']) && $item['quantity'] > 0) {
                $cleanedItems[] = [
                    'resource_id' => $item['resource_id'],
                    'quantity' => $item['quantity']
                ];
            }
        }
        return $cleanedItems;
    }

    public function createResourceOrder()
    {
        $this->validate([
            'selectedSupplier' => 'required',
            'selectedBuyer' => 'required',
            'selectedItems.*.resource_id' => 'required',
            'selectedItems.*.quantity' => 'required|numeric|min:1',
        ], [
            'selectedSupplier.required' => 'Please select a supplier.',
            'selectedBuyer.required' => 'Please select an Aktina representative.',
            'selectedItems.*.resource_id.required' => 'Please select a resource.',
            'selectedItems.*.quantity.required' => 'Please enter a quantity.',
            'selectedItems.*.quantity.numeric' => 'Quantity must be a number.',
            'selectedItems.*.quantity.min' => 'Quantity must be at least 1.',
        ]);

        try {
            $resourceOrderService = app(ResourceOrderServiceInterface::class);

            $resourceOrderData = [
                'buyer_id' => $this->selectedBuyer,
                'seller_id' => $this->selectedSupplier,
                'price' => $this->totalPrice,
                'items' => $this->cleanupItems($this->selectedItems),
                'status' => 'pending',
            ];

            $resourceOrder = $resourceOrderService->processNewResourceOrder($resourceOrderData);

            if ($resourceOrder) {
                $this->success('Resource order created successfully!');
                return redirect()->route('resource-orders.show', $resourceOrder->id);
            } else {
                $this->error('Failed to create resource order.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sales.resource-order-create', [
            'supplierOptions' => $this->suppliers,
            'buyerOptions' => $this->aktinaUsers,
            'resourceOptions' => $this->resources,
        ]);
    }
}
