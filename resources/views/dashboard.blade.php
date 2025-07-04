<x-layouts.app :title="__('Dashboard')">
    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->role }}</p>
            </div>
        </div>

        @php
            $role = auth()->user()->role ?? 'User';
        @endphp

        @if($role === 'admin')
            <!-- Admin Dashboard -->
            <livewire:admin.admin-dashboard-overview />


        @elseif($role === 'production_manager')
            <!-- Production Manager Dashboard -->
            <livewire:production-manager.production-dashboard />

        @elseif($role === 'supplier')
            <!-- Supplier Dashboard -->
            <livewire:supplier.supplier-dashboard />


        @elseif($role === 'hr_manager')
            <!-- HR Manager Dashboard -->
            <livewire:h-r-manager.h-r-dashboard />

        @elseif($role === 'vendor')
            <!-- Vendor Dashboard -->
            <livewire:vendor.vendor-dashboard />


        @elseif($role === 'retailer')
            <!-- Retailer Dashboard -->
            <livewire:retailer.retailer-dashboard />




        @endif
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-layouts.app>
