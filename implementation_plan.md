# Phased Implementation Plan for Role-Based Views in Laravel SCM System

## Overview
This plan builds upon your existing Admin implementation architecture using the Repository-Service pattern, Livewire components, and Blade templates. Each role will be implemented in phases for manageable development.

## Phase-Based Implementation Strategy

### Current Database Models Analysis
Your existing models structure:
- **User.php** - Main user model with role relationships
- **Admin.php** - Admin-specific data 
- **ProductionManager.php** - Production manager data
- **HrManager.php** - HR manager data
- **Supplier.php** - Supplier data
- **Vendor.php** - Vendor data with applications
- **Retailer.php** - Retailer data
- **Production.php** - Production tracking
- **Order.php** - Order management
- **Product.php** - Product catalog
- **Rating.php** - Product ratings

### Existing Architecture Pattern (Admin Implementation)
Following your current `AdminDashboardController` pattern:
```
app/Http/Controllers/Admin/AdminDashboardController.php
app/Livewire/Admin/UserManagement/Users/Table.php
app/Livewire/Admin/Sales/Table.php
app/Services/SalesAnalyticsService.php
app/Repositories/SalesRepository.php
resources/views/admin/overview.blade.php
resources/views/admin/sales.blade.php
```

## Phase 1: Production Manager Role (Week 1-2)

### 1.1 Controller Implementation
**File:** `app/Http/Controllers/ProductionManager/ProductionManagerController.php`
```php
<?php
namespace App\Http\Controllers\ProductionManager;
use App\Http\Controllers\Controller;

class ProductionManagerController extends Controller
{
    public function overview() {
        return view('production_manager.overview');
    }
    public function inventory() {
        return view('production_manager.inventory');
    }
    public function orders() {
        return view('production_manager.orders');
    }
    public function production() {
        return view('production_manager.production');
    }
}
```

### 1.2 Routes Implementation
**Add to:** `routes/web.php`
```php
// Production Manager routes
Route::middleware(['auth', 'verified'])->prefix('production_manager')->name('production_manager.')->group(function () {
    Route::controller(ProductionManagerController::class)->group(function () {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/production', 'production')->name('production');
    });
});
```

### 1.3 Services & Repositories
**Files to create:**
- `app/Services/ProductionManagerService.php`
- `app/Repositories/ProductionManagerRepository.php`
- `app/Interfaces/Services/ProductionManagerServiceInterface.php`
- `app/Interfaces/Repositories/ProductionManagerRepositoryInterface.php`

### 1.4 Livewire Components
**Components to create:**
- `app/Livewire/ProductionManager/Inventory/Table.php`
- `app/Livewire/ProductionManager/Orders/Table.php`
- `app/Livewire/ProductionManager/Production/Dashboard.php`

### 1.5 Blade Views
**Views to create:**
- `resources/views/production_manager/overview.blade.php`
- `resources/views/production_manager/inventory.blade.php`
- `resources/views/production_manager/orders.blade.php`
- `resources/views/production_manager/production.blade.php`

## Phase 2: Supplier Role (Week 3)

### 2.1 Following Admin Pattern
**Controller:** `app/Http/Controllers/Supplier/SupplierController.php`
**Routes:** Add supplier prefix group similar to admin
**Views:** 
- `resources/views/supplier/overview.blade.php`
- `resources/views/supplier/orders.blade.php`

### 2.2 Business Logic
- Supplier-specific order management
- Resource supply tracking
- Delivery metrics

## Phase 3: Vendor Role (Week 4-5)

### 3.1 Extended Features
**Controller:** `app/Http/Controllers/Vendor/VendorController.php`
**Special Features:**
- Application PDF upload (using existing Application model)
- Retailer management
- Verification workflow

### 3.2 Additional Migrations Needed
**File:** `database/migrations/add_vendor_retailer_relationships.php`
```php
Schema::table('retailer_listings', function (Blueprint $table) {
    $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
});
```

## Phase 4: Retailer Role (Week 6)

### 4.1 Rating System Implementation
**Migration:** `database/migrations/enhance_ratings_table.php`
```php
Schema::table('ratings', function (Blueprint $table) {
    $table->foreignId('retailer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->integer('rating_value')->between(1, 5);
    $table->text('comment')->nullable();
});
```

### 4.2 Components
- `app/Livewire/Retailer/Ratings/Form.php`
- Rating submission and display system

## Phase 5: HR Manager Role (Week 7-8)

### 5.1 Production Assignments Table
**Migration:** `database/migrations/create_production_assignments_table.php`
```php
Schema::create('production_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('production_id')->constrained()->onDelete('cascade');
    $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
    $table->datetime('assigned_at')->nullable();
    $table->datetime('completed_at')->nullable();
    $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled']);
    $table->timestamps();
});
```

### 5.2 Performance Analytics
- Employee performance tracking
- Assignment completion metrics
- Workload distribution

## Phase 6: Role-Based Middleware & Security (Week 9)

### 6.1 Custom Middleware Implementation
**File:** `app/Http/Middleware/RoleMiddleware.php`
```php
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (!auth()->check() || auth()->user()->role !== $role) {
            return redirect()->route('access.denied');
        }
        return $next($request);
    }
}
```

### 6.2 Register Middleware
**In:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

## Phase 7: UI Enhancements & Charts (Week 10)

### 7.1 Mary.UI Integration
Update all Blade templates to use Mary.UI components:
```blade
<x-mary-card>
<x-mary-button>
<x-mary-table>
```

### 7.2 Apex Charts Implementation
Add charts to overview pages:
```javascript
// In overview.blade.php
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<div id="production-chart"></div>
<script>
document.addEventListener('livewire:load', () => {
    const chart = new ApexCharts(document.querySelector("#production-chart"), {
        // Chart configuration
    });
    chart.render();
});
</script>
```

## Implementation Directory Structure

```
/app
    /Http
        /Controllers
            /Admin (existing)
                AdminDashboardController.php
            /ProductionManager
                ProductionManagerController.php
            /Supplier  
                SupplierController.php
            /Vendor
                VendorController.php
            /Retailer
                RetailerController.php
            /HrManager
                HrManagerController.php
        /Middleware
            RoleMiddleware.php
    /Livewire
        /Admin (existing)
        /ProductionManager
            /Inventory
                Table.php
            /Orders  
                Table.php
            /Production
                Dashboard.php
        /Supplier
            /Orders
                Table.php
        /Vendor
            /Application
                Form.php
            /Retailers
                Table.php
        /Retailer
            /Ratings
                Form.php
        /HrManager
            /Assignments
                Table.php
            /Performance
                Chart.php
    /Services
        SalesAnalyticsService.php (existing)
        ProductionManagerService.php
        SupplierService.php
        VendorService.php
        RetailerService.php
        HrManagerService.php
    /Repositories
        SalesRepository.php (existing)
        ProductionManagerRepository.php
        SupplierRepository.php
        VendorRepository.php
        RetailerRepository.php
        HrManagerRepository.php
    /Interfaces
        /Services (existing pattern)
        /Repositories (existing pattern)
/resources
    /views
        /admin (existing)
        /production_manager
            overview.blade.php
            inventory.blade.php
            orders.blade.php
            production.blade.php
        /supplier
            overview.blade.php
            orders.blade.php
        /vendor
            overview.blade.php
            retailers.blade.php
            orders.blade.php
            application.blade.php
        /retailer
            overview.blade.php
            orders.blade.php
            ratings.blade.php
        /hr_manager
            employees.blade.php
            assignments.blade.php
            staff_performance.blade.php
/database
    /migrations
        create_production_assignments_table.php
        enhance_ratings_table.php
        add_vendor_retailer_relationships.php
    /seeders
        ProductionManagerSeeder.php
        SupplierSeeder.php
        VendorSeeder.php
        RetailerSeeder.php
        HrManagerSeeder.php
    /factories
        ProductionAssignmentFactory.php
        RatingFactory.php
```

## Detailed Implementation Steps

### Step-by-Step Implementation for Each Phase

#### Phase 1: Production Manager Role - Detailed Steps

**Step 1.1: Create Controller Structure**
```bash
# Create controller directory and file
mkdir -p app/Http/Controllers/ProductionManager
```

**File:** `app/Http/Controllers/ProductionManager/ProductionManagerController.php`
```php
<?php

namespace App\Http\Controllers\ProductionManager;

use App\Http\Controllers\Controller;
use App\Services\ProductionManagerService;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class ProductionManagerController extends Controller
{
    public function __construct(
        protected ProductionManagerService $productionManagerService,
        protected InventoryService $inventoryService
    ) {}

    public function overview()
    {
        // Fetch production stats and KPIs
        $productionStats = $this->productionManagerService->getOverviewData();
        return view('production_manager.overview', compact('productionStats'));
    }

    public function inventory()
    {
        // Get Aktina-owned inventory (not sold to vendors/retailers)
        $inventory = $this->inventoryService->getAktinaStock();
        return view('production_manager.inventory', compact('inventory'));
    }

    public function orders()
    {
        // Production-related orders
        $orders = $this->productionManagerService->getProductionOrders();
        return view('production_manager.orders', compact('orders'));
    }

    public function production()
    {
        // Active production lines and status
        $productions = $this->productionManagerService->getAllProductions();
        return view('production_manager.production', compact('productions'));
    }
}
```

**Step 1.2: Create Service Layer**
**File:** `app/Interfaces/Services/ProductionManagerServiceInterface.php`
```php
<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface ProductionManagerServiceInterface
{
    public function getOverviewData(): array;
    public function getProductionOrders(): Collection;
    public function getAllProductions(): Collection;
    public function getProductionStats(): array;
}
```

**File:** `app/Services/ProductionManagerService.php`
```php
<?php

namespace App\Services;

use App\Interfaces\Services\ProductionManagerServiceInterface;
use App\Interfaces\Repositories\ProductionManagerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductionManagerService implements ProductionManagerServiceInterface
{
    public function __construct(
        protected ProductionManagerRepositoryInterface $repository
    ) {}

    public function getOverviewData(): array
    {
        return [
            'total_productions' => $this->repository->getTotalProductions(),
            'active_productions' => $this->repository->getActiveProductions()->count(),
            'completed_today' => $this->repository->getCompletedToday(),
            'efficiency_rate' => $this->calculateEfficiencyRate(),
        ];
    }

    public function getProductionOrders(): Collection
    {
        return $this->repository->getProductionOrders();
    }

    public function getAllProductions(): Collection
    {
        return $this->repository->getAllProductions();
    }

    public function getProductionStats(): array
    {
        return $this->repository->getProductionStatistics();
    }

    private function calculateEfficiencyRate(): float
    {
        // Business logic for calculating production efficiency
        return 85.5; // Placeholder
    }
}
```

**Step 1.3: Create Repository Layer**
**File:** `app/Interfaces/Repositories/ProductionManagerRepositoryInterface.php`
```php
<?php

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ProductionManagerRepositoryInterface
{
    public function getTotalProductions(): int;
    public function getActiveProductions(): Collection;
    public function getCompletedToday(): int;
    public function getProductionOrders(): Collection;
    public function getAllProductions(): Collection;
    public function getProductionStatistics(): array;
}
```

**File:** `app/Repositories/ProductionManagerRepository.php`
```php
<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ProductionManagerRepositoryInterface;
use App\Models\Production;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductionManagerRepository implements ProductionManagerRepositoryInterface
{
    public function getTotalProductions(): int
    {
        return Cache::remember('total_productions', 3600, function () {
            return Production::count();
        });
    }

    public function getActiveProductions(): Collection
    {
        return Cache::remember('active_productions', 900, function () {
            return Production::where('status', 'in_progress')->get();
        });
    }

    public function getCompletedToday(): int
    {
        return Production::where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();
    }

    public function getProductionOrders(): Collection
    {
        $productionManagerIds = User::where('role', 'Production Manager')->pluck('id');
        
        return Order::whereIn('seller_id', $productionManagerIds)
            ->with(['buyer', 'products'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllProductions(): Collection
    {
        return Production::with(['product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getProductionStatistics(): array
    {
        return [
            'total_units_produced' => Production::sum('completed_units'),
            'average_completion_rate' => Production::avg('completion_percentage'),
            'production_lines_active' => Production::where('status', 'in_progress')->count(),
        ];
    }
}
```

**Step 1.4: Create Livewire Components**
**File:** `app/Livewire/ProductionManager/Inventory/Table.php`
```php
<?php

namespace App\Livewire\ProductionManager\Inventory;

use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected InventoryService $inventoryService;

    public function boot(InventoryService $inventoryService): void
    {
        $this->inventoryService = $inventoryService;
    }

    public function render()
    {
        $inventory = $this->inventoryService->getAktinaStockPaginated(
            $this->search, 
            $this->perPage
        );

        return view('livewire.production-manager.inventory.table', [
            'inventory' => $inventory
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
```

**Step 1.5: Create Blade Views**
**File:** `resources/views/production_manager/overview.blade.php`
```blade
<x-layouts.app>
    <x-slot:title>{{ __('Production Manager Dashboard') }}</x-slot:title>

    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Production Overview') }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ __('Monitor production lines and inventory status') }}</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-ui.metric-card
                title="{{ __('Total Productions') }}"
                :value="$productionStats['total_productions']"
                icon="cog"
                description="{{ __('All time productions') }}"
            />
            <x-ui.metric-card
                title="{{ __('Active Lines') }}"
                :value="$productionStats['active_productions']"
                change="+2"
                change-type="positive"
                icon="play"
                description="{{ __('Currently running') }}"
            />
            <x-ui.metric-card
                title="{{ __('Completed Today') }}"
                :value="$productionStats['completed_today']"
                icon="check-circle"
                description="{{ __('Units finished today') }}"
            />
            <x-ui.metric-card
                title="{{ __('Efficiency Rate') }}"
                :value="$productionStats['efficiency_rate'] . '%'"
                change="+3.2%"
                change-type="positive"
                icon="trending-up"
                description="{{ __('Overall efficiency') }}"
            />
        </div>

        <!-- Production Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <x-ui.chart-card
                title="{{ __('Production Trends') }}"
                description="{{ __('Daily production output') }}"
            >
                <div id="production-chart" class="h-64"></div>
            </x-ui.chart-card>

            <x-ui.chart-card
                title="{{ __('Inventory Levels') }}"
                description="{{ __('Current stock status') }}"
            >
                <div id="inventory-chart" class="h-64"></div>
            </x-ui.chart-card>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:load', () => {
            // Production Trends Chart
            const productionChart = new ApexCharts(document.querySelector("#production-chart"), {
                series: [{
                    name: 'Units Produced',
                    data: [30, 40, 35, 50, 49, 60, 70]
                }],
                chart: {
                    height: 256,
                    type: 'line',
                    toolbar: { show: false }
                },
                colors: ['#10B981'],
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                }
            });
            productionChart.render();

            // Inventory Chart
            const inventoryChart = new ApexCharts(document.querySelector("#inventory-chart"), {
                series: [44, 55, 13, 43],
                chart: {
                    type: 'donut',
                    height: 256
                },
                labels: ['Raw Materials', 'Work in Progress', 'Finished Goods', 'Reserved'],
                colors: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444']
            });
            inventoryChart.render();
        });
    </script>
    @endpush
</x-layouts.app>
```

**Step 1.6: Register Services in Provider**
**Update:** `app/Providers/RepositoryServiceProvider.php`
```php
<?php

namespace App\Providers;

use App\Interfaces\Repositories\SalesRepositoryInterface;
use App\Interfaces\Services\SalesAnalyticsServiceInterface;
use App\Interfaces\Repositories\ProductionManagerRepositoryInterface;
use App\Interfaces\Services\ProductionManagerServiceInterface;
use App\Repositories\SalesRepository;
use App\Services\SalesAnalyticsService;
use App\Repositories\ProductionManagerRepository;
use App\Services\ProductionManagerService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Existing bindings
        $this->app->bind(SalesRepositoryInterface::class, SalesRepository::class);
        $this->app->bind(SalesAnalyticsServiceInterface::class, SalesAnalyticsService::class);
        
        // New Production Manager bindings
        $this->app->bind(ProductionManagerRepositoryInterface::class, ProductionManagerRepository::class);
        $this->app->bind(ProductionManagerServiceInterface::class, ProductionManagerService::class);
    }
}
```

**Step 1.7: Add Routes**
**Update:** `routes/web.php`
```php
// Add after existing admin routes
// Production Manager routes
Route::middleware(['auth', 'verified'])->prefix('production_manager')->name('production_manager.')->group(function () {
    Route::controller(\App\Http\Controllers\ProductionManager\ProductionManagerController::class)->group(function () {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/production', 'production')->name('production');
    });
});
```

#### Phase 2-5: Similar Implementation Pattern

**For each subsequent role (Supplier, Vendor, Retailer, HR Manager):**

1. **Create Controller** following the same namespace pattern
2. **Create Service & Repository** with interfaces
3. **Create Livewire Components** for interactive features
4. **Create Blade Views** with Mary.UI components
5. **Add Routes** with proper middleware
6. **Register Services** in provider

#### Phase 6: Middleware Implementation

**Step 6.1: Create Role Middleware**
**File:** `app/Http/Middleware/RoleMiddleware.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            return redirect()->route('access.denied')
                ->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
```

**Step 6.2: Register Middleware**
**Update:** `bootstrap/app.php`
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

**Step 6.3: Apply Middleware to Routes**
```php
// Update all role routes to include role middleware
Route::middleware(['auth', 'verified', 'role:Production Manager'])
    ->prefix('production_manager')
    ->name('production_manager.')
    ->group(function () {
        // routes...
    });
```

## Database Migrations & Seeders

### Additional Migrations Needed

**File:** `database/migrations/2024_XX_XX_create_production_assignments_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('production_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->datetime('assigned_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['production_id', 'employee_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_assignments');
    }
};
```

**File:** `database/migrations/2024_XX_XX_enhance_ratings_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('ratings', 'retailer_id')) {
                $table->foreignId('retailer_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('ratings', 'comment')) {
                $table->text('comment')->nullable();
            }
            if (!Schema::hasColumn('ratings', 'rating_value')) {
                $table->integer('rating_value')->between(1, 5);
            }
        });
    }

    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['retailer_id']);
            $table->dropColumn(['retailer_id', 'comment', 'rating_value']);
        });
    }
};
```

### Seeders

**File:** `database/seeders/ProductionManagerSeeder.php`
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProductionManager;
use Illuminate\Database\Seeder;

class ProductionManagerSeeder extends Seeder
{
    public function run()
    {
        $users = User::factory(3)->create([
            'role' => 'Production Manager',
            'email_verified_at' => now(),
        ]);

        foreach ($users as $user) {
            ProductionManager::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
```

**File:** `database/seeders/SupplierSeeder.php`
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $users = User::factory(5)->create([
            'role' => 'Supplier',
            'email_verified_at' => now(),
        ]);

        foreach ($users as $user) {
            Supplier::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
```

### Factories

**File:** `database/factories/ProductionAssignmentFactory.php`
```php
<?php

namespace Database\Factories;

use App\Models\Production;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionAssignmentFactory extends Factory
{
    public function definition()
    {
        return [
            'production_id' => Production::factory(),
            'employee_id' => User::factory(['role' => 'Production Manager']),
            'assigned_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'status' => $this->faker->randomElement(['assigned', 'in_progress', 'completed']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
```

## Summary of Implementation Timeline

| Phase | Duration | Focus | Deliverables |
|-------|----------|--------|-------------|
| Phase 1 | Week 1-2 | Production Manager | Controller, Service, Repository, Views, Livewire |
| Phase 2 | Week 3 | Supplier | Full supplier role implementation |
| Phase 3 | Week 4-5 | Vendor | Vendor with application system |
| Phase 4 | Week 6 | Retailer | Retailer with rating system |
| Phase 5 | Week 7-8 | HR Manager | HR with assignment management |
| Phase 6 | Week 9 | Security | Middleware and role protection |
| Phase 7 | Week 10 | UI/UX | Mary.UI integration and charts |

### Total Estimated Timeline: 10 weeks

This phased approach ensures:
- **Manageable development cycles**
- **Incremental testing and validation**
- **Consistency with existing Admin architecture**
- **Proper separation of concerns**
- **Scalable and maintainable codebase**

Each phase builds upon the previous one, allowing for iterative testing and refinement while maintaining the high-quality architecture you've established with your Admin implementation.
