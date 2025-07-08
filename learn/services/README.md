# Services in Aktina SCM 🔧

Services contain the business logic and complex operations of our application, acting as the brain of the system.

## 📍 Location in Project
```
app/Services/
├── AdminAnalyticsService.php
├── AdminOrderService.php
├── AlertService.php
├── AnalyticsService.php
├── ApplicationService.php
├── EnhancedOrderService.php
├── HRService.php
├── InventoryService.php
├── MLService.php
├── OrderService.php
├── ReportService.php
├── ResourceOrderService.php
├── SalesService.php
├── SupplierService.php
├── SystemHealthService.php
├── UserManagementService.php
├── VendorManagementService.php
├── VerificationService.php
└── WarehouseService.php
```

## 🎯 Three-Level Explanations

### 👶 **5-Year-Old Level: The Smart Helper**

Think of services like a really smart helper who knows how to do complicated jobs:

- **When you want to make a cake** (create an order), the helper knows all the steps: check ingredients, mix them, bake, and decorate
- **When you want to clean your room** (manage inventory), the helper knows where everything goes and how to organize it
- **The helper remembers rules** (business logic) like "always wash hands before cooking"

The helper is so smart that even if you forget something, they'll remind you and do it the right way!

### 🎓 **CS Student Level: Business Logic Layer**

Services implement the **Business Logic Layer** in our architecture:

```php
// Example: OrderService.php
class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryServiceInterface $inventoryService
    ) {}
    
    public function createOrder(array $orderData): Order
    {
        // Business rules and validation
        $this->validateOrderData($orderData);
        
        // Check inventory availability
        $this->inventoryService->checkAvailability($orderData['items']);
        
        // Create order through repository
        $order = $this->orderRepository->create($orderData);
        
        // Send notifications
        $this->notificationService->sendOrderConfirmation($order);
        
        return $order;
    }
}
```

**Key Characteristics:**
- **Encapsulate Business Logic**: Complex operations and rules
- **Coordinate Multiple Operations**: Orchestrate between repositories
- **Maintain Data Integrity**: Ensure business rules are followed
- **Provide Abstraction**: Hide complexity from controllers

### 👨‍🏫 **CS Professor Level: Domain-Driven Design Implementation**

Services implement **Domain Services** and **Application Services** patterns:

```php
interface OrderServiceInterface
{
    public function createOrder(array $orderData): Order;
    public function processPayment(Order $order): PaymentResult;
    public function fulfillOrder(Order $order): void;
}

class OrderService implements OrderServiceInterface
{
    // Dependency Injection following SOLID principles
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryServiceInterface $inventoryService,
        private PaymentServiceInterface $paymentService,
        private NotificationServiceInterface $notificationService
    ) {}
    
    public function createOrder(array $orderData): Order
    {
        // Domain logic encapsulation
        // Transaction management
        // Event sourcing
        // Complex business rules
        
        return DB::transaction(function() use ($orderData) {
            // Aggregate pattern implementation
            $order = $this->orderRepository->create($orderData);
            
            // Domain events
            event(new OrderCreated($order));
            
            return $order;
        });
    }
}
```

## 🏗️ Architecture Patterns Used

### **1. Service-Repository Pattern**
Services coordinate between multiple repositories:

```php
class EnhancedOrderService implements EnhancedOrderServiceInterface
{
    public function __construct(
        private EnhancedOrderRepositoryInterface $orderRepository,
        private InventoryServiceInterface $inventoryService,
        private WarehouseServiceInterface $warehouseService
    ) {}
    
    public function createOrder(array $orderData): Order
    {
        // Calculate order value
        $totalValue = $this->calculateOrderValue($orderData['items']);
        
        // Check inventory availability
        $availability = $this->checkInventoryAvailability($orderData['items']);
        
        // Allocate warehouse space
        $warehouse = $this->warehouseService->allocateSpace($orderData);
        
        // Create order with all validations
        return $this->orderRepository->create($orderData);
    }
}
```

### **2. Dependency Injection Pattern**
All services use constructor injection:

```php
class InventoryService implements InventoryServiceInterface
{
    public function __construct(
        private InventoryRepositoryInterface $inventoryRepository,
        private AlertServiceInterface $alertService
    ) {}
    
    public function updateStock(Resource $resource, int $quantity): Resource
    {
        // Business logic for stock updates
        $resource->recordMovement('adjustment', $quantity);
        
        // Check thresholds and send alerts
        $this->alertService->checkThresholds($resource);
        
        return $resource;
    }
}
```

## 📋 Actual Implementation Examples

### **Admin Analytics Service**
```php
// File: app/Services/AdminAnalyticsService.php
class AdminAnalyticsService
{
    public function __construct(
        private SalesRepository $salesRepository,
        private UserRepository $userRepository
    ) {}
    
    public function getOverviewData(): array
    {
        return [
            'total_revenue' => $this->salesRepository->getTotalRevenue(),
            'total_orders' => $this->salesRepository->getTotalOrders(),
            'total_users' => $this->userRepository->getActiveUsersCount(),
            'recent_activities' => $this->getRecentActivities()
        ];
    }
    
    private function getRecentActivities(): Collection
    {
        // Complex business logic for activity aggregation
        return collect([
            $this->salesRepository->getRecentOrders(),
            $this->userRepository->getRecentRegistrations()
        ])->flatten()->sortByDesc('created_at');
    }
}
```

### **ML Service (Microservice Integration)**
```php
// File: app/Services/MLService.php
class MLService
{
    public function __construct(
        private MLRepository $mlRepository,
        private HttpClient $httpClient
    ) {}
    
    public function getCustomerSegmentation(): array
    {
        // Check cache first
        return Cache::remember('ml.customer_segmentation', 86400, function() {
            // Get data from repository
            $retailerData = $this->mlRepository->getRetailerData();
            
            // Call Python ML microservice
            $response = $this->httpClient->post('http://python-ml:8000/segment', [
                'data' => $retailerData
            ]);
            
            return $response->json();
        });
    }
}
```

### **Verification Service (Complex Workflow)**
```php
// File: app/Services/VerificationService.php
class VerificationService implements VerificationServiceInterface
{
    public function processVerification(User $user, array $documents): VerificationResult
    {
        // Multi-step verification process
        $steps = [
            'document_validation' => $this->validateDocuments($documents),
            'identity_check' => $this->performIdentityCheck($user),
            'business_validation' => $this->validateBusinessInfo($user),
            'compliance_check' => $this->performComplianceCheck($user)
        ];
        
        // Aggregate results
        $overallResult = $this->aggregateVerificationResults($steps);
        
        // Update user status
        $this->updateUserVerificationStatus($user, $overallResult);
        
        return $overallResult;
    }
}
```

## 🔗 Interconnections

### **With Repositories**
```php
// Services use repositories for data access
public function __construct(
    private OrderRepositoryInterface $orderRepository,
    private InventoryRepositoryInterface $inventoryRepository
) {}
```

### **With Jobs**
```php
// Services dispatch jobs for background processing
public function processLargeOrder(Order $order)
{
    ProcessOrderFulfillmentJob::dispatch($order);
    GenerateOrderAnalyticsJob::dispatch($order);
}
```

### **With Events**
```php
// Services fire events for system-wide notifications
public function createOrder(array $data): Order
{
    $order = $this->orderRepository->create($data);
    
    event(new OrderCreated($order));
    
    return $order;
}
```

## 🎯 Best Practices Used

### **1. Interface Segregation**
```php
// Specific interfaces for different concerns
interface OrderServiceInterface
{
    public function createOrder(array $data): Order;
    public function updateOrderStatus(Order $order, string $status): void;
}

interface OrderAnalyticsServiceInterface
{
    public function getOrderMetrics(): array;
    public function generateOrderReport(): Report;
}
```

### **2. Single Responsibility Principle**
```php
// Each service has one clear responsibility
class OrderService           // Handles order CRUD operations
class OrderAnalyticsService  // Handles order analytics
class OrderNotificationService // Handles order notifications
```

### **3. Caching Strategy**
```php
public function getExpensiveData(): array
{
    return Cache::remember('expensive.data', 3600, function() {
        // Only cache data that doesn't change frequently
        return $this->performExpensiveOperation();
    });
}
```

## 🔧 Common Patterns

### **1. Transaction Management**
```php
public function complexBusinessOperation(): Result
{
    return DB::transaction(function() {
        // Multiple operations that must succeed together
        $order = $this->orderRepository->create($data);
        $this->inventoryService->reserveStock($order);
        $this->paymentService->processPayment($order);
        
        return $order;
    });
}
```

### **2. Error Handling**
```php
public function processOrder(array $data): Order
{
    try {
        $this->validateOrderData($data);
        return $this->orderRepository->create($data);
    } catch (ValidationException $e) {
        Log::error('Order validation failed', ['data' => $data, 'error' => $e->getMessage()]);
        throw new OrderCreationException('Invalid order data', 0, $e);
    }
}
```

### **3. Event Sourcing**
```php
public function updateOrderStatus(Order $order, string $status): void
{
    $oldStatus = $order->status;
    $order->update(['status' => $status]);
    
    // Record the change as an event
    event(new OrderStatusChanged($order, $oldStatus, $status));
}
```

## 🎪 Real-World Example: Enhanced Order Service

```php
class EnhancedOrderService implements EnhancedOrderServiceInterface
{
    public function __construct(
        private EnhancedOrderRepositoryInterface $orderRepository,
        private InventoryServiceInterface $inventoryService,
        private WarehouseServiceInterface $warehouseService
    ) {}
    
    public function createOrder(array $orderData): Order
    {
        return DB::transaction(function() use ($orderData) {
            // 1. Validate business rules
            $this->validateOrderBusinessRules($orderData);
            
            // 2. Calculate order value
            $totalValue = $this->calculateOrderValue($orderData['items']);
            
            // 3. Check inventory availability
            $availability = $this->checkInventoryAvailability($orderData['items']);
            if (!$availability['available']) {
                throw new InsufficientInventoryException($availability['message']);
            }
            
            // 4. Find optimal warehouse
            $warehouse = $this->warehouseService->findOptimalWarehouse($orderData);
            
            // 5. Create order
            $order = $this->orderRepository->create(array_merge($orderData, [
                'total_value' => $totalValue,
                'warehouse_id' => $warehouse->id
            ]));
            
            // 6. Reserve inventory
            $this->inventoryService->reserveStock($order);
            
            // 7. Queue background processing
            ProcessOrderJob::dispatch($order);
            
            // 8. Send notifications
            event(new OrderCreated($order));
            
            return $order;
        });
    }
    
    private function validateOrderBusinessRules(array $orderData): void
    {
        // Complex business validation logic
        if ($orderData['priority'] === 'urgent' && !$this->canHandleUrgentOrder()) {
            throw new BusinessRuleException('Cannot process urgent orders at this time');
        }
    }
}
```

## 📊 Performance Considerations

### **1. Caching Expensive Operations**
```php
public function getCustomerSegmentation(): array
{
    return Cache::remember('customer.segmentation', 86400, function() {
        // This is expensive - only cache for 24 hours
        return $this->mlService->performSegmentation();
    });
}
```

### **2. Lazy Loading**
```php
public function getOrderWithDetails(int $orderId): Order
{
    return $this->orderRepository->findWithRelations($orderId, [
        'items' => fn($q) => $q->with('product'),
        'user:id,name,email'
    ]);
}
```

### **3. Background Processing**
```php
public function processLargeDataset(array $data): void
{
    // Don't block the request - process in background
    ProcessLargeDatasetJob::dispatch($data);
}
```

Services in Aktina SCM serve as the intelligent orchestration layer, implementing complex business logic while maintaining clean separation of concerns and ensuring system reliability.
