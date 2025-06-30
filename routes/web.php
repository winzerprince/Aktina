<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\hr_manager\HrManagerDashboardController;
use App\Http\Controllers\production_manager\ProductionManagerDashboardController;
use App\Http\Controllers\Retailer\RetailerDashboardController;
use App\Http\Controllers\Supplier\SupplierDashboardController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Livewire\Counter;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('components-demo', 'components-demo')
    ->middleware(['auth', 'verified'])
    ->name('components.demo');

// Route::view('orders', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('orders');

// Route::view('stocks', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('stocks');

// Route::view('sales', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('sales');

// Route::view('products', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('products');

// Route::view('invoices', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('invoices');

// Route::view('settings', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('settings');




Route::view('access-denied', 'access_denied')
    ->middleware(['auth', 'verified'])
    ->name('access.denied');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});
//TODO: Add role middleware to restrict access to certain routes based on user roles
//TODO: Customize veification middleware to be based on admins approval rather than email verification

// Admin dashboard routes
 Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(AdminDashboardController::class)->group(function () {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/sales', 'sales')->name('sales');
        Route::get('/users', 'users')->name('users');
        Route::get('/vendors', 'vendors')->name('vendors');
        Route::get('/pending-signups', 'pendingSignups')->name('pending-signups');
        Route::get('/trends-and-predictions', 'trendsAndPredictions')->name('trends-and-predictions');
        Route::get('/important-metrics', 'importantMetrics')->name('important-metrics');
        Route::get('/customer-insights', 'customerInsights')->name('customer-insights');
    });

    });

 // Production Manager routes
Route::middleware(['auth'])->prefix('production_manager')->name('production_manager.')->group(function (){
    Route::controller(ProductionManagerDashboardController::class)->group(function () {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/production', 'production')->name('production');
    });
});

// Supplier dashboard routes
Route::middleware(['auth'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::controller(SupplierDashboardController::class)->group(function () {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/resources', 'resources')->name('resources');
    });
});


// Vendor dashboard routes
Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::controller(VendorDashboardController::class)->group(function() {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/sales', 'sales')->name('sales');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/retailers', 'retailers')->name('retailers');
    });
});

// Retailer dashboard routes
Route::middleware(['auth'])->prefix('retailer')->name('retailer.')->group(function () {
    Route::controller(RetailerDashboardController::class)->group(function () {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/sales', 'sales')->name('sales');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/ratings', 'ratings')->name('ratings');
    });
});

// HRManager dashboard routes
Route::middleware(['auth'])->prefix('hr_manager')->name('hr_manager.')->group(function () {
    Route::controller(HrManagerDashboardController::class)->group(function () {
        Route::get('/overview', 'overview')->name('overview');
        Route::get('/employees', 'employees')->name('employees');
    });
});

// Vendor-specific features
Route::view('vendor/order-management', 'vendor.order_management')->middleware(['auth', 'verified'])->name('vendor.order_management');
Route::view('vendor/ai-assistant', 'vendor.ai_assistant')->middleware(['auth', 'verified'])->name('vendor.AI_assistant');

// Supplier-specific features
Route::view('supplier/order-statistics', 'supplier.order_statistics')->middleware(['auth', 'verified'])->name('supplier.order_statistics');
Route::view('supplier/delivery-metrics', 'supplier.delivery_metrics')->middleware(['auth', 'verified'])->name('supplier.delivery_metrics');

// HR Manager-specific features
Route::view('hr_manager/workforce-analytics', 'hr_manager.workforce_analytics')->middleware(['auth', 'verified'])->name('hr_manager.workforce_analytics');
Route::view('hr_manager/ai-assistant', 'hr_manager.ai_assistant')->middleware(['auth', 'verified'])->name('hr_manager.AI_assistant');
Route::view('hr_manager/staff-performance', 'hr_manager.staff_performance')->middleware(['auth', 'verified'])->name('hr_manager.staff_performance');

// Production Manager-specific features
Route::view('production_manager/order-management', 'production_manager.order_management')->middleware(['auth', 'verified'])->name('production_manager.order_management');
Route::view('production_manager/inventory-alerts', 'production_manager.inventory_alerts')->middleware(['auth', 'verified'])->name('production_manager.inventory_alerts');
Route::view('production_manager/production-metrics', 'production_manager.production_metrics')->middleware(['auth', 'verified'])->name('production_manager.production_metrics');
Route::view('production_manager/sales_tracking', 'production_manager.sales_tracking')->middleware(['auth', 'verified'])->name('production_manager.sales_tracking');

// Retailer-specific features
Route::view('retailer/order_placement', 'retailer.order_placement')->middleware(['auth', 'verified'])->name('retailer.order_placement');
Route::view('retailer/sales-insights', 'retailer.sales-insights')->middleware(['auth', 'verified'])->name('retailer.sales_insights');

//Route::get('/production_manager/dashboard', function(){
  //  return view('production_manager.dashboard');
//})->name('production_manager.dashboard');

//Route::get('/order_management/completed_orders', function(){
  //  return view('order_management.completed_orders');
//})->name('order_management.completed_orders');

//Route::get('/order_management/pending_orders', function(){
  //  return view('order_management.pending_orders');
//})->name('order_management.pending_orders');


// Communication (common)
Route::view('communication', 'communication')->middleware(['auth', 'verified'])->name('communication');

require __DIR__.'/auth.php';


//Livewire routes
Route::get('counter',Counter::class);
