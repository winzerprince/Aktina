<div class="space-y-6">
    <!-- Recommendation Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php $stats = $this->getRecommendationStats(); @endphp
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Frequent Replenishment</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['frequent_replenishment'] }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Trending Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['trending_products'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Seasonal Opportunities</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['seasonal_opportunities'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stock Alerts</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['low_stock_alerts'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Performance Score</h3>
        @php $performanceData = $this->getPerformanceScoreData(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ round($performanceData['order_frequency']) }}%</div>
                <p class="text-sm text-gray-600">Order Frequency</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ round($performanceData['consistency']) }}%</div>
                <p class="text-sm text-gray-600">Consistency</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ round($performanceData['diversification']) }}%</div>
                <p class="text-sm text-gray-600">Diversification</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600">{{ round($performanceData['turnover']) }}%</div>
                <p class="text-sm text-gray-600">Turnover</p>
            </div>
        </div>
    </div>

    <!-- Controls and Category Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                <button wire:click="exportRecommendations" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Export
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select wire:model.live="selectedCategory" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">All Categories</option>
                        @foreach(array_keys($purchaseAnalytics['top_categories'] ?? []) as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select wire:model.live="priorityFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">All Priorities</option>
                        <option value="high">High Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="low">Low Priority</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Category Distribution</h3>
            <div id="category-distribution-chart" class="h-64"></div>
        </div>
    </div>

    <!-- Recommendation Sections -->
    <div class="space-y-6">
        <!-- Frequent Replenishment -->
        @if(count($recommendations['frequent_replenishment'] ?? []) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔄 Frequent Replenishment Recommendations</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach(collect($recommendations['frequent_replenishment'])->take(6) as $item)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">{{ $item['product']->name ?? 'Product' }}</h4>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ round($item['recommendation_score'] ?? 0) }}%
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">SKU: {{ $item['product']->sku ?? 'N/A' }}</p>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Order Frequency:</span>
                                    <span class="font-medium">{{ $item['order_frequency'] ?? 0 }}x</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Days Since Last:</span>
                                    <span class="font-medium">{{ $item['days_since_last_order'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Suggested Qty:</span>
                                    <span class="font-medium text-green-600">{{ $item['suggested_quantity'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Low Stock Alerts -->
        @if(count($recommendations['low_stock_alerts'] ?? []) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">⚠️ Low Stock Alerts</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach(collect($recommendations['low_stock_alerts'])->take(6) as $alert)
                        <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">{{ $alert['product']->name ?? 'Product' }}</h4>
                                @php
                                    $urgencyColor = match($alert['urgency_level'] ?? 'Low') {
                                        'High' => 'red',
                                        'Medium' => 'yellow',
                                        default => 'gray'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $urgencyColor }}-100 text-{{ $urgencyColor }}-800">
                                    {{ $alert['urgency_level'] ?? 'Low' }}
                                </span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Last Order:</span>
                                    <span class="font-medium">{{ $alert['days_since_last_order'] ?? 0 }} days ago</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Total Orders:</span>
                                    <span class="font-medium">{{ $alert['total_orders'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Trending Products -->
        @if(count($recommendations['trending_products'] ?? []) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📈 Trending Products</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach(collect($recommendations['trending_products'])->take(6) as $trending)
                        <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">{{ $trending['product']->name ?? 'Product' }}</h4>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    +{{ $trending['growth_rate'] ?? 0 }}%
                                </span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Trend Score:</span>
                                    <span class="font-medium">{{ $trending['trend_score'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Recent Orders:</span>
                                    <span class="font-medium">{{ $trending['recent_orders'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Seasonal Opportunities -->
        @if(count($recommendations['seasonal_opportunities'] ?? []) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🌟 Seasonal Opportunities</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach(collect($recommendations['seasonal_opportunities'])->take(6) as $seasonal)
                        <div class="border border-orange-200 bg-orange-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">{{ $seasonal['product']->name ?? 'Product' }}</h4>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                    {{ round($seasonal['confidence_score'] ?? 0) }}%
                                </span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Seasonal Qty:</span>
                                    <span class="font-medium">{{ $seasonal['seasonal_quantity'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Years Ordered:</span>
                                    <span class="font-medium">{{ $seasonal['years_ordered'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Recommended:</span>
                                    <span class="font-medium text-orange-600">{{ $seasonal['recommended_stock'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function() {
    // Category Distribution Chart
    const categoryData = @json($this->getCategoryDistribution());
    const categoryOptions = {
        series: categoryData.series,
        chart: {
            type: 'pie',
            height: 256,
        },
        labels: categoryData.labels,
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
        legend: { position: 'bottom' }
    };
    
    if (typeof ApexCharts !== 'undefined') {
        const categoryChart = new ApexCharts(document.querySelector("#category-distribution-chart"), categoryOptions);
        categoryChart.render();
    }
});
</script>
@endpush
