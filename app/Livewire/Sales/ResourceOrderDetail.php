<?php

namespace App\Livewire\Sales;

use App\Interfaces\Services\ResourceOrderServiceInterface;
use App\Models\ResourceOrder;
use Livewire\Component;

class ResourceOrderDetail extends Component
{


    public $resourceOrderId;
    public $resourceOrder;

    public function mount($id)
    {
        // Check if user is from Aktina company
        if (!auth()->user() || auth()->user()->company_name !== 'Aktina') {
            abort(403, 'Access denied. This section is only available to Aktina employees.');
        }

        $this->resourceOrderId = $id;
        $this->loadResourceOrder();
    }

    public function loadResourceOrder()
    {
        $resourceOrderService = app(ResourceOrderServiceInterface::class);
        $this->resourceOrder = $resourceOrderService->getResourceOrderById($this->resourceOrderId);

        if (!$this->resourceOrder) {
            session()->flash('error', 'Resource Order not found.');
            return redirect()->route('resource-orders.index');
        }
    }

    public function acceptResourceOrder()
    {
        try {
            $resourceOrderService = app(ResourceOrderServiceInterface::class);
            $result = $resourceOrderService->acceptResourceOrder($this->resourceOrderId);

            if ($result) {
                session()->flash('success', 'Resource order accepted successfully!');
                $this->loadResourceOrder();
            } else {
                session()->flash('error', 'Failed to accept resource order.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function completeResourceOrder()
    {
        try {
            $resourceOrderService = app(ResourceOrderServiceInterface::class);
            $result = $resourceOrderService->completeResourceOrder($this->resourceOrderId);

            if ($result) {
                session()->flash('success', 'Resource order completed successfully!');
                $this->loadResourceOrder();
            } else {
                session()->flash('error', 'Failed to complete resource order.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sales.resource-order-detail');
    }
}
