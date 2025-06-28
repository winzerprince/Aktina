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

        @if($role === 'Admin')
            <!-- Admin Dashboard -->
      <h1>Welcome to the Admin dashboard</h1>


        @elseif($role === 'Production Manager')
            <h1>Welcome to the Production Manager dashboard</h1>

        @elseif($role === 'Supplier')
            <h1>Welcome to the Supplier dashboard </h1>


        @elseif($role === 'HR Manager')
            <h1>Welcome to the HR Manager dashboard </h1>

        @elseif($role === 'Vendor')
            <h1>Welcome to the Vendor dashboard </h1>


        @elseif($role === 'Retailer')
            <h1>Welcome to the Retailer dashboard </h1>




        @endif
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-layouts.app>
