<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-zinc-100">Trends & Predictions</h1>
                <p class="mt-2 text-gray-600 dark:text-zinc-400">Comprehensive analytics and insights for production management</p>
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Sales Trends Section -->
                <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-zinc-200">Sales Performance Analytics</h2>
                        <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Track and analyze production manager sales trends over time</p>
                    </div>
                    <div class="p-6">
                        @livewire('admin.insights.trends-and-predictions.sales-graph')
                    </div>
                </div>

                <!-- Future Components Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Production Efficiency Trends -->
                    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-zinc-200">Production Efficiency</h3>
                            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Monitor production performance metrics</p>
                        </div>
                        <div class="p-6">
                            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <h4 class="mt-2 text-sm font-medium text-gray-900 dark:text-zinc-100">Production Metrics</h4>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Coming soon...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Market Predictions -->
                    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-zinc-200">Market Predictions</h3>
                            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">AI-powered demand forecasting</p>
                        </div>
                        <div class="p-6">
                            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    <h4 class="mt-2 text-sm font-medium text-gray-900 dark:text-zinc-100">Demand Forecasting</h4>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Coming soon...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Overview Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Volume Trends -->
                    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-zinc-200">Order Volume</h3>
                            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Track order patterns</p>
                        </div>
                        <div class="p-6">
                            <div class="h-48 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Coming soon...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Analytics -->
                    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-zinc-200">Customer Analytics</h3>
                            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Customer behavior insights</p>
                        </div>
                        <div class="p-6">
                            <div class="h-48 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Coming soon...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-zinc-200">Performance KPIs</h3>
                            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Key performance indicators</p>
                        </div>
                        <div class="p-6">
                            <div class="h-48 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Coming soon...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
