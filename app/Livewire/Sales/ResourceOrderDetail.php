<?php

namespace App\Livewire\Sales;

use App\Interfaces\Services\ResourceOrderServiceInterface;
use App\Models\ResourceOrder;
use Livewire\Component;
use Mary\Traits\Toast;

class ResourceOrderDetail extends Component
{
    use Toast;

    public $resourceOrderId;
    public $resourceOrder;

    public function mount($id)
    {
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
                $this->success('Resource order accepted successfully!');
                $this->loadResourceOrder();
            } else {
                $this->error('Failed to accept resource order.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function completeResourceOrder()
    {
        try {
            $resourceOrderService = app(ResourceOrderServiceInterface::class);
            $result = $resourceOrderService->completeResourceOrder($this->resourceOrderId);

            if ($result) {
                $this->success('Resource order completed successfully!');
                $this->loadResourceOrder();
            } else {
                $this->error('Failed to complete resource order.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sales.resource-order-detail');
    }
}
