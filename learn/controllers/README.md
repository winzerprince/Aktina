# Controllers in Aktina SCM 🎮

Controllers are the traffic directors of our application, handling HTTP requests and coordinating responses.

## 📍 Location in Project
```
app/Http/Controllers/
├── Admin/
│   ├── AdminDashboardController.php
│   └── ApplicationController.php
├── Auth/
├── HRManager/
├── ProductionManager/
├── Retailer/
├── Supplier/
├── Vendor/
└── Controller.php (Base Controller)
```

## 🎯 Three-Level Explanations

### 👶 **5-Year-Old Level: The Restaurant Manager**

Imagine you're at a restaurant. The controller is like the manager who:
- **Takes your order** (receives requests)
- **Tells the kitchen what to cook** (calls services)
- **Brings you your food** (returns responses)
- **Handles complaints** (manages errors)

When you ask for a hamburger, the manager doesn't cook it themselves - they tell the kitchen (service) to make it, then bring it to you!

### 🎓 **CS Student Level: HTTP Request Coordinators**

Controllers in Laravel follow the **MVC (Model-View-Controller)** pattern:

```php
// Example: AdminDashboardController.php
class AdminDashboardController extends Controller
{
    public function overview(SalesAnalyticsService $salesService)
    {
        // 1. Receive HTTP request
        // 2. Call business logic (Service)
        $analytics = $salesService->getOverviewData();
        
        // 3. Return view with data
        return view('admin.overview', compact('analytics'));
    }
}
```

**Key Responsibilities:**
- **Route Handling**: Map URLs to specific methods
- **Request Validation**: Ensure incoming data is valid
- **Service Orchestration**: Call appropriate business logic
- **Response Formatting**: Return views, JSON, or redirects

### 👨‍🏫 **CS Professor Level: Request-Response Orchestration Layer**

Controllers implement the **Command Pattern** and **Dependency Injection**:

```php
class AdminDashboardController extends Controller
{
    public function __construct(
        private SalesAnalyticsService $salesService,
        private UserManagementService $userService
    ) {}
    
    public function overview(): View
    {
        // Single Responsibility: Coordinate request handling
        // Open/Closed Principle: Extensible through DI
        // Dependency Inversion: Depends on abstractions
        
        return view('admin.overview', [
            'analytics' => $this->salesService->getOverviewData(),
            'users' => $this->userService->getActiveUsers()
        ]);
    }
}
```

## 🏗️ Architecture Patterns Used

### **1. Service-Repository Pattern Integration**
Controllers delegate business logic to services, maintaining thin controllers:

```php
// ❌ Fat Controller (Bad)
public function createOrder(Request $request)
{
    $order = new Order();
    $order->fill($request->all());
    // 50+ lines of business logic...
    $order->save();
}

// ✅ Thin Controller (Good)
public function createOrder(Request $request, OrderService $orderService)
{
    $order = $orderService->createOrder($request->validated());
    return redirect()->route('orders.show', $order);
}
```

### **2. Role-Based Controller Organization**
Each user role has dedicated controller namespace:

```
Controllers/
├── Admin/           # Admin-specific actions
├── Vendor/          # Vendor management
├── Retailer/        # Retailer operations
├── Supplier/        # Supplier functions
├── HRManager/       # HR operations
└── ProductionManager/ # Production control
```

## 📋 Actual Implementation Examples

### **Admin Dashboard Controller**
```php
// File: app/Http/Controllers/Admin/AdminDashboardController.php
class AdminDashboardController extends Controller
{
    public function overview()
    {
        return view('admin.overview');
    }
    
    public function sales()
    {
        return view('admin.sales');
    }
    
    public function users()
    {
        return view('admin.users');
    }
}
```

### **Application Controller (Complex Logic)**
```php
// File: app/Http/Controllers/Admin/ApplicationController.php
class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationService $applicationService,
        private VerificationService $verificationService
    ) {}
    
    public function store(ApplicationRequest $request)
    {
        // Delegate to service layer
        $application = $this->applicationService->create($request->validated());
        
        // Queue background job
        ProcessApplicationJob::dispatch($application);
        
        return redirect()->route('applications.show', $application)
                        ->with('success', 'Application submitted successfully');
    }
}
```

## 🔗 Interconnections

### **With Services**
```php
// Controllers inject and use services
public function __construct(
    private OrderService $orderService,
    private InventoryService $inventoryService
) {}
```

### **With Middleware**
```php
// Routes with middleware
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'overview']);
});
```

### **With Livewire Components**
```php
// Controllers return views with Livewire components
public function sales()
{
    return view('admin.sales'); // Contains <livewire:admin.sales-table />
}
```

## 🎯 Best Practices Used

### **1. Dependency Injection**
```php
// Constructor injection for services
public function __construct(
    private SalesAnalyticsService $salesService
) {}
```

### **2. Request Validation**
```php
public function store(CreateOrderRequest $request)
{
    // Validation happens in FormRequest class
    $validatedData = $request->validated();
}
```

### **3. Resource Controllers**
```php
// RESTful resource controllers
Route::resource('orders', OrderController::class);
// Generates: index, create, store, show, edit, update, destroy
```

### **4. Response Helpers**
```php
// Consistent response formatting
return response()->json(['success' => true, 'data' => $data]);
return redirect()->route('orders.index')->with('success', 'Order created');
```

## 🔧 Common Patterns

### **1. Service Orchestration**
```php
public function processOrder(Request $request)
{
    $order = $this->orderService->create($request->validated());
    $this->inventoryService->reserveStock($order);
    $this->notificationService->sendOrderConfirmation($order);
    
    return response()->json(['order' => $order]);
}
```

### **2. Error Handling**
```php
public function show(Order $order)
{
    try {
        $this->authorize('view', $order);
        return view('orders.show', compact('order'));
    } catch (AuthorizationException $e) {
        abort(403, 'Unauthorized access');
    }
}
```

### **3. API vs Web Controllers**
```php
// Web Controller - returns views
public function index()
{
    return view('orders.index');
}

// API Controller - returns JSON
public function index()
{
    return response()->json(['orders' => $this->orderService->getAllOrders()]);
}
```

## 🎪 Real-World Example: Order Processing

```php
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private InventoryService $inventoryService
    ) {}
    
    public function store(CreateOrderRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // 1. Create order through service
            $order = $this->orderService->createOrder($request->validated());
            
            // 2. Reserve inventory
            $this->inventoryService->reserveStock($order);
            
            // 3. Queue background processing
            ProcessOrderJob::dispatch($order);
            
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                           ->with('success', 'Order created successfully');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->with('error', 'Failed to create order')
                           ->withInput();
        }
    }
}
```

## 📊 Performance Considerations

### **1. Caching**
```php
public function dashboard()
{
    $analytics = Cache::remember('dashboard.analytics', 300, function() {
        return $this->analyticsService->getDashboardData();
    });
    
    return view('admin.dashboard', compact('analytics'));
}
```

### **2. Eager Loading**
```php
public function index()
{
    $orders = Order::with(['user', 'items.product'])->paginate(20);
    return view('orders.index', compact('orders'));
}
```

Controllers in Aktina SCM serve as the coordination layer, maintaining clean separation of concerns while orchestrating complex business operations across multiple services and systems.
