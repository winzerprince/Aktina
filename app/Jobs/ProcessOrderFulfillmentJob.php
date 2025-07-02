<?php

namespace App\Jobs;

use App\Models\Order;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Interfaces\Services\WarehouseServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderFulfillmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $orderId,
        private array $fulfillmentData = []
    ) {}

    public function handle(
        InventoryServiceInterface $inventoryService,
        WarehouseServiceInterface $warehouseService
    ): void {
        try {
            $order = Order::findOrFail($this->orderId);
            
            Log::info("Starting order fulfillment process", [
                'order_id' => $this->orderId,
                'current_status' => $order->status
            ]);
            
            // Validate order is ready for fulfillment
            if ($order->status !== Order::STATUS_ACCEPTED) {
                throw new \Exception("Order {$this->orderId} is not in accepted status for fulfillment");
            }
            
            // Process each item in the order
            $items = $order->getItemsAsArray();
            $fulfillmentResults = [];
            
            foreach ($items as $item) {
                $result = $this->processOrderItem($item, $inventoryService, $warehouseService);
                $fulfillmentResults[] = $result;
            }
            
            // Check if all items were successfully fulfilled
            $allFulfilled = collect($fulfillmentResults)->every(fn($result) => $result['fulfilled']);
            
            if ($allFulfilled) {
                $this->completeOrderFulfillment($order, $fulfillmentResults);
            } else {
                $this->handlePartialFulfillment($order, $fulfillmentResults);
            }
            
            Log::info("Order fulfillment process completed", [
                'order_id' => $this->orderId,
                'fully_fulfilled' => $allFulfilled,
                'results' => $fulfillmentResults
            ]);
            
        } catch (\Exception $e) {
            Log::error("Order fulfillment failed", [
                'order_id' => $this->orderId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    private function processOrderItem(
        array $item,
        InventoryServiceInterface $inventoryService,
        WarehouseServiceInterface $warehouseService
    ): array {
        try {
            $resourceId = $item['resource_id'];
            $quantity = $item['quantity'];
            
            // Check inventory availability
            $availableStock = $inventoryService->getAvailableStock($resourceId);
            
            if ($availableStock < $quantity) {
                return [
                    'resource_id' => $resourceId,
                    'requested_quantity' => $quantity,
                    'available_quantity' => $availableStock,
                    'fulfilled' => false,
                    'reason' => 'insufficient_stock'
                ];
            }
            
            // Find optimal warehouse for fulfillment
            $optimalWarehouse = $warehouseService->allocateOptimalWarehouse(
                \App\Models\Resource::find($resourceId),
                $quantity
            );
            
            if (!$optimalWarehouse) {
                return [
                    'resource_id' => $resourceId,
                    'requested_quantity' => $quantity,
                    'fulfilled' => false,
                    'reason' => 'no_warehouse_available'
                ];
            }
            
            // Process the inventory movement
            $inventoryService->processMovement(
                $resourceId,
                $quantity,
                'outbound',
                "Order fulfillment - Order #{$this->orderId}",
                $optimalWarehouse->id
            );
            
            return [
                'resource_id' => $resourceId,
                'requested_quantity' => $quantity,
                'fulfilled_quantity' => $quantity,
                'warehouse_id' => $optimalWarehouse->id,
                'fulfilled' => true
            ];
            
        } catch (\Exception $e) {
            Log::error("Order item fulfillment failed", [
                'order_id' => $this->orderId,
                'resource_id' => $resourceId,
                'quantity' => $quantity,
                'error' => $e->getMessage()
            ]);
            
            return [
                'resource_id' => $resourceId,
                'requested_quantity' => $quantity,
                'fulfilled' => false,
                'reason' => 'processing_error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function completeOrderFulfillment(Order $order, array $fulfillmentResults): void
    {
        $order->update([
            'status' => 'in_transit',
            'fulfillment_started_at' => now(),
            'fulfillment_data' => $fulfillmentResults,
            'tracking_number' => $this->generateTrackingNumber()
        ]);
        
        // Trigger shipping process
        if (isset($this->fulfillmentData['auto_ship']) && $this->fulfillmentData['auto_ship']) {
            \App\Jobs\ProcessOrderShipmentJob::dispatch($this->orderId, [
                'tracking_number' => $order->tracking_number,
                'carrier' => $this->fulfillmentData['shipping_carrier'] ?? 'default',
                'estimated_delivery' => now()->addDays(3)
            ])->delay(now()->addMinutes(10));
        }
    }

    private function handlePartialFulfillment(Order $order, array $fulfillmentResults): void
    {
        $order->update([
            'status' => 'partially_fulfilled',
            'fulfillment_data' => $fulfillmentResults,
            'partial_fulfillment_at' => now()
        ]);
        
        // Create backorder for unfulfilled items
        $unfulfilledItems = collect($fulfillmentResults)
            ->where('fulfilled', false)
            ->values()
            ->toArray();
        
        if (!empty($unfulfilledItems)) {
            $this->createBackorder($order, $unfulfilledItems);
        }
    }

    private function createBackorder(Order $order, array $unfulfilledItems): void
    {
        // Create a new order for unfulfilled items
        $backorderItems = [];
        foreach ($unfulfilledItems as $item) {
            $backorderItems[] = [
                'resource_id' => $item['resource_id'],
                'quantity' => $item['requested_quantity']
            ];
        }
        
        $backorder = Order::create([
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'items' => $backorderItems,
            'status' => Order::STATUS_PENDING,
            'parent_order_id' => $order->id,
            'is_backorder' => true,
            'notes' => "Backorder for Order #{$order->id}"
        ]);
        
        Log::info("Backorder created", [
            'original_order_id' => $this->orderId,
            'backorder_id' => $backorder->id,
            'unfulfilled_items' => count($unfulfilledItems)
        ]);
    }

    private function generateTrackingNumber(): string
    {
        return 'AKT' . strtoupper(uniqid()) . rand(100, 999);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessOrderFulfillmentJob failed", [
            'order_id' => $this->orderId,
            'exception' => $exception->getMessage()
        ]);
        
        // Mark order as fulfillment failed
        try {
            $order = Order::find($this->orderId);
            if ($order) {
                $order->update([
                    'status' => 'fulfillment_failed',
                    'fulfillment_error' => $exception->getMessage(),
                    'fulfillment_failed_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update order status after fulfillment failure", [
                'order_id' => $this->orderId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
