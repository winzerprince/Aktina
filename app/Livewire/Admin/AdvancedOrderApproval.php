<?php

namespace App\Livewire\Admin;

use App\Services\AdminOrderService;
use App\Services\ApprovalWorkflowService;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class AdvancedOrderApproval extends Component
{
    use WithPagination;

    public $orders = [];
    public $pendingOrders = [];
    public $approvalStats = [];
    public $selectedOrder = null;
    public $showOrderModal = false;
    public $orderDetails = [];
    public $approvalNotes = '';
    public $filterStatus = 'pending';
    public $filterPriority = 'all';
    public $searchTerm = '';
    public $selectedTimeframe = '7d';

    public $statusFilters = [
        'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'all' => 'All Orders'
    ];

    public $priorityFilters = [
        'all' => 'All Priorities',
        'high' => 'High Priority',
        'medium' => 'Medium Priority',
        'low' => 'Low Priority'
    ];

    public $timeframes = [
        '24h' => 'Last 24 Hours',
        '7d' => 'Last 7 Days',
        '30d' => 'Last 30 Days',
        'all' => 'All Time'
    ];

    protected $adminOrderService;
    protected $approvalWorkflowService;

    public function boot(AdminOrderService $adminOrderService, ApprovalWorkflowService $approvalWorkflowService)
    {
        $this->adminOrderService = $adminOrderService;
        $this->approvalWorkflowService = $approvalWorkflowService;
    }

    public function mount()
    {
        $this->loadOrders();
        $this->loadApprovalStats();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
        $this->loadOrders();
    }

    public function updatedFilterPriority()
    {
        $this->resetPage();
        $this->loadOrders();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->loadOrders();
    }

    public function updatedSelectedTimeframe()
    {
        $this->loadOrders();
        $this->loadApprovalStats();
    }

    public function loadOrders()
    {
        $filters = [
            'status' => $this->filterStatus !== 'all' ? $this->filterStatus : null,
            'priority' => $this->filterPriority !== 'all' ? $this->filterPriority : null,
            'search' => $this->searchTerm,
            'timeframe' => $this->selectedTimeframe !== 'all' ? $this->selectedTimeframe : null,
        ];

        $this->orders = $this->adminOrderService->getOrdersForApproval($filters, 20);
        $this->pendingOrders = $this->adminOrderService->getPendingApprovals();
    }

    public function loadApprovalStats()
    {
        $timeframe = $this->getTimeframeDate();
        
        $this->approvalStats = [
            'pending_count' => $this->adminOrderService->getPendingApprovalsCount(),
            'approved_today' => $this->adminOrderService->getApprovedTodayCount(),
            'rejected_today' => $this->adminOrderService->getRejectedTodayCount(),
            'avg_approval_time' => $this->adminOrderService->getAverageApprovalTime($timeframe),
            'approval_rate' => $this->adminOrderService->getApprovalRate($timeframe),
            'high_priority_pending' => $this->adminOrderService->getHighPriorityPendingCount(),
            'overdue_approvals' => $this->adminOrderService->getOverdueApprovalsCount(),
        ];
    }

    private function getTimeframeDate()
    {
        return match($this->selectedTimeframe) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subYear(),
        };
    }

    public function showOrderDetails($orderId)
    {
        $this->selectedOrder = $orderId;
        $this->orderDetails = $this->adminOrderService->getOrderDetails($orderId);
        $this->showOrderModal = true;
        $this->approvalNotes = '';
    }

    public function approveOrder($orderId = null)
    {
        $orderId = $orderId ?? $this->selectedOrder;
        
        try {
            $this->approvalWorkflowService->approveOrder($orderId, $this->approvalNotes);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Order approved successfully'
            ]);

            $this->closeOrderModal();
            $this->loadOrders();
            $this->loadApprovalStats();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Approval failed: ' . $e->getMessage()
            ]);
        }
    }

    public function rejectOrder($orderId = null)
    {
        $orderId = $orderId ?? $this->selectedOrder;
        
        if (empty($this->approvalNotes)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Rejection reason is required'
            ]);
            return;
        }

        try {
            $this->approvalWorkflowService->rejectOrder($orderId, $this->approvalNotes);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Order rejected successfully'
            ]);

            $this->closeOrderModal();
            $this->loadOrders();
            $this->loadApprovalStats();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Rejection failed: ' . $e->getMessage()
            ]);
        }
    }

    public function requestMoreInfo($orderId = null)
    {
        $orderId = $orderId ?? $this->selectedOrder;
        
        try {
            $this->approvalWorkflowService->requestMoreInfo($orderId, $this->approvalNotes);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Information request sent successfully'
            ]);

            $this->closeOrderModal();
            $this->loadOrders();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Request failed: ' . $e->getMessage()
            ]);
        }
    }

    public function escalateOrder($orderId = null)
    {
        $orderId = $orderId ?? $this->selectedOrder;
        
        try {
            $this->approvalWorkflowService->escalateOrder($orderId, $this->approvalNotes);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Order escalated successfully'
            ]);

            $this->closeOrderModal();
            $this->loadOrders();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Escalation failed: ' . $e->getMessage()
            ]);
        }
    }

    public function bulkApprove(array $orderIds)
    {
        try {
            $this->approvalWorkflowService->bulkApprove($orderIds);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => count($orderIds) . ' orders approved successfully'
            ]);

            $this->loadOrders();
            $this->loadApprovalStats();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Bulk approval failed: ' . $e->getMessage()
            ]);
        }
    }

    public function closeOrderModal()
    {
        $this->showOrderModal = false;
        $this->selectedOrder = null;
        $this->orderDetails = [];
        $this->approvalNotes = '';
    }

    public function exportApprovals()
    {
        try {
            $filters = [
                'status' => $this->filterStatus !== 'all' ? $this->filterStatus : null,
                'timeframe' => $this->selectedTimeframe !== 'all' ? $this->selectedTimeframe : null,
            ];

            $this->adminOrderService->exportApprovals($filters);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Export started. Download will begin shortly.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ]);
        }
    }

    #[On('refresh-approvals')]
    public function refreshApprovals()
    {
        $this->loadOrders();
        $this->loadApprovalStats();
    }

    public function render()
    {
        return view('livewire.admin.advanced-order-approval');
    }
}
