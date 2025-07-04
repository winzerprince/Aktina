<x-layouts.app>
    <x-slot:title>{{ __('Order Statistics') }}</x-slot:title>

    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Order Statistics</h1>
                <p class="text-gray-600 dark:text-gray-400">Track your order performance and delivery metrics</p>
            </div>
            <div class="flex gap-2">
                <x-ui.button variant="outline" size="sm">7 Days</x-ui.button>
                <x-ui.button variant="primary" size="sm">30 Days</x-ui.button>
                <x-ui.button variant="outline" size="sm">90 Days</x-ui.button>
            </div>
        </div>

        <!-- Key Statistics -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Total Orders"
                    value="142"
                    change="15%"
                    changeType="positive"
                    icon="shopping-bag"
                    iconBg="primary"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Completed Orders"
                    value="128"
                    change="12%"
                    changeType="positive"
                    icon="check-circle"
                    iconBg="success"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Success Rate"
                    value="90.1%"
                    change="2.3%"
                    changeType="positive"
                    icon="chart-bar"
                    iconBg="success"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Avg Delivery Time"
                    value="2.4 days"
                    change="0.2 days"
                    changeType="negative"
                    icon="clock"
                    iconBg="warning"
                />
            </div>
        </div>

        <!-- Simple Chart Container -->
        <div class="w-full mb-6">
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Trends</h3>
                    <div id="orderTrendsChart" class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Order trends chart will be rendered here</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    @push('scripts')
    <script>
        // Simple ApexCharts implementation will be added later
        document.addEventListener('DOMContentLoaded', function() {
            // Chart placeholder for now
            console.log('Order statistics charts ready to be implemented');
        });
    </script>
    @endpush
</x-layouts.app>
