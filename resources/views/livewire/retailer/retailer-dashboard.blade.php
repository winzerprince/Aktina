<div class="space-y-6">
    <!-- Sales Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesMetrics['total_orders'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Spent</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($salesMetrics['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($salesMetrics['average_order_value'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesMetrics['orders_this_month'] ?? 0) }}</p>
                    @if(($salesMetrics['order_growth_percentage'] ?? 0) >= 0)
                        <p class="text-sm text-green-600">+{{ $salesMetrics['order_growth_percentage'] }}%</p>
                    @else
                        <p class="text-sm text-red-600">{{ $salesMetrics['order_growth_percentage'] }}%</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Overview</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php $quickStats = $this->getQuickStats(); @endphp
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">{{ $quickStats['pending_orders'] }}</p>
                <p class="text-sm text-blue-800">Pending Orders</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-2xl font-bold text-green-600">{{ $quickStats['total_products_purchased'] }}</p>
                <p class="text-sm text-green-800">Unique Products</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-2xl font-bold text-yellow-600">{{ $quickStats['avg_order_size'] }}</p>
                <p class="text-sm text-yellow-800">Avg Order Size</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-lg font-bold text-purple-600">{{ $quickStats['purchase_frequency'] }}</p>
                <p class="text-sm text-purple-800">Purchase Frequency</p>
            </div>
        </div>
    </div>

    <!-- Sales Trends and Purchase Patterns -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Sales Trends</h3>
                <select wire:model.live="timeFrame" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                    <option value="12">12 Months</option>
                </select>
            </div>
            <div id="sales-trends-chart" class="h-64"></div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Patterns by Day</h3>
            <div id="purchase-patterns-chart" class="h-64"></div>
        </div>
    </div>

    <!-- Top Products and Inventory Health -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Purchased Products</h3>
            <div class="space-y-3">
                @forelse(collect($topProducts)->take(5) as $product)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium text-gray-900">{{ $product['product_name'] }}</p>
                            <p class="text-sm text-gray-500">SKU: {{ $product['product_sku'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $product['total_quantity'] }} units</p>
                            <p class="text-xs text-gray-500">${{ number_format($product['total_spent'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">No purchase history available</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Health Score</h3>
            @php $healthScore = $this->getInventoryHealthScore(); @endphp
            <div class="text-center mb-4">
                <div class="text-4xl font-bold {{ $healthScore >= 80 ? 'text-green-600' : ($healthScore >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $healthScore }}/100
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    @if($healthScore >= 80)
                        Excellent inventory management
                    @elseif($healthScore >= 60)
                        Good inventory practices
                    @else
                        Needs improvement
                    @endif
                </p>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Diversity Score</span>
                    <span>{{ $purchaseAnalytics['inventory_diversity_score'] ?? 0 }}%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Purchase Velocity</span>
                    <span>{{ round(($purchaseAnalytics['purchase_velocity'] ?? 0) * 30, 1) }}/month</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Avg Days Between Orders</span>
                    <span>{{ $purchasePatterns['average_days_between_orders'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Recommendations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Orders</h3>
            <div class="space-y-3">
                @forelse($recentActivity as $order)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded">
                        <div>
                            <p class="font-medium text-gray-900">Order #{{ $order['id'] }}</p>
                            <p class="text-sm text-gray-500">{{ $order['items_count'] }} items - {{ $order['items_preview'] }}</p>
                            <p class="text-xs text-gray-400">{{ $order['created_at']->format('M j, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">${{ number_format($order['total_amount'], 2) }}</p>
                            @php
                                $statusColor = match($order['status']) {
                                    'pending' => 'yellow',
                                    'processing' => 'blue',
                                    'shipped' => 'indigo',
                                    'delivered' => 'green',
                                    'cancelled' => 'red',
                                    default => 'gray'
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                {{ ucfirst($order['status']) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">No recent orders</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Smart Recommendations</h3>
            <div class="space-y-3">
                @forelse(collect($inventoryRecommendations['frequent_replenishment'] ?? [])->take(4) as $recommendation)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded">
                        <div>
                            <p class="font-medium text-gray-900">{{ $recommendation['product']->name ?? 'Product' }}</p>
                            <p class="text-sm text-blue-700">Frequently ordered - {{ $recommendation['days_since_last_order'] ?? 0 }} days ago</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-blue-800">{{ $recommendation['suggested_quantity'] ?? 1 }} units</p>
                            <p class="text-xs text-blue-600">Score: {{ round($recommendation['recommendation_score'] ?? 0) }}%</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">No recommendations available</p>
                @endforelse
                
                @if(count($inventoryRecommendations['low_stock_alerts'] ?? []) > 0)
                    <div class="mt-4">
                        <h4 class="font-medium text-red-700 mb-2">⚠️ Stock Alerts</h4>
                        @foreach(collect($inventoryRecommendations['low_stock_alerts'])->take(2) as $alert)
                            <div class="p-2 bg-red-50 rounded text-sm">
                                <p class="font-medium text-red-800">{{ $alert['product']->name ?? 'Product' }}</p>
                                <p class="text-red-600">Last ordered {{ $alert['days_since_last_order'] ?? 0 }} days ago</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        <button wire:click="refreshData" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh Data
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function() {
    // Sales Trends Chart
    const trendsData = @json($this->getSalesTrendsChartData());
    const trendsOptions = {
        series: trendsData.series,
        chart: {
            type: 'line',
            height: 256,
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

    // Purchase Patterns Chart
    const patternsData = @json($this->getPurchasePatternsChartData());
    const patternsOptions = {
        series: patternsData.series,
        chart: {
            type: 'bar',
            height: 256,
            toolbar: { show: false }
        },
        xaxis: {
            categories: patternsData.categories
        },
        colors: ['#8b5cf6'],
        dataLabels: { enabled: false }
    };
    
    if (typeof ApexCharts !== 'undefined') {
        const patternsChart = new ApexCharts(document.querySelector("#purchase-patterns-chart"), patternsOptions);
        patternsChart.render();
    }
});
</script>
@endpush
