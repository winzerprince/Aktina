# Jobs in Aktina SCM ⚡

Jobs handle background tasks and asynchronous processing, ensuring the application remains responsive while handling time-consuming operations.

## 📍 Location in Project
```
app/Jobs/
├── GenerateOrderAnalyticsJob.php
├── ProcessOrderApprovalJob.php
├── ProcessOrderFulfillmentJob.php
└── RefreshMLPredictions.php
```

## 🎯 Three-Level Explanations

### 👶 **5-Year-Old Level: The Helper Elves**

Imagine you have magical helper elves who work in the background:

- **When you ask them to clean your room** (process an order), they do it while you play with toys
- **They work at night while you sleep** (background processing), so everything is ready in the morning
- **If one elf gets tired**, another elf can take over the work (retry mechanisms)
- **They have a special list** (queue) of all the jobs they need to do
- **They're very organized** and do one job at a time, but there can be many elves working!

The best part? You don't have to wait for them - you can keep playing while they work!

### 🎓 **CS Student Level: Asynchronous Task Processing**

Jobs implement the **Queue Pattern** for background processing:

```php
// Example: ProcessOrderFulfillmentJob.php
class ProcessOrderFulfillmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function __construct(
        private Order $order
    ) {}
    
    public function handle(
        OrderService $orderService,
        InventoryService $inventoryService
    ): void
    {
        // Time-consuming operations that don't block the web request
        $orderService->validateOrderFulfillment($this->order);
        $inventoryService->allocateInventory($this->order);
        $orderService->generateShippingLabel($this->order);
        $orderService->sendFulfillmentNotification($this->order);
    }
}
```

**Key Characteristics:**
- **Asynchronous Execution**: Don't block HTTP requests
- **Retry Logic**: Automatic retry on failure
- **Queue Management**: FIFO processing with priority support
- **Error Handling**: Failed job tracking and recovery

### 👨‍🏫 **CS Professor Level: Event-Driven Architecture Component**

Jobs implement **Command Pattern** and **Event-Driven Architecture**:

```php
class ProcessOrderFulfillmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    // Queue configuration
    public string $queue = 'order-processing';
    public int $timeout = 300;
    public int $tries = 3;
    public int $backoff = 60;
    
    public function __construct(
        private Order $order,
        private array $options = []
    ) {}
    
    public function handle(
        OrderService $orderService,
        InventoryService $inventoryService,
        NotificationService $notificationService
    ): void
    {
        // Idempotent operation with proper error handling
        try {
            DB::transaction(function() use ($orderService, $inventoryService) {
                // Atomic operations
                $orderService->processOrderFulfillment($this->order);
                $inventoryService->updateInventoryLevels($this->order);
            });
            
            // Fire domain events
            event(new OrderFulfilled($this->order));
            
        } catch (Exception $e) {
            // Custom error handling and reporting
            $this->handleJobFailure($e);
            throw $e; // Re-throw for queue retry mechanism
        }
    }
    
    public function failed(Exception $exception): void
    {
        // Clean up resources and notify administrators
        Log::error('Order fulfillment job failed', [
            'order_id' => $this->order->id,
            'exception' => $exception->getMessage()
        ]);
        
        // Notify administrators
        AdminNotification::dispatch($this->order, $exception);
    }
}
```

## 🏗️ Architecture Patterns Used

### **1. Command Pattern**
Each job encapsulates a specific command/operation:

```php
class RefreshMLPredictions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle(MLService $mlService): void
    {
        // Command: Refresh ML predictions
        $mlService->refreshCustomerSegmentation();
        $mlService->refreshSalesForecasts();
        $mlService->clearCache();
    }
}
```

### **2. Observer Pattern Integration**
Jobs can be triggered by model events:

```php
// In a Model Observer
class OrderObserver
{
    public function created(Order $order): void
    {
        // Dispatch job when order is created
        ProcessOrderApprovalJob::dispatch($order);
        GenerateOrderAnalyticsJob::dispatch($order);
    }
}
```

### **3. Chain of Responsibility**
Jobs can be chained for complex workflows:

```php
// Chain multiple jobs
ProcessOrderApprovalJob::withChain([
    new ProcessOrderFulfillmentJob($order),
    new GenerateOrderAnalyticsJob($order),
    new SendOrderCompletionNotification($order)
])->dispatch($order);
```

## 📋 Actual Implementation Examples

### **Generate Order Analytics Job**
```php
// File: app/Jobs/GenerateOrderAnalyticsJob.php
class GenerateOrderAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public string $queue = 'analytics';
    public int $timeout = 120;
    
    public function __construct(private Order $order) {}
    
    public function handle(AnalyticsService $analyticsService): void
    {
        // Generate comprehensive analytics for the order
        $analyticsService->updateOrderMetrics($this->order);
        $analyticsService->updateCustomerMetrics($this->order->user);
        $analyticsService->updateProductMetrics($this->order->items);
        
        // Update cached dashboard data
        Cache::forget('dashboard.analytics');
    }
}
```

### **Process Order Approval Job**
```php
// File: app/Jobs/ProcessOrderApprovalJob.php
class ProcessOrderApprovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public string $queue = 'order-approval';
    public int $tries = 3;
    
    public function __construct(
        private Order $order,
        private bool $autoApprove = false
    ) {}
    
    public function handle(
        OrderService $orderService,
        ApprovalWorkflowService $approvalService
    ): void
    {
        if ($this->autoApprove || $this->order->total_amount < 10000) {
            // Auto-approve small orders
            $orderService->approveOrder($this->order);
        } else {
            // Send for manual approval
            $approvalService->requestManagerApproval($this->order);
        }
        
        // Continue the workflow
        if ($this->order->status === 'approved') {
            ProcessOrderFulfillmentJob::dispatch($this->order);
        }
    }
}
```

### **Refresh ML Predictions Job**
```php
// File: app/Jobs/RefreshMLPredictions.php
class RefreshMLPredictions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public string $queue = 'ml-processing';
    public int $timeout = 600; // 10 minutes for ML operations
    
    public function handle(MLService $mlService): void
    {
        Log::info('Starting ML predictions refresh');
        
        try {
            // Refresh customer segmentation
            $segmentation = $mlService->refreshCustomerSegmentation();
            Log::info('Customer segmentation refreshed', ['segments' => count($segmentation)]);
            
            // Refresh sales forecasts
            $forecasts = $mlService->refreshSalesForecasts();
            Log::info('Sales forecasts refreshed', ['forecasts' => count($forecasts)]);
            
            // Clear related caches
            Cache::forget('ml.customer_segmentation');
            Cache::forget('ml.sales_forecasts');
            
        } catch (Exception $e) {
            Log::error('ML predictions refresh failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw for retry mechanism
        }
    }
    
    public function failed(Exception $exception): void
    {
        Log::error('ML predictions refresh job finally failed', [
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
        
        // Notify administrators about the failure
        AdminNotification::dispatch(
            'ML Predictions Refresh Failed',
            $exception->getMessage()
        );
    }
}
```

## 🔗 Interconnections

### **With Services**
```php
// Jobs inject services through dependency injection
public function handle(
    OrderService $orderService,
    InventoryService $inventoryService
): void
{
    $orderService->processOrder($this->order);
    $inventoryService->updateStock($this->order);
}
```

### **With Events**
```php
// Jobs can fire events when completed
public function handle(): void
{
    // Process the job
    $result = $this->processData();
    
    // Fire completion event
    event(new JobCompleted($this->jobId, $result));
}
```

### **With Controllers**
```php
// Controllers dispatch jobs
class OrderController extends Controller
{
    public function store(Request $request)
    {
        $order = Order::create($request->validated());
        
        // Dispatch background job
        ProcessOrderApprovalJob::dispatch($order);
        
        return response()->json(['order' => $order]);
    }
}
```

## 🎯 Best Practices Used

### **1. Queue Configuration**
```php
class ProcessOrderFulfillmentJob implements ShouldQueue
{
    // Specific queue for order processing
    public string $queue = 'order-processing';
    
    // Timeout for long-running operations
    public int $timeout = 300;
    
    // Number of retry attempts
    public int $tries = 3;
    
    // Backoff strategy (seconds between retries)
    public int $backoff = 60;
}
```

### **2. Idempotent Operations**
```php
public function handle(OrderService $orderService): void
{
    // Check if already processed to avoid duplicate work
    if ($this->order->status === 'processed') {
        Log::info('Order already processed', ['order_id' => $this->order->id]);
        return;
    }
    
    // Process the order
    $orderService->processOrder($this->order);
}
```

### **3. Proper Error Handling**
```php
public function handle(): void
{
    try {
        $this->processOrder();
    } catch (TemporaryException $e) {
        // Temporary failure - will be retried
        Log::warning('Temporary job failure', ['error' => $e->getMessage()]);
        throw $e;
    } catch (PermanentException $e) {
        // Permanent failure - don't retry
        Log::error('Permanent job failure', ['error' => $e->getMessage()]);
        $this->fail($e);
    }
}
```

## 🔧 Common Patterns

### **1. Job Chaining**
```php
// Chain jobs for sequential processing
ProcessOrderApprovalJob::withChain([
    new ProcessOrderFulfillmentJob($order),
    new UpdateInventoryJob($order),
    new SendCompletionNotificationJob($order)
])->dispatch($order);
```

### **2. Batch Processing**
```php
// Process multiple items in batches
class ProcessBulkOrdersJob implements ShouldQueue
{
    public function handle(): void
    {
        Order::where('status', 'pending')
             ->chunk(100, function($orders) {
                 foreach ($orders as $order) {
                     ProcessOrderFulfillmentJob::dispatch($order);
                 }
             });
    }
}
```

### **3. Delayed Execution**
```php
// Delay job execution
ProcessOrderReminderJob::dispatch($order)
                      ->delay(now()->addHours(24));
```

## 🎪 Real-World Example: Complex Order Processing

```php
class ProcessOrderFulfillmentJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public string $queue = 'order-processing';
    public int $timeout = 300;
    public int $tries = 3;
    public array $backoff = [60, 180, 600]; // Exponential backoff
    
    public function __construct(
        private Order $order,
        private array $options = []
    ) {}
    
    // Ensure only one job per order
    public function uniqueId(): string
    {
        return "order-fulfillment-{$this->order->id}";
    }
    
    public function handle(
        OrderService $orderService,
        InventoryService $inventoryService,
        WarehouseService $warehouseService,
        NotificationService $notificationService
    ): void
    {
        Log::info('Starting order fulfillment', ['order_id' => $this->order->id]);
        
        try {
            DB::transaction(function() use (
                $orderService, 
                $inventoryService, 
                $warehouseService
            ) {
                // 1. Validate order can be fulfilled
                $orderService->validateOrderFulfillment($this->order);
                
                // 2. Reserve inventory
                $inventoryService->reserveInventory($this->order);
                
                // 3. Allocate warehouse space
                $warehouse = $warehouseService->allocateSpace($this->order);
                
                // 4. Update order status
                $this->order->update([
                    'status' => 'fulfilling',
                    'warehouse_id' => $warehouse->id,
                    'fulfillment_started_at' => now()
                ]);
                
                Log::info('Order fulfillment completed', [
                    'order_id' => $this->order->id,
                    'warehouse_id' => $warehouse->id
                ]);
            });
            
            // 5. Send notifications (outside transaction)
            $notificationService->sendOrderFulfillmentNotification($this->order);
            
            // 6. Chain next job
            GenerateShippingLabelJob::dispatch($this->order)
                                   ->delay(now()->addMinutes(5));
                                   
        } catch (InsufficientInventoryException $e) {
            // Handle specific business exceptions
            $this->order->update(['status' => 'inventory_shortage']);
            
            // Notify inventory managers
            InventoryShortageNotification::dispatch($this->order, $e);
            
            // Don't retry for business logic failures
            $this->fail($e);
            
        } catch (Exception $e) {
            Log::error('Order fulfillment failed', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);
            
            // Re-throw for retry mechanism
            throw $e;
        }
    }
    
    public function failed(Exception $exception): void
    {
        Log::error('Order fulfillment job finally failed', [
            'order_id' => $this->order->id,
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
        
        // Update order status
        $this->order->update([
            'status' => 'fulfillment_failed',
            'failure_reason' => $exception->getMessage()
        ]);
        
        // Notify administrators
        AdminNotification::dispatch(
            'Order Fulfillment Failed',
            "Order {$this->order->id} fulfillment failed: {$exception->getMessage()}"
        );
    }
    
    public function retryUntil(): DateTime
    {
        // Stop retrying after 24 hours
        return now()->addHours(24);
    }
}
```

## 📊 Performance Considerations

### **1. Queue Prioritization**
```php
// High priority queue for urgent orders
ProcessUrgentOrderJob::dispatch($order)->onQueue('urgent');

// Low priority queue for analytics
GenerateAnalyticsJob::dispatch($data)->onQueue('analytics');
```

### **2. Memory Management**
```php
public function handle(): void
{
    // Process large datasets in chunks to avoid memory issues
    Order::where('needs_processing', true)
         ->chunk(100, function($orders) {
             foreach ($orders as $order) {
                 $this->processOrder($order);
             }
         });
}
```

### **3. Rate Limiting**
```php
class CallExternalAPIJob implements ShouldQueue
{
    public function handle(): void
    {
        // Rate limit API calls
        RateLimiter::attempt(
            'external-api',
            10, // 10 requests
            function() {
                $this->callExternalAPI();
            },
            60 // per minute
        );
    }
}
```

Jobs in Aktina SCM provide robust background processing capabilities, ensuring the system remains responsive while handling complex, time-consuming operations with proper error handling and retry mechanisms.
