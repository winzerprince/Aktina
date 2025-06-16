<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Default dashboard - redirects to role-specific dashboard
Route::get('dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }

    return redirect()->route($user->getDashboardRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

// Role-specific dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard/supplier', App\Livewire\Dashboard\SupplierDashboard::class)
        ->middleware('role:supplier')->name('dashboard.supplier');

    Route::get('dashboard/production-manager', function () {
        return view('dashboards.production-manager');
    })->middleware('role:production_manager')->name('dashboard.production-manager');

    Route::get('dashboard/hr-manager', function () {
        return view('dashboards.hr-manager');
    })->middleware('role:hr_manager')->name('dashboard.hr-manager');

    Route::get('dashboard/system-administrator', function () {
        return view('dashboards.system-administrator');
    })->middleware('role:system_administrator')->name('dashboard.system-administrator');

    Route::get('dashboard/wholesaler', function () {
        return view('dashboards.wholesaler');
    })->middleware('role:wholesaler')->name('dashboard.wholesaler');

    Route::get('dashboard/retailer', function () {
        return view('dashboards.retailer');
    })->middleware('role:retailer')->name('dashboard.retailer');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
