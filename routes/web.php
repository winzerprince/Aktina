<?php

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

Route::view('retailer/dashboard', 'retailer.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('retailer.dashboard');

Route::redirect('admin/dashboard', 'admin/home')
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');

Route::view('retailer/feedback', 'retailer.feedback')
    ->middleware(['auth', 'verified'])
    ->name('retailer.feedback');

Route::view('production_manager/dashboard', 'production_manager.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('production_manager.dashboard');

Route::view('hr_manager/dashboard', 'hr_manager.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('hr_manager.dashboard');

Route::view('supplier/dashboard', 'supplier.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('supplier.dashboard');

Route::view('vendor/dashboard', 'vendor.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('vendor.dashboard');

Route::view('access-denied', 'access_denied')
    ->middleware(['auth', 'verified'])
    ->name('access.denied');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Admin-specific features
Route::view('admin/home', 'admin.home')->middleware(['auth', 'verified'])->name('admin.home');
Route::view('admin/sales', 'admin.sales')->middleware(['auth', 'verified'])->name('admin.sales');
Route::view('admin/user-management', 'admin.user-management')->middleware(['auth', 'verified'])->name('admin.user-management');
Route::view('admin/insights', 'admin.insights')->middleware(['auth', 'verified'])->name('admin.insights');

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
