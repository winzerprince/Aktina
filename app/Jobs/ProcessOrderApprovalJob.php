<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Interfaces\Services\EnhancedOrderServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessOrderApprovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $orderId,
        private int $approverId,
        private string $action, // 'approve' or 'reject'
        private ?string $reason = null
    ) {}

    public function handle(EnhancedOrderServiceInterface $orderService): void
    {
        try {
            $order = Order::findOrFail($this->orderId);
            $approver = User::findOrFail($this->approverId);
            
            if ($this->action === 'approve') {
                $updatedOrder = $orderService->approveOrder($this->orderId, $this->approverId);
                
                // Send notifications
                $this->sendApprovalNotifications($updatedOrder, $approver);
                
                Log::info("Order approval processed", [
                    'order_id' => $this->orderId,
                    'approver_id' => $this->approverId
                ]);
                
            } elseif ($this->action === 'reject') {
                $updatedOrder = $orderService->rejectOrder($this->orderId, $this->approverId, $this->reason);
                
                // Send notifications
                $this->sendRejectionNotifications($updatedOrder, $approver, $this->reason);
                
                Log::info("Order rejection processed", [
                    'order_id' => $this->orderId,
                    'approver_id' => $this->approverId,
                    'reason' => $this->reason
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("Order approval processing failed", [
                'order_id' => $this->orderId,
                'approver_id' => $this->approverId,
                'action' => $this->action,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    private function sendApprovalNotifications(Order $order, User $approver): void
    {
        // Notify the buyer
        if ($order->buyer) {
            // TODO: Send order approved notification
        }
        
        // Notify relevant stakeholders based on supply chain role
        $this->notifySupplyChainParticipants($order, 'approved');
    }

    private function sendRejectionNotifications(Order $order, User $approver, ?string $reason): void
    {
        // Notify the buyer
        if ($order->buyer) {
            // TODO: Send order rejected notification
        }
        
        // Notify relevant stakeholders
        $this->notifySupplyChainParticipants($order, 'rejected', $reason);
    }

    private function notifySupplyChainParticipants(Order $order, string $status, ?string $reason = null): void
    {
        // Logic to notify relevant users in the supply chain
        // This can be expanded based on specific business requirements
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessOrderApprovalJob failed", [
            'order_id' => $this->orderId,
            'approver_id' => $this->approverId,
            'action' => $this->action,
            'exception' => $exception->getMessage()
        ]);
    }
}
