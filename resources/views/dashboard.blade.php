<x-layouts.app :title="__('Dashboard')">
    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <x-ui.status-badge status="online" />
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
            </div>
        </div>

        @php
            $role = auth()->user()->role ?? 'User';
        @endphp

        @if($role === 'Admin')
            <!-- Admin Dashboard -->
      <h1>Welcom to the Admin dashboard</h1>

        @elseif($role === 'Retailer')
            <!-- Retailer Dashboard -->
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/3">
                    <x-ui.stats-card
                        title="Sales Today"
                        value="$8,230"
                        change="15%"
                        changeType="positive"
                        icon="cart"
                        iconBg="success"
                    />
                </div>

                <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/3">
                    <x-ui.stats-card
                        title="Orders"
                        value="156"
                        change="8%"
                        changeType="positive"
                        icon="paper-diploma"
                        iconBg="primary"
                    />
                </div>

                <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/3">
                    <x-ui.stats-card
                        title="Customers"
                        value="89"
                        change="5%"
                        changeType="positive"
                        icon="world"
                        iconBg="warning"
                    />
                </div>
            </div>

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full max-w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
                    <x-ui.chart-card
                        title="Sales Performance"
                        chartId="retailerChart"
                        chartType="bar"
                        description="Weekly sales analysis"
                        :stats="[
                            ['label' => 'Sales', 'value' => '$8.2K', 'progress' => ['value' => 75, 'max' => 100]],
                            ['label' => 'Orders', 'value' => '156', 'progress' => ['value' => 62, 'max' => 100]]
                        ]"
                    />
                </div>

                <div class="w-full max-w-full px-3 lg:w-1/2 lg:flex-none">
                    <x-ui.card title="Recent Orders" subtitle="Latest customer orders">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <h6 class="font-semibold text-gray-800 dark:text-white">Order #1234</h6>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">John Doe - $125.50</p>
                                </div>
                                <x-ui.status-badge status="success" />
                            </div>

                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <h6 class="font-semibold text-gray-800 dark:text-white">Order #1235</h6>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Jane Smith - $89.00</p>
                                </div>
                                <x-ui.status-badge status="pending" />
                            </div>

                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <h6 class="font-semibold text-gray-800 dark:text-white">Order #1236</h6>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Bob Johnson - $67.25</p>
                                </div>
                                <x-ui.status-badge status="success" />
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <x-ui.button variant="outline" class="w-full">
                                View All Orders
                            </x-ui.button>
                        </div>
                    </x-ui.card>
                </div>
            </div>

        @else
            <!-- Default Dashboard -->
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/2">
                    <x-ui.stats-card
                        title="My Tasks"
                        value="12"
                        change="3"
                        changeType="positive"
                        icon="paper-diploma"
                        iconBg="primary"
                    />
                </div>

                <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/2">
                    <x-ui.stats-card
                        title="Completed"
                        value="8"
                        change="2"
                        changeType="positive"
                        icon="check"
                        iconBg="success"
                    />
                </div>
            </div>

            <div class="flex flex-wrap -mx-3">
                <div class="w-full max-w-full px-3">
                    <x-ui.card title="Welcome" subtitle="Getting started with your dashboard">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Welcome to your personalized dashboard. Here you can view your tasks, track progress, and manage your workflow.
                        </p>

                        <x-ui.progress-bar
                            label="Profile Completion"
                            :value="70"
                            color="primary"
                            showPercentage="true"
                            class="mb-4"
                        />

                        <x-ui.button variant="primary">
                            Complete Profile
                        </x-ui.button>
                    </x-ui.card>
                </div>
            </div>
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
