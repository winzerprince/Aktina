<?php

namespace App\Livewire\Sales;

use App\Interfaces\Services\OrderServiceInterface;
use App\Models\Order;
use App\Models\Employee;
use Livewire\Component;
use Mary\Traits\Toast;

class OrderDetail extends Component
{
    use Toast;

    public $orderId;
    public $order;
    public $availableEmployees = [];
    public $selectedEmployees = [];

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrder();
        $this->loadAvailableEmployees();
    }

    public function loadOrder()
    {
        $orderService = app(OrderServiceInterface::class);
        $this->order = $orderService->getOrderById($this->orderId);

        if (!$this->order) {
            session()->flash('error', 'Order not found.');
            return redirect()->route('orders.index');
        }
    }

    public function loadAvailableEmployees()
    {
        if ($this->order && $this->order->status === Order::STATUS_PENDING) {
            $this->availableEmployees = Employee::where('status', Employee::STATUS_AVAILABLE)
                                      ->where('current_activity', Employee::ACTIVITY_NONE)
                                      ->get()
                                      ->toArray();
        }
    }

    public function acceptOrder()
    {
        if (empty($this->selectedEmployees)) {
            $this->error('Please select at least one employee to manage this order');
            return;
        }

        try {
            $orderService = app(OrderServiceInterface::class);
            $result = $orderService->acceptOrder($this->orderId);

            if ($result) {
                $this->success('Order accepted successfully!');
                $this->loadOrder();
                $this->loadAvailableEmployees();
            } else {
                $this->error('Failed to accept order.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function completeOrder()
    {
        try {
            $orderService = app(OrderServiceInterface::class);
            $result = $orderService->completeOrder($this->orderId);

            if ($result) {
                $this->success('Order completed successfully!');
                $this->loadOrder();
            } else {
                $this->error('Failed to complete order.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Refresh assigned employees
        $assignedEmployees = [];
        if ($this->order && isset($this->order->employees)) {
            $assignedEmployees = $this->order->employees->toArray();
        }

        return view('livewire.sales.order-detail', [
            'assignedEmployees' => $assignedEmployees
        ]);
    }
}
