<x-layouts.app>
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Trends and Predictions</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">AI-powered insights and forecasting for business planning</p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->format('F j, Y') }}
                </div>
            </div>
        </div>

        <!-- Prediction Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900 dark:to-indigo-800 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-500 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 ml-3">Revenue Forecast</h3>
                </div>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">$3.2M</p>
                <p class="text-blue-700 dark:text-blue-300 text-sm mt-1">Next Quarter (+15.3%)</p>
                <div class="mt-4 text-xs text-blue-600 dark:text-blue-400">
                    High confidence prediction based on seasonal trends
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900 dark:to-emerald-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-500 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 ml-3">Demand Prediction</h3>
                </div>
                <p class="text-3xl font-bold text-green-900 dark:text-green-100">4,850</p>
                <p class="text-green-700 dark:text-green-300 text-sm mt-1">Units Next Month</p>
                <div class="mt-4 text-xs text-green-600 dark:text-green-400">
                    Peak demand expected in industrial valves
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-orange-100 dark:from-yellow-900 dark:to-orange-800 rounded-lg p-6 border border-yellow-200 dark:border-yellow-700">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-yellow-500 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 ml-3">Risk Alert</h3>
                </div>
                <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">Medium</p>
                <p class="text-yellow-700 dark:text-yellow-300 text-sm mt-1">Supply Chain Risk</p>
                <div class="mt-4 text-xs text-yellow-600 dark:text-yellow-400">
                    Monitor steel prices and availability
                </div>
            </div>
        </div>

        <!-- Forecasting Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Revenue & Demand Forecasting</h2>
            <div id="forecasting-chart" class="h-80"></div>
        </div>

        <!-- Trend Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Market Trends -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Market Trends</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                        <div>
                            <h4 class="font-medium text-green-800 dark:text-green-200">Industrial Automation</h4>
                            <p class="text-sm text-green-600 dark:text-green-400">Growing demand for smart manufacturing</p>
                        </div>
                        <span class="px-2 py-1 bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-200 rounded text-sm font-medium">+23%</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <div>
                            <h4 class="font-medium text-blue-800 dark:text-blue-200">Sustainable Materials</h4>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Eco-friendly product preferences rising</p>
                        </div>
                        <span class="px-2 py-1 bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200 rounded text-sm font-medium">+18%</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                        <div>
                            <h4 class="font-medium text-yellow-800 dark:text-yellow-200">Supply Chain Resilience</h4>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">Focus on risk mitigation strategies</p>
                        </div>
                        <span class="px-2 py-1 bg-yellow-200 dark:bg-yellow-700 text-yellow-800 dark:text-yellow-200 rounded text-sm font-medium">+31%</span>
                    </div>
                </div>
            </div>

            <!-- AI Recommendations -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">AI Recommendations</h3>
                <div class="space-y-4">
                    <div class="p-4 border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900">
                        <h4 class="font-medium text-blue-800 dark:text-blue-200">Inventory Optimization</h4>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">Increase valve inventory by 20% before Q4 peak season</p>
                    </div>
                    <div class="p-4 border-l-4 border-green-500 bg-green-50 dark:bg-green-900">
                        <h4 class="font-medium text-green-800 dark:text-green-200">Supplier Diversification</h4>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">Add 2-3 backup suppliers for critical materials</p>
                    </div>
                    <div class="p-4 border-l-4 border-purple-500 bg-purple-50 dark:bg-purple-900">
                        <h4 class="font-medium text-purple-800 dark:text-purple-200">Market Expansion</h4>
                        <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">Consider expanding into renewable energy sector</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Forecasting Chart
        var forecastOptions = {
            series: [{
                name: 'Historical Revenue',
                data: [1200, 1350, 1180, 1420, 1380, 1650, 1540]
            }, {
                name: 'Predicted Revenue',
                data: [null, null, null, null, null, 1650, 1850, 2100, 2350, 2280, 2650, 2950]
            }, {
                name: 'Demand (Units)',
                data: [2800, 3100, 2900, 3400, 3200, 3700, 3500, 3900, 4200, 4100, 4500, 4850]
            }],
            chart: {
                type: 'line',
                height: 320
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            colors: ['#3B82F6', '#F59E0B', '#10B981'],
            stroke: {
                curve: 'smooth',
                width: [3, 3, 2],
                dashArray: [0, 5, 0]
            },
            fill: {
                opacity: [1, 0.3, 0.8]
            },
            markers: {
                size: [0, 6, 4]
            }
        };

        var forecastChart = new ApexCharts(document.querySelector("#forecasting-chart"), forecastOptions);
        forecastChart.render();
    </script>
    @endpush
</x-layouts.app>
