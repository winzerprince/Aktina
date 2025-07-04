<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Production Dashboard</h1>
                <p class="text-gray-600">Monitor production efficiency and resource utilization</p>
            </div>
            <div class="flex flex-col md:flex-row gap-4 mt-4 md:mt-0">
                <div>
                    <select wire:model="selectedTimeframe" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($timeframes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model="selectedWarehouse" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Overall Efficiency -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Overall Efficiency</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                {{ number_format($efficiencyMetrics['overall_efficiency'] ?? 0, 1) }}%
                            </div>
                            @if(isset($efficiencyMetrics['efficiency_trend']) && $efficiencyMetrics['efficiency_trend'] != 0)
                                @if($efficiencyMetrics['efficiency_trend'] > 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ number_format(abs($efficiencyMetrics['efficiency_trend']), 1) }}%
                                    </div>
                                @else
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-red-600">
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ number_format(abs($efficiencyMetrics['efficiency_trend']), 1) }}%
                                    </div>
                                @endif
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Fulfillment Rate -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Fulfillment Rate</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            {{ number_format($fulfillmentStats['fulfillment_rate'] ?? 0, 1) }}%
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Orders</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            {{ $fulfillmentStats['pending_orders'] ?? 0 }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Quality Score -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Quality Score</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            {{ number_format($efficiencyMetrics['quality_score'] ?? 0, 1) }}%
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Production Efficiency Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Production Efficiency Trend</h3>
            <div id="efficiency-chart" style="height: 300px;"></div>
        </div>

        <!-- Resource Consumption Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Resource Consumption</h3>
            <div id="resource-chart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Detailed Metrics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Inventory Overview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Overview</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Items</span>
                    <span class="font-semibold">{{ number_format($inventoryOverview['total_items'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Low Stock Items</span>
                    <span class="font-semibold text-yellow-600">{{ $inventoryOverview['low_stock_items'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Out of Stock</span>
                    <span class="font-semibold text-red-600">{{ $inventoryOverview['out_of_stock'] ?? 0 }}</span>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Capacity Utilization</span>
                        <span class="font-semibold">{{ number_format($inventoryOverview['capacity_utilization'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="mt-2 bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $inventoryOverview['capacity_utilization'] ?? 0) }}%"></div>
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

        <!-- Active Alerts -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Active Alerts</h3>
            <div class="space-y-3">
                @php
                    $allAlerts = collect($alerts['production_alerts'] ?? [])
                        ->merge($alerts['inventory_alerts'] ?? [])
                        ->merge($alerts['quality_alerts'] ?? [])
                        ->take(5);
                @endphp
                
                @forelse($allAlerts as $alert)
                    @php
                        $severity = $alert['severity'] ?? 'info';
                        $borderColor = match($severity) {
                            'critical' => 'border-red-400 bg-red-50',
                            'warning' => 'border-yellow-400 bg-yellow-50',
                            'info' => 'border-blue-400 bg-blue-50',
                            default => 'border-gray-400 bg-gray-50'
                        };
                    @endphp
                    <div class="flex items-start justify-between p-3 border-l-4 {{ $borderColor }} rounded-r-lg">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $alert['title'] ?? 'Alert' }}</div>
                            <div class="text-xs text-gray-600 mt-1">{{ $alert['message'] ?? '' }}</div>
                        </div>
                        <div class="flex space-x-2 ml-2">
                            <button wire:click="acknowledgeAlert({{ $alert['id'] ?? 0 }}, '{{ $alert['type'] ?? 'production' }}')"
                                    class="text-xs text-blue-600 hover:text-blue-800">
                                Acknowledge
                            </button>
                            <button wire:click="resolveAlert({{ $alert['id'] ?? 0 }}, '{{ $alert['type'] ?? 'production' }}')"
                                    class="text-xs text-green-600 hover:text-green-800">
                                Resolve
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center py-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="ml-2 text-sm text-gray-600">No active alerts</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Resource Consumption Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Resource Consumption Analysis</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($resourceConsumption['labor_hours'] ?? 0) }}</div>
                <div class="text-sm text-gray-600">Labor Hours</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">${{ number_format($resourceConsumption['energy_consumption']['cost'] ?? 0) }}</div>
                <div class="text-sm text-gray-600">Energy Cost</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($resourceConsumption['utilization_rate'] ?? 0, 1) }}%</div>
                <div class="text-sm text-gray-600">Utilization Rate</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($resourceConsumption['waste_percentage'] ?? 0, 1) }}%</div>
                <div class="text-sm text-gray-600">Waste Percentage</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Production Efficiency Chart
    const efficiencyOptions = {
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false }
        },
        series: [{
            name: 'Efficiency %',
            data: @js($efficiencyMetrics['efficiency_trend'] ?? [85, 87, 83, 89, 91, 88, 92])
        }],
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        },
        colors: ['#4F46E5'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 6 },
        yaxis: { min: 0, max: 100 }
    };
    
    const efficiencyChart = new ApexCharts(document.querySelector("#efficiency-chart"), efficiencyOptions);
    efficiencyChart.render();

    // Resource Consumption Chart
    const resourceOptions = {
        chart: {
            type: 'donut',
            height: 300
        },
        series: @js(array_values($resourceConsumption['cost_breakdown'] ?? [40, 30, 20, 10])),
        labels: @js(array_keys($resourceConsumption['cost_breakdown'] ?? ['Materials', 'Labor', 'Energy', 'Overhead'])),
        colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444'],
        legend: { position: 'bottom' }
    };
    
    const resourceChart = new ApexCharts(document.querySelector("#resource-chart"), resourceOptions);
    resourceChart.render();
});
</script>
