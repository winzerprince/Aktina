<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Vendor Dashboard</h1>
                <p class="text-gray-600">Monitor sales performance and retailer analytics</p>
            </div>
            <div class="flex flex-col md:flex-row gap-4 mt-4 md:mt-0">
                <div>
                    <select wire:model="selectedTimeframe" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($timeframes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="exportSalesReport" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                ${{ number_format($salesMetrics['total_revenue'] ?? 0, 2) }}
                            </div>
                            @if(isset($salesMetrics['growth_rate']) && $salesMetrics['growth_rate'] != 0)
                                <div class="ml-2 flex items-baseline text-sm font-semibold {{ $salesMetrics['growth_rate'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    @if($salesMetrics['growth_rate'] > 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ abs(number_format($salesMetrics['growth_rate'], 1)) }}%
                                </div>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            {{ number_format($salesMetrics['order_count'] ?? 0) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Avg Order Value</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            ${{ number_format($salesMetrics['average_order_value'] ?? 0, 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Active Retailers -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Retailers</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            {{ $retailerMetrics['total_retailers'] ?? 0 }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Trend Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Trend</h3>
            <div id="sales-trend-chart" style="height: 300px;"></div>
        </div>

        <!-- Top Products Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Products</h3>
            <div id="top-products-chart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Detailed Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Retailers -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Retailers</h3>
            <div class="space-y-3">
                @forelse($topRetailers as $retailer)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-indigo-600">{{ substr($retailer['name'], 0, 2) }}</span>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $retailer['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $retailer['order_count'] }} orders</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">${{ number_format($retailer['total_spent'], 2) }}</div>
                            <button wire:click="contactRetailer({{ $retailer['id'] }})" class="text-xs text-indigo-600 hover:text-indigo-800">
                                Contact
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No retailer data available</p>
                @endforelse
            </div>
        </div>

        <!-- Inventory Overview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Overview</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Products</span>
                    <span class="font-semibold">{{ number_format($inventoryOverview['total_products'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Low Stock</span>
                    <span class="font-semibold text-yellow-600">{{ $inventoryOverview['low_stock_count'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Out of Stock</span>
                    <span class="font-semibold text-red-600">{{ $inventoryOverview['out_of_stock'] ?? 0 }}</span>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Inventory Value</span>
                        <span class="font-semibold">${{ number_format($inventoryOverview['inventory_value'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-600">Turnover Rate</span>
                        <span class="font-semibold">{{ number_format($inventoryOverview['turnover_rate'] ?? 0, 1) }}x</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Orders</h3>
            <div class="space-y-3">
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-sm">#{{ $order->order_number ?? $order->id }}</div>
                            <div class="text-xs text-gray-500">{{ $order->created_at->format('M j, Y') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium">${{ number_format($order->total_amount ?? 0, 2) }}</div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($order->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No recent orders</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Retailer Performance Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Retailer Performance Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $retailerMetrics['new_retailers'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">New Retailers</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $retailerMetrics['repeat_customers'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Repeat Customers</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($retailerMetrics['churn_rate'] ?? 0, 1) }}%</div>
                <div class="text-sm text-gray-600">Churn Rate</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($retailerMetrics['retailer_satisfaction'] ?? 0, 1) }}%</div>
                <div class="text-sm text-gray-600">Satisfaction Score</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($salesMetrics['conversion_rate'] ?? 0, 1) }}%</div>
                <div class="text-sm text-gray-600">Conversion Rate</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    renderSalesTrendChart();
    renderTopProductsChart();
});

Livewire.on('chartDataUpdated', () => {
    renderSalesTrendChart();
    renderTopProductsChart();
});

function renderSalesTrendChart() {
    const salesTrend = @js($salesTrend);
    
    const periods = Object.keys(salesTrend);
    const revenueData = periods.map(period => salesTrend[period]?.revenue || 0);
    const orderData = periods.map(period => salesTrend[period]?.orders || 0);
    
    const options = {
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false }
        },
        series: [
            {
                name: 'Revenue ($)',
                data: revenueData,
                yAxisIndex: 0
            },
            {
                name: 'Orders',
                data: orderData,
                yAxisIndex: 1
            }
        ],
        xaxis: {
            categories: periods,
            labels: { rotate: -45 }
        },
        yaxis: [
            {
                title: { text: 'Revenue ($)' },
                labels: {
                    formatter: function (val) {
                        return "$" + val.toLocaleString();
                    }
                }
            },
            {
                opposite: true,
                title: { text: 'Orders' },
                labels: {
                    formatter: function (val) {
                        return val.toFixed(0);
                    }
                }
            }
        ],
        colors: ['#4F46E5', '#10B981'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 6 }
    };
    
    const chart = new ApexCharts(document.querySelector("#sales-trend-chart"), options);
    chart.render();
}

function renderTopProductsChart() {
    const topProducts = @js($salesMetrics['top_products'] ?? []);
    
    if (Object.keys(topProducts).length === 0) {
        document.querySelector("#top-products-chart").innerHTML = '<div class="flex items-center justify-center h-full text-gray-500">No product data available</div>';
        return;
    }
    
    const options = {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false }
        },
        series: [{
            name: 'Units Sold',
            data: Object.values(topProducts)
        }],
        xaxis: {
            categories: Object.keys(topProducts),
            labels: { rotate: -45 }
        },
        colors: ['#F59E0B'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '60%'
            }
        }
    };
    
    const chart = new ApexCharts(document.querySelector("#top-products-chart"), options);
    chart.render();
}
</script>
