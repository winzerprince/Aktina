<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApprovalWorkflowService
{
    public function approveOrder(int $orderId, string $notes = '')
    {
        return DB::transaction(function () use ($orderId, $notes) {
            $order = Order::findOrFail($orderId);
            
            // Update order status
            $order->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $notes,
            ]);
            
            // Create approval history record
            $this->createApprovalHistory($orderId, 'approved', $notes);
            
            // Send notification to order creator
            $this->sendApprovalNotification($order, 'approved');
            
            // Trigger next steps in workflow
            $this->triggerPostApprovalActions($order);
            
            return $order;
        });
    }

    public function rejectOrder(int $orderId, string $reason)
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = Order::findOrFail($orderId);
            
            // Update order status
            $order->update([
                'approval_status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);
            
            // Create approval history record
            $this->createApprovalHistory($orderId, 'rejected', $reason);
            
            // Send notification to order creator
            $this->sendApprovalNotification($order, 'rejected', $reason);
            
            return $order;
        });
    }

    public function requestMoreInfo(int $orderId, string $request)
    {
        return DB::transaction(function () use ($orderId, $request) {
            $order = Order::findOrFail($orderId);
            
            // Update order status
            $order->update([
                'approval_status' => 'info_requested',
                'info_requested_by' => Auth::id(),
                'info_requested_at' => now(),
                'info_request_notes' => $request,
            ]);
            
            // Create approval history record
            $this->createApprovalHistory($orderId, 'info_requested', $request);
            
            // Send notification to order creator
            $this->sendApprovalNotification($order, 'info_requested', $request);
            
            return $order;
        });
    }

    public function escalateOrder(int $orderId, string $reason = '')
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = Order::findOrFail($orderId);
            
            // Find next level approver (simplified logic)
            $nextApprover = $this->getNextLevelApprover($order);
            
            if (!$nextApprover) {
                throw new \Exception('No higher level approver found');
            }
            
            // Update order status
            $order->update([
                'approval_status' => 'escalated',
                'escalated_by' => Auth::id(),
                'escalated_at' => now(),
                'escalated_to' => $nextApprover->id,
                'escalation_reason' => $reason,
            ]);
            
            // Create approval history record
            $this->createApprovalHistory($orderId, 'escalated', $reason);
            
            // Send notification to next approver
            $this->sendEscalationNotification($order, $nextApprover);
            
            return $order;
        });
    }

    public function bulkApprove(array $orderIds, string $notes = '')
    {
        return DB::transaction(function () use ($orderIds, $notes) {
            $approvedCount = 0;
            
            foreach ($orderIds as $orderId) {
                try {
                    $this->approveOrder($orderId, $notes);
                    $approvedCount++;
                } catch (\Exception $e) {
                    // Log error but continue with other orders
                    logger()->error("Bulk approval failed for order {$orderId}: " . $e->getMessage());
                }
            }
            
            return $approvedCount;
        });
    }

    public function getApprovalWorkflow(Order $order)
    {
        // Define approval workflow based on order value and type
        $workflow = [];
        
        if ($order->total_amount > 50000) {
            // High value orders need multiple approvals
            $workflow = [
                'production_manager' => 'Production Manager Review',
                'admin' => 'Admin Approval',
                'senior_admin' => 'Senior Admin Final Approval'
            ];
        } elseif ($order->total_amount > 10000) {
            // Medium value orders
            $workflow = [
                'production_manager' => 'Production Manager Review',
                'admin' => 'Admin Approval'
            ];
        } else {
            // Low value orders
            $workflow = [
                'production_manager' => 'Production Manager Approval'
            ];
        }
        
        return $workflow;
    }

    public function getCurrentApprovalStep(Order $order)
    {
        $workflow = $this->getApprovalWorkflow($order);
        $currentStep = 0;
        
        // Determine current step based on approval history
        $history = $this->getApprovalHistory($order->id);
        
        foreach ($workflow as $role => $description) {
            $stepApproved = $history->where('approver_role', $role)
                                  ->where('action', 'approved')
                                  ->isNotEmpty();
            
            if ($stepApproved) {
                $currentStep++;
            } else {
                break;
            }
        }
        
        return [
            'current_step' => $currentStep,
            'total_steps' => count($workflow),
            'next_approver_role' => array_keys($workflow)[$currentStep] ?? null,
            'workflow' => $workflow
        ];
    }

    private function createApprovalHistory(int $orderId, string $action, string $notes = '')
    {
        return DB::table('order_approval_history')->insert([
            'order_id' => $orderId,
            'approver_id' => Auth::id(),
            'approver_role' => Auth::user()->role,
            'action' => $action,
            'notes' => $notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getApprovalHistory(int $orderId)
    {
        return collect(DB::table('order_approval_history')
            ->where('order_id', $orderId)
            ->orderBy('created_at')
            ->get());
    }

    private function sendApprovalNotification(Order $order, string $status, string $notes = '')
    {
        // In a real implementation, this would send email/SMS notifications
        logger()->info("Approval notification sent", [
            'order_id' => $order->id,
            'status' => $status,
            'notes' => $notes,
            'recipient' => $order->created_by
        ]);
    }

    private function sendEscalationNotification(Order $order, User $nextApprover)
    {
        // In a real implementation, this would send notifications
        logger()->info("Escalation notification sent", [
            'order_id' => $order->id,
            'next_approver' => $nextApprover->id,
            'escalated_by' => Auth::id()
        ]);
    }

    private function getNextLevelApprover(Order $order)
    {
        // Simplified logic - in practice, this would be more complex
        $currentUserRole = Auth::user()->role;
        
        $hierarchy = [
            'production_manager' => 'admin',
            'admin' => 'senior_admin'
        ];
        
        $nextRole = $hierarchy[$currentUserRole] ?? null;
        
        if ($nextRole) {
            return User::where('role', $nextRole)
                      ->where('is_active', true)
                      ->first();
        }
        
        return null;
    }

    private function triggerPostApprovalActions(Order $order)
    {
        // Trigger actions after approval (inventory allocation, etc.)
        logger()->info("Post-approval actions triggered", [
            'order_id' => $order->id,
            'actions' => ['inventory_allocation', 'production_scheduling']
        ]);
    }

    public function getApprovalMetrics(string $timeframe = '30d')
    {
        $startDate = match($timeframe) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subYear(),
        };

        return [
            'total_approvals' => Order::where('approved_at', '>=', $startDate)->count(),
            'avg_approval_time' => $this->calculateAverageApprovalTime($startDate),
            'approval_rate' => $this->calculateApprovalRate($startDate),
            'escalation_rate' => $this->calculateEscalationRate($startDate),
        ];
    }

    private function calculateAverageApprovalTime($startDate)
    {
        $orders = Order::where('approved_at', '>=', $startDate)
                       ->whereNotNull('approved_at')
                       ->get();

        if ($orders->isEmpty()) return '0 hours';

        $totalHours = $orders->sum(function ($order) {
            return $order->created_at->diffInHours($order->approved_at);
        });

        $avgHours = $totalHours / $orders->count();
        
        if ($avgHours < 1) {
            return round($avgHours * 60) . ' minutes';
        }
        
        return round($avgHours, 1) . ' hours';
    }

    private function calculateApprovalRate($startDate)
    {
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $approvedOrders = Order::where('created_at', '>=', $startDate)
                               ->where('approval_status', 'approved')
                               ->count();

        return $totalOrders > 0 ? round(($approvedOrders / $totalOrders) * 100, 1) : 0;
    }

    private function calculateEscalationRate($startDate)
    {
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $escalatedOrders = Order::where('created_at', '>=', $startDate)
                                ->where('approval_status', 'escalated')
                                ->count();

        return $totalOrders > 0 ? round(($escalatedOrders / $totalOrders) * 100, 1) : 0;
    }
}
