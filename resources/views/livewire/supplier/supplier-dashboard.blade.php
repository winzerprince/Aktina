<div class="space-y-6" wire:poll.{{ $refreshInterval }}ms>
    <!-- Header with Time Frame Controls -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Supplier Dashboard</h1>
        <div class="flex space-x-2">
            <button wire:click="updateTimeframe('7')" 
                    class="px-3 py-1 text-sm rounded-lg {{ $timeframe === '7' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                7 days
            </button>
            <button wire:click="updateTimeframe('30')" 
                    class="px-3 py-1 text-sm rounded-lg {{ $timeframe === '30' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                30 days
            </button>
            <button wire:click="updateTimeframe('90')" 
                    class="px-3 py-1 text-sm rounded-lg {{ $timeframe === '90' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                90 days
            </button>
            <button wire:click="refresh" 
                    class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Key Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Orders</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Pending Orders</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['pending_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Fulfillment Rate</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['fulfillment_rate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-{{ $performanceMetrics['orders_growth'] >= 0 ? 'green' : 'red' }}-600">
                    {{ $performanceMetrics['orders_growth'] >= 0 ? '+' : '' }}{{ $performanceMetrics['orders_growth'] }}%
                </div>
                <div class="text-sm text-gray-500">Orders Growth</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-{{ $performanceMetrics['revenue_growth'] >= 0 ? 'green' : 'red' }}-600">
                    {{ $performanceMetrics['revenue_growth'] >= 0 ? '+' : '' }}{{ $performanceMetrics['revenue_growth'] }}%
                </div>
                <div class="text-sm text-gray-500">Revenue Growth</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $performanceMetrics['avg_delivery_time'] }}</div>
                <div class="text-sm text-gray-500">Avg Delivery (days)</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $performanceMetrics['on_time_delivery_rate'] }}%</div>
                <div class="text-sm text-gray-500">On-Time Delivery</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Trends Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Trends</h3>
            <div id="orderTrendsChart"></div>
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
            <div id="orderStatusChart"></div>
        </div>
    </div>

    <!-- Resource Metrics and Top Resources -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Resource Supply Metrics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Resource Inventory</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Resources</span>
                    <span class="font-semibold text-gray-900">{{ number_format($resourceMetrics['total_resources']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Low Stock Resources</span>
                    <span class="font-semibold text-yellow-600">{{ number_format($resourceMetrics['low_stock_resources']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Out of Stock</span>
                    <span class="font-semibold text-red-600">{{ number_format($resourceMetrics['out_of_stock_resources']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Inventory Value</span>
                    <span class="font-semibold text-green-600">${{ number_format($resourceMetrics['total_inventory_value'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t">
                    <span class="text-gray-600">Stock Health Score</span>
                    <span class="font-bold text-{{ $resourceMetrics['stock_health_score'] >= 80 ? 'green' : ($resourceMetrics['stock_health_score'] >= 60 ? 'yellow' : 'red') }}-600">
                        {{ $resourceMetrics['stock_health_score'] }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Top Requested Resources -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Requested Resources</h3>
            <div class="space-y-3">
                @foreach($topResources->take(8) as $resource)
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $resource->name }}</div>
                            <div class="text-sm text-gray-500">{{ number_format($resource->total_ordered) }} {{ $resource->unit }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">${{ number_format($resource->total_value, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
            <a href="{{ route('supplier.orders') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                View All Orders →
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $order->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->resource->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($order->quantity) }} {{ $order->resource->unit ?? '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($order->total_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @switch($order->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('processing') bg-blue-100 text-blue-800 @break
                                        @case('shipped') bg-purple-100 text-purple-800 @break
                                        @case('delivered') bg-green-100 text-green-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('cancelled') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order Trends Chart
    const orderTrendsOptions = {
        series: [{
            name: 'Orders',
            data: @json(array_values($orderTrends['orders']->toArray()))
        }, {
            name: 'Revenue',
            data: @json(array_values($orderTrends['revenue']->toArray()))
        }],
        chart: {
            height: 300,
            type: 'line',
            toolbar: { show: false }
        },
        colors: ['#3B82F6', '#10B981'],
        xaxis: {
            categories: @json(array_values($orderTrends['dates']->toArray()))
        },
        yaxis: [{
            title: { text: 'Orders' }
        }, {
            opposite: true,
            title: { text: 'Revenue ($)' }
        }],
        stroke: { curve: 'smooth' }
    };
    new ApexCharts(document.querySelector("#orderTrendsChart"), orderTrendsOptions).render();

    // Order Status Chart
    const statusData = @json($ordersByStatus);
    const orderStatusOptions = {
        series: Object.values(statusData),
        chart: {
            height: 300,
            type: 'donut'
        },
        labels: Object.keys(statusData),
        colors: ['#F59E0B', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444'],
        legend: { position: 'bottom' }
    };
    new ApexCharts(document.querySelector("#orderStatusChart"), orderStatusOptions).render();
});
</script>
@endpush
