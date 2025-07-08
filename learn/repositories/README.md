# Repositories in Aktina SCM 🗄️

Repositories handle all data access operations, acting as the bridge between our business logic and the database.

## 📍 Location in Project
```
app/Repositories/
├── AlertRepository.php
├── AnalyticsRepository.php
├── ApplicationRepository.php
├── ConversationRepository.php
├── EnhancedOrderRepository.php
├── InventoryRepository.php
├── MLDataRepository.php
├── MLRepository.php
├── MessageRepository.php
├── MetricsRepository.php
├── OrderRepository.php
├── ReportRepository.php
├── ResourceOrderRepository.php
├── SalesRepository.php
└── WarehouseRepository.php
```

## 🎯 Three-Level Explanations

### 👶 **5-Year-Old Level: The Librarian**

Think of repositories like a super helpful librarian who:
- **Knows exactly where every book is** (finds data in the database)
- **Can get any book you want** (retrieves specific records)
- **Puts new books in the right place** (saves new data)
- **Keeps track of all the books** (manages all the information)
- **Knows the library rules** (handles database constraints)

When you ask for a story about dragons, the librarian knows exactly which shelf to check and brings you the perfect book!

### 🎓 **CS Student Level: Data Access Layer**

Repositories implement the **Repository Pattern** for data access:

```php
// Example: OrderRepository.php
class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private Order $model) {}
    
    public function findById(int $id): ?Order
    {
        return $this->model->find($id);
    }
    
    public function create(array $data): Order
    {
        return $this->model->create($data);
    }
    
    public function getOrdersByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
                          ->with(['user', 'items'])
                          ->get();
    }
}
```

**Key Responsibilities:**
- **Abstract Database Operations**: Hide SQL complexity
- **Provide Data Access Interface**: Consistent methods for CRUD operations
- **Handle Relationships**: Manage Eloquent relationships
- **Optimize Queries**: Use eager loading and query optimization

### 👨‍🏫 **CS Professor Level: Domain-Driven Design Data Access**

Repositories implement **Aggregate Repository** and **Query Object** patterns:

```php
interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    public function save(Order $order): Order;
    public function findBySpecification(Specification $spec): Collection;
}

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private Order $model,
        private QueryBuilder $queryBuilder
    ) {}
    
    public function findBySpecification(Specification $spec): Collection
    {
        // Specification pattern for complex queries
        return $spec->apply($this->model->newQuery())->get();
    }
    
    public function findWithComplexCriteria(array $criteria): Collection
    {
        // Query Object pattern
        return $this->queryBuilder
            ->select(['orders.*', 'users.name as user_name'])
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->when($criteria['status'] ?? null, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($criteria['date_range'] ?? null, function($query, $dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            })
            ->get();
    }
}
```

## 🏗️ Architecture Patterns Used

### **1. Repository Pattern**
Each repository encapsulates data access for a specific entity:

```php
class SalesRepository implements SalesRepositoryInterface
{
    public function __construct(private Order $model) {}
    
    public function getTotalRevenue(): float
    {
        return $this->model->where('status', 'completed')
                          ->sum('total_amount');
    }
    
    public function getMonthlyRevenue(): array
    {
        return $this->model->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
                          ->where('status', 'completed')
                          ->groupBy('month')
                          ->get()
                          ->toArray();
    }
}
```

### **2. Query Builder Pattern**
Complex queries are encapsulated in repository methods:

```php
class EnhancedOrderRepository implements EnhancedOrderRepositoryInterface
{
    public function getOrdersWithFilters(array $filters): Collection
    {
        return $this->model->newQuery()
            ->when($filters['status'] ?? null, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['priority'] ?? null, function($query, $priority) {
                return $query->where('priority', $priority);
            })
            ->when($filters['date_from'] ?? null, function($query, $dateFrom) {
                return $query->where('created_at', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function($query, $dateTo) {
                return $query->where('created_at', '<=', $dateTo);
            })
            ->with(['user:id,name', 'items.product:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
```

## 📋 Actual Implementation Examples

### **Sales Repository**
```php
// File: app/Repositories/SalesRepository.php
class SalesRepository implements SalesRepositoryInterface
{
    public function __construct(private Order $model) {}
    
    public function getTotalRevenue(): float
    {
        return $this->model->where('status', 'completed')
                          ->sum('total_amount');
    }
    
    public function getRevenueByPeriod(string $period): array
    {
        $groupBy = match($period) {
            'daily' => 'DATE(created_at)',
            'weekly' => 'WEEK(created_at)',
            'monthly' => 'MONTH(created_at)',
            'yearly' => 'YEAR(created_at)'
        };
        
        return $this->model->selectRaw("{$groupBy} as period, SUM(total_amount) as revenue")
                          ->where('status', 'completed')
                          ->groupBy('period')
                          ->orderBy('period')
                          ->get()
                          ->toArray();
    }
}
```

### **ML Data Repository**
```php
// File: app/Repositories/MLDataRepository.php
class MLDataRepository implements MLDataRepositoryInterface
{
    public function getRetailerData(): array
    {
        return DB::table('retailers')
            ->join('users', 'retailers.user_id', '=', 'users.id')
            ->select([
                'retailers.id',
                'retailers.male_female_ratio',
                'retailers.city',
                'retailers.urban_rural_classification',
                'retailers.customer_age_class',
                'retailers.customer_income_bracket',
                'retailers.customer_education_level',
                'users.company_name'
            ])
            ->where('users.is_verified', true)
            ->get()
            ->toArray();
    }
    
    public function getAktinaSalesData(): array
    {
        return DB::table('orders')
            ->join('users', 'orders.seller_id', '=', 'users.id')
            ->select([
                'orders.created_at as date',
                'orders.total_amount as amount',
                'users.company_name'
            ])
            ->where('users.company_name', 'Aktina')
            ->where('orders.status', 'completed')
            ->orderBy('orders.created_at')
            ->get()
            ->toArray();
    }
}
```

### **Enhanced Order Repository**
```php
// File: app/Repositories/EnhancedOrderRepository.php
class EnhancedOrderRepository implements EnhancedOrderRepositoryInterface
{
    public function __construct(private Order $model) {}
    
    public function create(array $data): Order
    {
        return DB::transaction(function() use ($data) {
            $order = $this->model->create($data);
            
            // Handle order items
            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $order->items()->create($item);
                }
            }
            
            return $order->load(['items', 'user']);
        });
    }
    
    public function findByIdWithRelations(int $id): ?Order
    {
        return $this->model->with([
            'user:id,name,email',
            'items' => function($query) {
                $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price')
                      ->with('product:id,name,price');
            },
            'warehouse:id,name,location'
        ])->find($id);
    }
}
```

## 🔗 Interconnections

### **With Models**
```php
// Repositories work with Eloquent models
class OrderRepository
{
    public function __construct(private Order $model) {}
    
    public function findById(int $id): ?Order
    {
        return $this->model->find($id);
    }
}
```

### **With Services**
```php
// Services inject repositories
class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}
    
    public function createOrder(array $data): Order
    {
        return $this->orderRepository->create($data);
    }
}
```

### **With Query Builder**
```php
// Complex queries using Laravel's Query Builder
public function getComplexReport(): array
{
    return DB::table('orders')
        ->join('users', 'orders.user_id', '=', 'users.id')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->select([
            'orders.id',
            'users.name as customer_name',
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total')
        ])
        ->groupBy('orders.id', 'users.name')
        ->having('total', '>', 1000)
        ->get()
        ->toArray();
}
```

## 🎯 Best Practices Used

### **1. Interface Implementation**
```php
interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    public function create(array $data): Order;
    public function update(Order $order, array $data): Order;
    public function delete(Order $order): bool;
}

class OrderRepository implements OrderRepositoryInterface
{
    // Implementation...
}
```

### **2. Eager Loading**
```php
public function getOrdersWithRelations(): Collection
{
    return $this->model->with([
        'user:id,name,email',
        'items.product:id,name,price'
    ])->get();
}
```

### **3. Query Optimization**
```php
public function getOrdersByStatus(string $status): Collection
{
    return $this->model->where('status', $status)
                      ->select(['id', 'user_id', 'total_amount', 'created_at'])
                      ->with(['user:id,name'])
                      ->orderBy('created_at', 'desc')
                      ->get();
}
```

## 🔧 Common Patterns

### **1. Specification Pattern**
```php
class OrderSpecification
{
    public static function byStatus(string $status)
    {
        return function($query) use ($status) {
            return $query->where('status', $status);
        };
    }
    
    public static function byDateRange(Carbon $start, Carbon $end)
    {
        return function($query) use ($start, $end) {
            return $query->whereBetween('created_at', [$start, $end]);
        };
    }
}

// Usage in repository
public function findBySpecifications(array $specs): Collection
{
    $query = $this->model->newQuery();
    
    foreach ($specs as $spec) {
        $query = $spec($query);
    }
    
    return $query->get();
}
```

### **2. Caching Layer**
```php
public function getExpensiveData(): array
{
    return Cache::remember('expensive.repository.data', 3600, function() {
        return $this->model->join('related_table', 'condition')
                          ->selectRaw('complex calculation')
                          ->get()
                          ->toArray();
    });
}
```

### **3. Transaction Management**
```php
public function createOrderWithItems(array $orderData, array $items): Order
{
    return DB::transaction(function() use ($orderData, $items) {
        $order = $this->model->create($orderData);
        
        foreach ($items as $item) {
            $order->items()->create($item);
        }
        
        return $order->load('items');
    });
}
```

## 🎪 Real-World Example: Analytics Repository

```php
class AnalyticsRepository implements AnalyticsRepositoryInterface
{
    public function __construct(private Order $orderModel) {}
    
    public function getDashboardMetrics(): array
    {
        $baseQuery = $this->orderModel->where('status', 'completed');
        
        return [
            'total_revenue' => $baseQuery->sum('total_amount'),
            'total_orders' => $baseQuery->count(),
            'average_order_value' => $baseQuery->avg('total_amount'),
            'monthly_growth' => $this->getMonthlyGrowthRate(),
            'top_products' => $this->getTopSellingProducts(),
            'customer_segments' => $this->getCustomerSegments()
        ];
    }
    
    private function getMonthlyGrowthRate(): float
    {
        $currentMonth = $this->orderModel->whereMonth('created_at', Carbon::now()->month)
                                        ->where('status', 'completed')
                                        ->sum('total_amount');
        
        $previousMonth = $this->orderModel->whereMonth('created_at', Carbon::now()->subMonth()->month)
                                         ->where('status', 'completed')
                                         ->sum('total_amount');
        
        return $previousMonth > 0 ? (($currentMonth - $previousMonth) / $previousMonth) * 100 : 0;
    }
    
    private function getTopSellingProducts(): Collection
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select([
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            ])
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
    }
}
```

## 📊 Performance Considerations

### **1. Index Usage**
```php
// Ensure proper indexing for frequently queried columns
public function getOrdersByUserAndStatus(int $userId, string $status): Collection
{
    // This query benefits from compound index on (user_id, status)
    return $this->model->where('user_id', $userId)
                      ->where('status', $status)
                      ->get();
}
```

### **2. Pagination**
```php
public function getPaginatedOrders(int $perPage = 20): LengthAwarePaginator
{
    return $this->model->with(['user:id,name'])
                      ->orderBy('created_at', 'desc')
                      ->paginate($perPage);
}
```

### **3. Raw Queries for Complex Operations**
```php
public function getComplexAnalytics(): array
{
    return DB::select("
        SELECT 
            DATE_FORMAT(o.created_at, '%Y-%m') as month,
            COUNT(o.id) as order_count,
            SUM(o.total_amount) as revenue,
            AVG(o.total_amount) as avg_order_value
        FROM orders o
        WHERE o.status = 'completed'
        AND o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY month
        ORDER BY month
    ");
}
```

Repositories in Aktina SCM provide a clean, efficient interface to data access while maintaining separation of concerns and enabling complex query optimization.
