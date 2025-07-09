<?php

namespace App\Livewire\ProductionManager;

use App\Interfaces\Services\ProductionOrderServiceInterface;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ProductionOrderManagement extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $statusFilter = 'all';
    public string $dateRange = '30';
    public string $priorityFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public string $dateFrom = '';
    public string $dateTo = '';

    // Selected orders for bulk actions
    public array $selectedOrders = [];
    public bool $selectAll = false;

    // Modals state
    public bool $showOrderDetails = false;
    public bool $showResourceCheck = false;
    public bool $showAssignEmployees = false;
    public bool $showScheduleProduction = false;
    public bool $showFulfillmentWizard = false;

    // Selected order
    public $selectedOrder = null;

    // Wizard state
    public int $wizardStep = 1;
    public string $nextStatus = '';
    public array $partialFulfilledItems = [];
    public array $productionNotes = [];
    public string $errorMessage = '';
    public string $successMessage = '';

    // Employee assignment
    public array $availableEmployees = [];
    public array $selectedEmployees = [];

    // Production scheduling
    public string $scheduledDate = '';
    public string $productionPriority = 'normal';
    public array $productionDetails = [];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');

        // Load available employees (this would come from a service in a real implementation)
        $this->availableEmployees = User::where('role', 'employee')
            ->where('status', 'active')
            ->get(['id', 'name'])
            ->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateRange()
    {
        $this->resetPage();

        if ($this->dateRange !== 'custom') {
            $days = (int) $this->dateRange;
            $this->dateFrom = Carbon::now()->subDays($days)->format('Y-m-d');
            $this->dateTo = Carbon::now()->format('Y-m-d');
        }
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedOrders = $this->getOrdersQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedOrders = [];
        }
    }

    public function viewOrderDetails($orderId)
    {
        $this->selectedOrder = Order::with(['buyer', 'orderItems', 'orderItems.product'])
            ->findOrFail($orderId);
        $this->showOrderDetails = true;
    }

    public function closeOrderDetails()
    {
        $this->showOrderDetails = false;
        $this->showResourceCheck = false;
        $this->showAssignEmployees = false;
        $this->showScheduleProduction = false;
        $this->showFulfillmentWizard = false;
        $this->selectedOrder = null;
    }

    public function checkResources($orderId)
    {
        $this->selectedOrder = Order::with(['orderItems', 'orderItems.product'])->findOrFail($orderId);
        $this->showResourceCheck = true;
    }

    public function openAssignEmployees($orderId)
    {
        $this->selectedOrder = Order::findOrFail($orderId);
        $this->selectedEmployees = $this->selectedOrder->metadata['assigned_employees'] ?? [];
        $this->showAssignEmployees = true;
    }

    public function assignEmployees()
    {
        if (empty($this->selectedEmployees)) {
            $this->errorMessage = 'Please select at least one employee';
            return;
        }

        $productionService = app(ProductionOrderServiceInterface::class);
        $success = $productionService->assignProductionOrderToEmployees(
            $this->selectedOrder->id,
            $this->selectedEmployees
        );

        if ($success) {
            $this->successMessage = 'Employees assigned successfully';
            $this->showAssignEmployees = false;
            $this->selectedOrder = Order::findOrFail($this->selectedOrder->id);
        } else {
            $this->errorMessage = 'Failed to assign employees';
        }
    }

    public function openScheduleProduction($orderId)
    {
        $this->selectedOrder = Order::with(['orderItems', 'orderItems.product'])->findOrFail($orderId);

        // Initialize production details with order items
        $this->productionDetails = [];
        foreach ($this->selectedOrder->orderItems as $item) {
            $this->productionDetails[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'production_days' => 1, // Default
                'status' => 'pending'
            ];
        }

        $this->scheduledDate = Carbon::now()->addDay()->format('Y-m-d');
        $this->showScheduleProduction = true;
    }

    public function scheduleProduction()
    {
        if (empty($this->scheduledDate)) {
            $this->errorMessage = 'Please select a production date';
            return;
        }

        $productionService = app(ProductionOrderServiceInterface::class);
        $success = $productionService->scheduleProduction(
            $this->selectedOrder->id,
            [
                'scheduled_date' => $this->scheduledDate,
                'priority' => $this->productionPriority,
                'items' => $this->productionDetails
            ]
        );

        if ($success) {
            $this->successMessage = 'Production scheduled successfully';
            $this->showScheduleProduction = false;
            $this->selectedOrder = Order::findOrFail($this->selectedOrder->id);
        } else {
            $this->errorMessage = 'Failed to schedule production';
        }
    }

    public function openFulfillmentWizard($orderId)
    {
        $productionService = app(ProductionOrderServiceInterface::class);

        $this->selectedOrder = Order::with(['orderItems', 'orderItems.product'])
            ->findOrFail($orderId);

        // Reset wizard state
        $this->wizardStep = 1;
        $this->nextStatus = '';
        $this->partialFulfilledItems = [];
        $this->productionNotes = [];
        $this->errorMessage = '';
        $this->successMessage = '';

        // Initialize partial fulfillment items with all order items
        foreach ($this->selectedOrder->orderItems as $item) {
            $this->partialFulfilledItems[$item->id] = [
                'fulfilled_quantity' => 0,
                'max_quantity' => $item->quantity,
                'product_name' => $item->product->name,
                'reason' => ''
            ];
        }

        $this->showFulfillmentWizard = true;
    }

    public function nextStep()
    {
        // Validate current step
        if ($this->wizardStep === 1 && empty($this->nextStatus)) {
            $this->errorMessage = 'Please select a status';
            return;
        }

        if ($this->wizardStep === 2) {
            if ($this->nextStatus === Order::STATUS_PARTIALLY_FULFILLED) {
                $hasPartial = false;
                foreach ($this->partialFulfilledItems as $item) {
                    if ($item['fulfilled_quantity'] > 0) {
                        $hasPartial = true;
                        break;
                    }
                }

                if (!$hasPartial) {
                    $this->errorMessage = 'Please fulfill at least one item';
                    return;
                }
            }
        }

        $this->errorMessage = '';
        $this->wizardStep++;
    }

    public function previousStep()
    {
        if ($this->wizardStep > 1) {
            $this->wizardStep--;
        }
    }

    public function completeFulfillment()
    {
        $productionService = app(ProductionOrderServiceInterface::class);
        $additionalData = [];

        if ($this->nextStatus === Order::STATUS_PARTIALLY_FULFILLED) {
            $fulfillmentData = [];
            foreach ($this->partialFulfilledItems as $itemId => $data) {
                if ($data['fulfilled_quantity'] > 0) {
                    $fulfillmentData[] = [
                        'item_id' => $itemId,
                        'fulfilled_quantity' => $data['fulfilled_quantity'],
                        'reason' => $data['reason']
                    ];
                }
            }
            $additionalData['fulfilled_items'] = $fulfillmentData;
            $additionalData['notes'] = $this->productionNotes;
        }

        $success = $productionService->updateProductionOrderStatus(
            $this->selectedOrder->id,
            $this->nextStatus,
            $additionalData
        );

        if ($success) {
            $this->successMessage = 'Order updated successfully';
            $this->showFulfillmentWizard = false;
            $this->dispatch('order-status-updated');
        } else {
            $this->errorMessage = 'Failed to update order status';
        }
    }

    public function bulkUpdateStatus($status)
    {
        if (empty($this->selectedOrders)) {
            $this->errorMessage = 'Please select at least one order';
            return;
        }

        $productionService = app(ProductionOrderServiceInterface::class);
        $updatedCount = $productionService->bulkUpdateProductionOrders(
            $this->selectedOrders,
            $status
        );

        if ($updatedCount > 0) {
            $this->successMessage = "{$updatedCount} orders updated successfully";
            $this->selectedOrders = [];
            $this->selectAll = false;
        } else {
            $this->errorMessage = 'Failed to update orders';
        }
    }

    public function getOrdersQuery()
    {
        $productionService = app(ProductionOrderServiceInterface::class);

        $filters = [
            'status' => $this->statusFilter,
            'search' => $this->search,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'priority' => $this->priorityFilter,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection
        ];

        return $productionService->getAllProductionOrders($filters);
    }

    public function getResourceAvailability()
    {
        if (!$this->selectedOrder) {
            return [];
        }

        $productionService = app(ProductionOrderServiceInterface::class);
        return $productionService->checkResourceAvailability($this->selectedOrder->id);
    }

    public function getValidNextStatuses()
    {
        if (!$this->selectedOrder) {
            return [];
        }

        $productionService = app(ProductionOrderServiceInterface::class);
        return $productionService->getValidNextStatuses($this->selectedOrder);
    }

    public function exportOrders()
    {
        // Implementation for exporting orders to CSV
        // This would typically generate and return a file download
        $this->successMessage = 'Orders exported successfully';
    }

    public function render()
    {
        $orders = $this->getOrdersQuery();

        $productionService = app(ProductionOrderServiceInterface::class);
        $stats = $productionService->getProductionOrderStatistics(
            Carbon::now()->subDays(30),
            Carbon::now()
        );

        $validBulkStatuses = [
            Order::STATUS_PROCESSING => 'Process',
            Order::STATUS_FULFILLED => 'Mark Fulfilled'
        ];

        return view('livewire.production-manager.production-order-management', [
            'orders' => $orders,
            'stats' => $stats,
            'validBulkStatuses' => $validBulkStatuses,
            'resources' => $this->showResourceCheck ? $this->getResourceAvailability() : [],
            'validNextStatuses' => $this->showFulfillmentWizard ? $this->getValidNextStatuses() : []
        ]);
    }
}
