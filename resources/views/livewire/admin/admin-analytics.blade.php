<div class="space-y-6">
    <!-- Header with Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Advanced Analytics</h3>
            <p class="mt-1 text-sm text-gray-500">Comprehensive business analytics and insights</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 mt-4 lg:mt-0">
            <!-- Date Range Selector -->
            <select wire:model.live="dateRange" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="7_days">Last 7 Days</option>
                <option value="30_days">Last 30 Days</option>
                <option value="90_days">Last 90 Days</option>
                <option value="1_year">Last Year</option>
            </select>
            
            <!-- Export Button -->
            <button wire:click="exportData('csv')" 
                    wire:loading.attr="disabled"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium disabled:opacity-50">
                <span wire:loading.remove wire:target="exportData">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </span>
                <span wire:loading wire:target="exportData">Exporting...</span>
            </button>
            
            <!-- Refresh Button -->
            <button wire:click="refreshCache" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($revenueMetrics['total'] ?? 0, 2) }}</p>
                    <div class="flex items-center mt-2">
                        @if(($revenueMetrics['change_percent'] ?? 0) >= 0)
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7" />
                            </svg>
                            <span class="text-sm text-green-600">+{{ number_format($revenueMetrics['change_percent'] ?? 0, 1) }}%</span>
                        @else
                            <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10" />
                            </svg>
                            <span class="text-sm text-red-600">{{ number_format($revenueMetrics['change_percent'] ?? 0, 1) }}%</span>
                        @endif
                    </div>
                </div>
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($orderMetrics['total'] ?? 0) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-600">{{ number_format($orderMetrics['completion_rate'] ?? 0, 1) }}% completion</span>
                    </div>
                </div>
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($userMetrics['active'] ?? 0) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-600">{{ number_format($userMetrics['new'] ?? 0) }} new</span>
                    </div>
                </div>
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a4 4 0 110-5.292M15 21h-6" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Inventory Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inventory Value</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($inventoryMetrics['stock_value'] ?? 0, 0) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-orange-600">{{ $inventoryMetrics['low_stock'] ?? 0 }} low stock</span>
                    </div>
                </div>
                <div class="p-3 rounded-full bg-orange-100">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h4 class="text-lg font-medium text-gray-900">Analytics Charts</h4>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <!-- Metric Selector -->
                <select wire:model.live="selectedMetric" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="revenue">Revenue</option>
                    <option value="orders">Orders</option>
                    <option value="users">Users</option>
                    <option value="inventory">Inventory</option>
                </select>
                
                <!-- Chart Type Selector -->
                <select wire:model.live="chartType" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="line">Line Chart</option>
                    <option value="bar">Bar Chart</option>
                    <option value="area">Area Chart</option>
                </select>
            </div>
        </div>
        
        <!-- Main Chart -->
        <div id="analytics-main-chart" class="h-80"></div>
    </div>

    <!-- Secondary Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Breakdown -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Revenue Breakdown</h4>
            <div id="revenue-breakdown-chart" class="h-64"></div>
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h4>
            <div id="order-status-chart" class="h-64"></div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-6">Performance Metrics</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($performanceMetrics['page_load_time'] ?? 0, 2) }}s</div>
                <div class="text-sm text-gray-500">Page Load Time</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($performanceMetrics['uptime_percentage'] ?? 0, 1) }}%</div>
                <div class="text-sm text-gray-500">Uptime</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ number_format($performanceMetrics['error_rate'] ?? 0, 2) }}%</div>
                <div class="text-sm text-gray-500">Error Rate</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($performanceMetrics['response_time'] ?? 0, 3) }}s</div>
                <div class="text-sm text-gray-500">Response Time</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ number_format($performanceMetrics['throughput'] ?? 0) }}</div>
                <div class="text-sm text-gray-500">Requests/min</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h4>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <button wire:click="generateReport('comprehensive')" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md text-sm font-medium text-center">
                Generate Comprehensive Report
            </button>
            <button wire:click="generateReport('financial')" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-md text-sm font-medium text-center">
                Financial Report
            </button>
            <button wire:click="exportData('json')" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-md text-sm font-medium text-center">
                Export JSON
            </button>
            <button wire:click="$refresh" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-md text-sm font-medium text-center">
                Refresh All Data
            </button>
        </div>
    </div>

    <!-- ApexCharts Scripts -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            let mainChart, revenueChart, orderChart;
            
            function initCharts() {
                if (typeof ApexCharts !== 'undefined') {
                    initMainChart();
                    initRevenueBreakdownChart();
                    initOrderStatusChart();
                }
            }
            
            function initMainChart() {
                const chartData = @json($chartData);
                const chartType = @this.get('chartType');
                
                const options = {
                    chart: {
                        type: chartType,
                        height: 320,
                        toolbar: { show: true },
                        animations: { enabled: true }
                    },
                    series: [{
                        name: getMetricName(@this.get('selectedMetric')),
                        data: chartData.values || []
                    }],
                    xaxis: {
                        categories: chartData.labels || [],
                        labels: { rotate: -45 }
                    },
                    colors: ['#3B82F6'],
                    stroke: { width: 2, curve: 'smooth' },
                    fill: { opacity: chartType === 'area' ? 0.3 : 1 },
                    grid: { strokeDashArray: 3 },
                    tooltip: {
                        x: { format: 'dd MMM yyyy' }
                    }
                };
                
                if (mainChart) mainChart.destroy();
                mainChart = new ApexCharts(document.getElementById('analytics-main-chart'), options);
                mainChart.render();
            }
            
            function initRevenueBreakdownChart() {
                const options = {
                    chart: {
                        type: 'donut',
                        height: 256
                    },
                    series: [65, 25, 10],
                    labels: ['Product Sales', 'Services', 'Other'],
                    colors: ['#3B82F6', '#10B981', '#F59E0B'],
                    legend: { position: 'bottom' }
                };
                
                if (revenueChart) revenueChart.destroy();
                revenueChart = new ApexCharts(document.getElementById('revenue-breakdown-chart'), options);
                revenueChart.render();
            }
            
            function initOrderStatusChart() {
                const orderMetrics = @json($orderMetrics);
                
                const options = {
                    chart: {
                        type: 'pie',
                        height: 256
                    },
                    series: [
                        orderMetrics.completed || 0,
                        orderMetrics.pending || 0,
                        (orderMetrics.total || 0) - (orderMetrics.completed || 0) - (orderMetrics.pending || 0)
                    ],
                    labels: ['Completed', 'Pending', 'Others'],
                    colors: ['#10B981', '#F59E0B', '#EF4444'],
                    legend: { position: 'bottom' }
                };
                
                if (orderChart) orderChart.destroy();
                orderChart = new ApexCharts(document.getElementById('order-status-chart'), options);
                orderChart.render();
            }
            
            function getMetricName(metric) {
                const names = {
                    revenue: 'Revenue ($)',
                    orders: 'Orders',
                    users: 'Users',
                    inventory: 'Inventory Movement'
                };
                return names[metric] || 'Metric';
            }
            
            // Initialize charts
            initCharts();
            
            // Listen for updates
            Livewire.on('chartDataUpdated', () => {
                setTimeout(initMainChart, 100);
            });
            
            Livewire.on('analyticsRefreshed', () => {
                setTimeout(initCharts, 100);
            });
        });
    </script>
</div>
