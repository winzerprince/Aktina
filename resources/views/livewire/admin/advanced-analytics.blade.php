<div class="space-y-6" wire:poll.{{ $refreshInterval }}ms="refreshData">
    <!-- Header with Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Advanced Analytics</h2>
                <p class="text-gray-600">Comprehensive business intelligence and performance metrics</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Time Range Selector -->
                <select wire:model.live="timeRange" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="7d">Last 7 Days</option>
                    <option value="30d">Last 30 Days</option>
                    <option value="90d">Last 3 Months</option>
                    <option value="1y">Last Year</option>
                </select>
                
                <!-- Chart Type Selector -->
                <select wire:model.live="chartType" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="line">Line Chart</option>
                    <option value="area">Area Chart</option>
                    <option value="bar">Bar Chart</option>
                    <option value="column">Column Chart</option>
                </select>
                
                <!-- Export Button -->
                <button wire:click="exportData('csv')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Data
                </button>
                
                <!-- Refresh Button -->
                <button wire:click="refreshData" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        
        <!-- Metric Toggles -->
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach(['revenue', 'orders', 'users', 'inventory'] as $metric)
                <button 
                    wire:click="toggleMetric('{{ $metric }}')"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ in_array($metric, $selectedMetrics) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}"
                >
                    {{ ucfirst($metric) }}
                    @if(in_array($metric, $selectedMetrics))
                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @if($this->revenueData)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($this->revenueData['totals']['revenue'], 2) }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($this->performanceMetrics)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Response Time</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->performanceMetrics['response_time'] }}ms</p>
                    </div>
                </div>
            </div>
        @endif

        @if($this->inventoryMetrics)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->inventoryMetrics['summary']['low_stock'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($this->customerSegmentation)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->customerSegmentation['active'] }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Analytics Chart -->
        @if(in_array('revenue', $selectedMetrics) && $this->revenueData)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Analytics</h3>
                <div id="revenue-chart" class="h-80"></div>
            </div>
        @endif

        <!-- Order Trends Chart -->
        @if(in_array('orders', $selectedMetrics) && $this->orderTrendData)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Trends</h3>
                <div id="order-trends-chart" class="h-80"></div>
            </div>
        @endif
    </div>

    <!-- Secondary Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Growth Chart -->
        @if(in_array('users', $selectedMetrics) && $this->userGrowthData)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth</h3>
                <div id="user-growth-chart" class="h-80"></div>
            </div>
        @endif

        <!-- Inventory Overview -->
        @if(in_array('inventory', $selectedMetrics) && $this->inventoryMetrics)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory Overview</h3>
                <div id="inventory-chart" class="h-80"></div>
            </div>
        @endif
    </div>

    <!-- Sales Heatmap -->
    @if($this->salesHeatmapData)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Activity Heatmap</h3>
            <div id="sales-heatmap" class="h-96"></div>
        </div>
    @endif

    <!-- Top Performing Products -->
    @if($this->topPerformingProducts)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Products</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->topPerformingProducts as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->total_sold) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('livewire:navigated', function () {
    initializeCharts();
});

document.addEventListener('DOMContentLoaded', function () {
    initializeCharts();
});

function initializeCharts() {
    // Revenue Chart
    @if(in_array('revenue', $selectedMetrics) && $this->revenueData)
        const revenueOptions = {
            series: @json($this->revenueData['series']),
            chart: {
                type: '{{ $chartType }}',
                height: 320,
                toolbar: { show: true }
            },
            xaxis: {
                categories: @json($this->revenueData['categories'])
            },
            colors: ['#3B82F6', '#10B981', '#F59E0B'],
            stroke: { curve: 'smooth' },
            fill: { opacity: 0.8 }
        };
        
        if (window.revenueChart) {
            window.revenueChart.destroy();
        }
        window.revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), revenueOptions);
        window.revenueChart.render();
    @endif

    // Order Trends Chart
    @if(in_array('orders', $selectedMetrics) && $this->orderTrendData)
        const orderTrendsOptions = {
            series: @json($this->orderTrendData['trend_data']),
            chart: {
                type: 'line',
                height: 320,
                stacked: true
            },
            colors: ['#EF4444', '#F59E0B', '#10B981', '#3B82F6'],
            stroke: { curve: 'smooth' }
        };
        
        if (window.orderTrendsChart) {
            window.orderTrendsChart.destroy();
        }
        window.orderTrendsChart = new ApexCharts(document.querySelector("#order-trends-chart"), orderTrendsOptions);
        window.orderTrendsChart.render();
    @endif

    // User Growth Chart
    @if(in_array('users', $selectedMetrics) && $this->userGrowthData)
        const userGrowthOptions = {
            series: @json($this->userGrowthData['series']),
            chart: {
                type: 'area',
                height: 320
            },
            xaxis: {
                categories: @json($this->userGrowthData['categories'])
            },
            colors: ['#8B5CF6', '#06B6D4'],
            fill: { opacity: 0.6 }
        };
        
        if (window.userGrowthChart) {
            window.userGrowthChart.destroy();
        }
        window.userGrowthChart = new ApexCharts(document.querySelector("#user-growth-chart"), userGrowthOptions);
        window.userGrowthChart.render();
    @endif

    // Inventory Chart
    @if(in_array('inventory', $selectedMetrics) && $this->inventoryMetrics)
        const inventoryOptions = {
            series: @json(array_values($this->inventoryMetrics['category_breakdown'])),
            chart: {
                type: 'donut',
                height: 320
            },
            labels: @json(array_column($this->inventoryMetrics['category_breakdown'], 'category')),
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
        };
        
        if (window.inventoryChart) {
            window.inventoryChart.destroy();
        }
        window.inventoryChart = new ApexCharts(document.querySelector("#inventory-chart"), inventoryOptions);
        window.inventoryChart.render();
    @endif

    // Sales Heatmap
    @if($this->salesHeatmapData)
        const heatmapOptions = {
            series: [{
                name: 'Orders',
                data: @json($this->salesHeatmapData)
            }],
            chart: {
                type: 'heatmap',
                height: 384
            },
            plotOptions: {
                heatmap: {
                    shadeIntensity: 0.5,
                    colorScale: {
                        ranges: [
                            { from: 0, to: 5, color: '#E5E7EB' },
                            { from: 6, to: 20, color: '#93C5FD' },
                            { from: 21, to: 50, color: '#3B82F6' },
                            { from: 51, to: 100, color: '#1D4ED8' }
                        ]
                    }
                }
            },
            xaxis: {
                type: 'category',
                categories: Array.from({length: 24}, (_, i) => i + ':00')
            }
        };
        
        if (window.salesHeatmapChart) {
            window.salesHeatmapChart.destroy();
        }
        window.salesHeatmapChart = new ApexCharts(document.querySelector("#sales-heatmap"), heatmapOptions);
        window.salesHeatmapChart.render();
    @endif
}

// Livewire listeners
document.addEventListener('livewire:init', () => {
    Livewire.on('refresh-charts', () => {
        setTimeout(() => initializeCharts(), 100);
    });
    
    Livewire.on('chart-type-changed', (event) => {
        setTimeout(() => initializeCharts(), 100);
    });
    
    Livewire.on('metrics-updated', (event) => {
        setTimeout(() => initializeCharts(), 100);
    });
    
    Livewire.on('data-refreshed', () => {
        setTimeout(() => initializeCharts(), 100);
    });
    
    Livewire.on('download-export', (event) => {
        const data = event.data;
        const format = event.format;
        const filename = event.filename;
        
        // Convert data to CSV format
        const csv = convertToCSV(data);
        downloadFile(csv, filename + '.' + format, 'text/csv');
    });
});

function convertToCSV(data) {
    // Simplified CSV conversion
    return JSON.stringify(data, null, 2);
}

function downloadFile(content, filename, contentType) {
    const blob = new Blob([content], { type: contentType });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
</script>
@endpush
