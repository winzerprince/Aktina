<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('orders', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('orders');

Route::view('stocks', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('stocks');

Route::view('sales', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('sales');

Route::view('products', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('products');

Route::view('invoices', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('invoices');

Route::view('settings', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('settings');

Route::view('retailer/dashboard', 'retailer.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('retailer.dashboard');

Route::view('admin/dashboard', 'admin.dashboard')
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

require __DIR__.'/auth.php';
