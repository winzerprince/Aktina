<x-layouts.app>
    <x-slot:title>{{ __('Delivery Metrics') }}</x-slot:title>

    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Delivery Metrics</h1>
                <p class="text-gray-600 dark:text-gray-400">Monitor delivery performance and efficiency</p>
            </div>
            <x-ui.button variant="primary" icon="download">
                Export Report
            </x-ui.button>
        </div>

        <!-- Performance Metrics -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/3">
                <x-ui.stats-card
                    title="On-Time Delivery"
                    value="92.3%"
                    change="3.2%"
                    changeType="positive"
                    icon="truck"
                    iconBg="success"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/3">
                <x-ui.stats-card
                    title="Avg Response Time"
                    value="4.2 hrs"
                    change="0.8 hrs"
                    changeType="negative"
                    icon="clock"
                    iconBg="warning"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/3">
                <x-ui.stats-card
                    title="Customer Rating"
                    value="4.7/5"
                    change="0.2"
                    changeType="positive"
                    icon="star"
                    iconBg="primary"
                />
            </div>
        </div>

        <!-- Delivery Status Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Deliveries -->
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Deliveries</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">#DEL-001</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Aktina Technologies</p>
                            </div>
                            <x-ui.status-badge status="completed" size="sm" />
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">#DEL-002</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Aktina Technologies</p>
                            </div>
                            <x-ui.status-badge status="processing" size="sm" />
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">#DEL-003</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Aktina Technologies</p>
                            </div>
                            <x-ui.status-badge status="pending" size="sm" />
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Performance Chart Placeholder -->
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Trend</h3>
                    <div id="performanceChart" class="h-48 flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-center">
                            <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Performance chart coming soon</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
