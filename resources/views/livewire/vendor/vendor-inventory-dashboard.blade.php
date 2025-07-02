<div class="space-y-6">
    <!-- Inventory Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($turnoverMetrics['total_products'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($turnoverMetrics['active_products'] ?? 0) }}</p>
                    <p class="text-sm text-green-600">{{ round($turnoverMetrics['active_percentage'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Turnover Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $turnoverMetrics['average_turnover_rate'] ?? 0 }}x</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Health Score</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $turnoverMetrics['inventory_health_score'] ?? 0 }}/100</p>
                </div>
            </div>
        </div>
    </div>

    <!-- View Controls -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex space-x-4">
                <div class="flex space-x-2">
                    <button wire:click="switchView('overview')" 
                            class="px-4 py-2 rounded-md {{ $selectedView === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        Overview
                    </button>
                    <button wire:click="switchView('movement')" 
                            class="px-4 py-2 rounded-md {{ $selectedView === 'movement' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        Product Movement
                    </button>
                    <button wire:click="switchView('reorder')" 
                            class="px-4 py-2 rounded-md {{ $selectedView === 'reorder' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        Reorder Recommendations
                    </button>
                </div>
                
                @if($selectedView === 'movement')
                    <div class="flex space-x-2">
                        <select wire:model.live="sortBy" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="turnover_rate">Turnover Rate</option>
                            <option value="total_sold">Total Sold</option>
                            <option value="current_stock">Current Stock</option>
                            <option value="total_revenue">Revenue</option>
                        </select>
                        <select wire:model.live="sortDirection" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select>
                    </div>
                @endif
            </div>
            
            <button wire:click="exportInventoryData" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Export Data
            </button>
        </div>
    </div>

    @if($selectedView === 'overview')
        <!-- Turnover Trends Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Inventory Turnover Trends</h3>
                <select wire:model.live="timeFrame" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                    <option value="12">12 Months</option>
                </select>
            </div>
            <div id="turnover-trends-chart" class="h-80"></div>
        </div>

        <!-- Stock Distribution and Movement Category Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Level Distribution</h3>
                <div id="stock-distribution-chart" class="h-64"></div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Movement Categories</h3>
                <div id="movement-category-chart" class="h-64"></div>
            </div>
        </div>

        <!-- Reorder Priority Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reorder Priority Summary</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php $priorities = $this->getReorderPriorityData(); @endphp
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">{{ $priorities['critical'] }}</p>
                    <p class="text-sm text-red-800">Critical</p>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <p class="text-2xl font-bold text-orange-600">{{ $priorities['high'] }}</p>
                    <p class="text-sm text-orange-800">High</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600">{{ $priorities['medium'] }}</p>
                    <p class="text-sm text-yellow-800">Medium</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ $priorities['low'] }}</p>
                    <p class="text-sm text-green-800">Low</p>
                </div>
            </div>
        </div>

    @elseif($selectedView === 'movement')
        <!-- Product Movement Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Product Movement Analysis</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turnover Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Movement Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($productMovement as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product['name'] }}</div>
                                        <div class="text-sm text-gray-500">SKU: {{ $product['sku'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($product['current_stock']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($product['total_sold']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product['turnover_rate'] }}x
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = match($product['movement_category']) {
                                            'Fast Moving' => 'green',
                                            'Medium Moving' => 'blue',
                                            'Slow Moving' => 'yellow',
                                            default => 'red'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $product['movement_category'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColor = match($product['reorder_status']) {
                                            'Out of Stock' => 'red',
                                            'Critical' => 'red',
                                            'Low Stock' => 'yellow',
                                            'Overstocked' => 'blue',
                                            default => 'green'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                        {{ $product['reorder_status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No product movement data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($selectedView === 'reorder')
        <!-- Reorder Recommendations Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Reorder Recommendations</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommended Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Velocity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Est. Stockout</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reorderRecommendations as $recommendation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $recommendation['name'] }}</div>
                                        <div class="text-sm text-gray-500">SKU: {{ $recommendation['sku'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($recommendation['current_stock']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($recommendation['recommended_quantity']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ round($recommendation['sales_velocity'], 2) }}/day
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($recommendation['estimated_stockout_date'])
                                        {{ \Carbon\Carbon::parse($recommendation['estimated_stockout_date'])->format('M d, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = match($recommendation['priority']) {
                                            'Critical' => 'red',
                                            'High' => 'orange',
                                            'Medium' => 'yellow',
                                            default => 'green'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $recommendation['priority'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No reorder recommendations at this time</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function() {
    @if($selectedView === 'overview')
        // Turnover Trends Chart
        const trendsData = @json($this->getTurnoverTrendsChartData());
        const trendsOptions = {
            series: trendsData.series,
            chart: {
                type: 'line',
                height: 320,
                toolbar: { show: false }
            },
            xaxis: {
                categories: trendsData.categories
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
            const trendsChart = new ApexCharts(document.querySelector("#turnover-trends-chart"), trendsOptions);
            trendsChart.render();
        }

        // Stock Distribution Chart
        const stockData = @json($this->getStockDistributionChartData());
        const stockOptions = {
            series: stockData.series,
            chart: {
                type: 'donut',
                height: 256,
            },
            labels: stockData.labels,
            colors: stockData.colors,
            legend: { position: 'bottom' }
        };
        
        if (typeof ApexCharts !== 'undefined') {
            const stockChart = new ApexCharts(document.querySelector("#stock-distribution-chart"), stockOptions);
            stockChart.render();
        }

        // Movement Category Chart
        const movementData = @json($this->getMovementCategoryChartData());
        const movementOptions = {
            series: movementData.series,
            chart: {
                type: 'pie',
                height: 256,
            },
            labels: movementData.labels,
            colors: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
            legend: { position: 'bottom' }
        };
        
        if (typeof ApexCharts !== 'undefined') {
            const movementChart = new ApexCharts(document.querySelector("#movement-category-chart"), movementOptions);
            movementChart.render();
        }
    @endif
});
</script>
@endpush
