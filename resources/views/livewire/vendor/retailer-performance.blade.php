<div class="space-y-6">
    <!-- Performance Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Retailers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($performanceMetrics['total_retailers'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Retailers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($performanceMetrics['active_retailers'] ?? 0) }}</p>
                    <p class="text-sm text-green-600">{{ round($performanceMetrics['active_percentage'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Orders/Retailer</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $performanceMetrics['average_orders_per_retailer'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Retention Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ round($performanceMetrics['retailer_retention_rate'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select wire:model.live="selectedMetric" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="revenue">Revenue</option>
                        <option value="orders">Order Count</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Frame</label>
                    <select wire:model.live="timeFrame" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                    </select>
                </div>
            </div>
            <button wire:click="exportRetailerData" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Export Data
            </button>
        </div>
    </div>

    <!-- Growth Trends Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Retailer Growth Trends</h3>
        <div id="retailer-growth-chart" class="h-80"></div>
    </div>

    <!-- Top Retailers Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Top Performing Retailers</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retailer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Order Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topRetailers as $retailer)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($retailer['name'], 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $retailer['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $retailer['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($retailer['total_orders']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($retailer['total_revenue'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($retailer['average_order_value'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $performance = $retailer['total_revenue'] > 50000 ? 'Excellent' : 
                                                 ($retailer['total_revenue'] > 25000 ? 'Good' : 
                                                 ($retailer['total_revenue'] > 10000 ? 'Fair' : 'Needs Attention'));
                                    $color = $performance === 'Excellent' ? 'green' : 
                                           ($performance === 'Good' ? 'blue' : 
                                           ($performance === 'Fair' ? 'yellow' : 'red'));
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $performance }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No retailer data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Frequency Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Frequency Distribution</h3>
            <div id="order-frequency-chart" class="h-64"></div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Order Activity</h3>
            <div class="space-y-3">
                @forelse(collect($orderFrequency)->take(5) as $retailer)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium text-gray-900">{{ $retailer['retailer_name'] }}</p>
                            <p class="text-sm text-gray-500">{{ $retailer['order_count'] }} orders</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium">{{ $retailer['frequency_category'] }}</p>
                            <p class="text-xs text-gray-500">{{ round($retailer['avg_days_since_last_order']) }} days ago</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">No order frequency data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function() {
    // Retailer Growth Trends Chart
    const growthData = @json($this->getRetailerPerformanceChartData());
    const growthOptions = {
        series: growthData.series,
        chart: {
            type: 'line',
            height: 320,
            toolbar: { show: false }
        },
        xaxis: {
            categories: growthData.categories
        },
        stroke: { 
            curve: 'smooth',
            width: 3
        },
        colors: ['#3b82f6', '#10b981'],
        dataLabels: { enabled: false },
        legend: { position: 'top' }
    };
    
    if (typeof ApexCharts !== 'undefined') {
        const growthChart = new ApexCharts(document.querySelector("#retailer-growth-chart"), growthOptions);
        growthChart.render();
    }

    // Order Frequency Chart
    const frequencyData = @json($this->getOrderFrequencyChartData());
    const frequencyOptions = {
        series: frequencyData.series,
        chart: {
            type: 'donut',
            height: 256,
        },
        labels: frequencyData.labels,
        colors: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
        legend: { position: 'bottom' }
    };
    
    if (typeof ApexCharts !== 'undefined') {
        const frequencyChart = new ApexCharts(document.querySelector("#order-frequency-chart"), frequencyOptions);
        frequencyChart.render();
    }
});
</script>
@endpush
