<div class="space-y-6">
    <!-- Analytics Controls -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
            <div class="flex space-x-2">
                <button wire:click="switchView('trends')" 
                        class="px-4 py-2 rounded-md {{ $selectedView === 'trends' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Trends
                </button>
                <button wire:click="switchView('products')" 
                        class="px-4 py-2 rounded-md {{ $selectedView === 'products' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Products
                </button>
                <button wire:click="switchView('patterns')" 
                        class="px-4 py-2 rounded-md {{ $selectedView === 'patterns' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Patterns
                </button>
                <button wire:click="switchView('seasonal')" 
                        class="px-4 py-2 rounded-md {{ $selectedView === 'seasonal' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Seasonal
                </button>
            </div>
            
            <div class="flex space-x-4">
                @if($selectedView === 'trends' || $selectedView === 'seasonal')
                    <select wire:model.live="timeFrame" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                    </select>
                @endif
                <button wire:click="exportAnalytics" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Export Data
                </button>
            </div>
        </div>
    </div>

    @if($selectedView === 'trends')
        <!-- Sales Trends -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Trends Over Time</h3>
            <div id="sales-trends-chart" class="h-80"></div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-medium text-gray-900 mb-4">Total Spend</h4>
                <p class="text-3xl font-bold text-blue-600">
                    ${{ number_format(collect($salesTrends)->sum('revenue'), 2) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Last {{ $timeFrame }} months</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-medium text-gray-900 mb-4">Total Orders</h4>
                <p class="text-3xl font-bold text-green-600">
                    {{ number_format(collect($salesTrends)->sum('orders')) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Last {{ $timeFrame }} months</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-medium text-gray-900 mb-4">Average Order Value</h4>
                @php
                    $totalRevenue = collect($salesTrends)->sum('revenue');
                    $totalOrders = collect($salesTrends)->sum('orders');
                    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
                @endphp
                <p class="text-3xl font-bold text-purple-600">
                    ${{ number_format($avgOrderValue, 2) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Average per order</p>
            </div>
        </div>

    @elseif($selectedView === 'products')
        <!-- Top Products Analysis -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Purchased Products</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topProducts as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product['product_name'] }}</div>
                                        <div class="text-sm text-gray-500">SKU: {{ $product['product_sku'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($product['total_quantity']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($product['total_spent'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($product['average_price'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $performance = $product['total_spent'] > 1000 ? 'High Value' : 
                                                     ($product['total_quantity'] > 50 ? 'High Volume' : 'Regular');
                                        $color = $performance === 'High Value' ? 'green' : 
                                               ($performance === 'High Volume' ? 'blue' : 'gray');
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $performance }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No product data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($selectedView === 'patterns')
        <!-- Purchase Patterns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Patterns by Day</h3>
                @php $patterns = $this->getPurchasePatternsData(); @endphp
                <div class="space-y-3">
                    @foreach($patterns['day_patterns'] as $day => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ $day }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $count > 0 ? min(100, ($count / max($patterns['day_patterns'])) * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Insights</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium text-blue-800">Average Days Between Orders</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $patterns['avg_days_between_orders'] }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm font-medium text-green-800">Purchase Frequency</p>
                        <p class="text-lg font-bold text-green-600">{{ $patterns['frequency_category'] }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm font-medium text-purple-800">Most Active Day</p>
                        <p class="text-lg font-bold text-purple-600">
                            {{ collect($patterns['day_patterns'])->sortDesc()->keys()->first() ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

    @elseif($selectedView === 'seasonal')
        <!-- Seasonal Trends -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Seasonal Purchase Trends</h3>
            <div id="seasonal-trends-chart" class="h-80"></div>
        </div>

        <!-- Order Status Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
                <div id="order-status-chart" class="h-64"></div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quarterly Performance</h3>
                <div class="space-y-3">
                    @foreach($seasonalTrends as $quarter)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <div>
                                <p class="font-medium text-gray-900">{{ $quarter['quarter'] }}</p>
                                <p class="text-sm text-gray-500">{{ $quarter['orders'] }} orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($quarter['revenue'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function() {
    @if($selectedView === 'trends')
        // Sales Trends Chart
        const trendsData = @json($this->getSalesTrendsChartData());
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
            const trendsChart = new ApexCharts(document.querySelector("#sales-trends-chart"), trendsOptions);
            trendsChart.render();
        }
    @endif

    @if($selectedView === 'seasonal')
        // Seasonal Trends Chart
        const seasonalData = @json($this->getSeasonalTrendsChartData());
        const seasonalOptions = {
            series: seasonalData.series,
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false }
            },
            xaxis: {
                categories: seasonalData.categories
            },
            colors: ['#8b5cf6', '#f59e0b'],
            dataLabels: { enabled: false },
            legend: { position: 'top' }
        };
        
        if (typeof ApexCharts !== 'undefined') {
            const seasonalChart = new ApexCharts(document.querySelector("#seasonal-trends-chart"), seasonalOptions);
            seasonalChart.render();
        }

        // Order Status Chart
        const statusData = @json($this->getOrderStatusChartData());
        const statusOptions = {
            series: statusData.series,
            chart: {
                type: 'donut',
                height: 256,
            },
            labels: statusData.labels,
            colors: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444'],
            legend: { position: 'bottom' }
        };
        
        if (typeof ApexCharts !== 'undefined') {
            const statusChart = new ApexCharts(document.querySelector("#order-status-chart"), statusOptions);
            statusChart.render();
        }
    @endif
});
</script>
@endpush
