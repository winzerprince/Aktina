<x-layouts.app>
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Important Metrics</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Key business metrics and performance indicators</p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->format('F j, Y') }}
                </div>
            </div>
        </div>

        <!-- Critical Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-blue-700 dark:text-blue-300">Revenue Growth</h3>
                        <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">+18.7%</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">vs last quarter</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-500 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-green-700 dark:text-green-300">Order Fulfillment</h3>
                        <p class="text-3xl font-bold text-green-900 dark:text-green-100">96.4%</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">on-time delivery</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-500 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg p-6 border border-yellow-200 dark:border-yellow-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Inventory Turnover</h3>
                        <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">8.2x</p>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">annual turnover</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-500 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg p-6 border border-purple-200 dark:border-purple-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-purple-700 dark:text-purple-300">Customer Satisfaction</h3>
                        <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">4.8/5</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">average rating</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-500 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Trends Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Key Performance Trends</h2>
            <div id="performance-trends-chart" class="h-80"></div>
        </div>

        <!-- Metrics Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Operational Metrics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Operational Excellence</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Production Efficiency</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 94%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">94%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Quality Score</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 97%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">97%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Supply Chain Health</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 87%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">87%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Metrics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Financial Performance</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Gross Margin</span>
                        <span class="text-lg font-bold text-green-600">32.4%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Operating Margin</span>
                        <span class="text-lg font-bold text-blue-600">18.7%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cash Flow</span>
                        <span class="text-lg font-bold text-purple-600">$2.4M</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">ROI</span>
                        <span class="text-lg font-bold text-yellow-600">24.1%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Performance Trends Chart
        var trendsOptions = {
            series: [{
                name: 'Revenue',
                data: [28, 31, 34, 38, 42, 45, 48]
            }, {
                name: 'Orders',
                data: [450, 520, 480, 610, 580, 650, 720]
            }, {
                name: 'Customer Satisfaction',
                data: [4.2, 4.3, 4.5, 4.6, 4.7, 4.8, 4.8]
            }],
            chart: {
                type: 'line',
                height: 320
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']
            },
            colors: ['#3B82F6', '#10B981', '#F59E0B'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            yaxis: [{
                title: {
                    text: 'Revenue ($K)'
                }
            }, {
                opposite: true,
                title: {
                    text: 'Orders'
                }
            }, {
                opposite: true,
                title: {
                    text: 'Rating'
                }
            }]
        };

        var trendsChart = new ApexCharts(document.querySelector("#performance-trends-chart"), trendsOptions);
        trendsChart.render();
    </script>
    @endpush
</x-layouts.app>
