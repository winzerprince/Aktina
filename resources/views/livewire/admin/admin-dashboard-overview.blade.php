<div class="space-y-6" 
     wire:poll.{{ $refreshInterval }}ms="loadRealtimeData">
    
    <!-- Header with Time Range Filter and Real-time Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Dashboard Overview</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Real-time system monitoring and analytics</p>
                @if($lastUpdated)
                    <p class="text-xs text-green-600 mt-1">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                        Live • Last updated: {{ $lastUpdated }}
                    </p>
                @endif
            </div>
            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                <select wire:model.live="timeRange" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="7d">Last 7 Days</option>
                    <option value="30d">Last 30 Days</option>
                    <option value="90d">Last 90 Days</option>
                    <option value="1y">Last Year</option>
                </select>
                
                <button wire:click="loadRealtimeData" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Users -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm uppercase tracking-wide">Total Users</p>
                    <p class="text-2xl font-bold">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="text-blue-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm uppercase tracking-wide">Active Users</p>
                    <p class="text-2xl font-bold">{{ number_format($activeUsers) }}</p>
                </div>
                <div class="text-green-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm uppercase tracking-wide">Total Orders</p>
                    <p class="text-2xl font-bold">{{ number_format($totalOrders) }}</p>
                </div>
                <div class="text-purple-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm uppercase tracking-wide">Pending Orders</p>
                    <p class="text-2xl font-bold">{{ number_format($pendingOrders) }}</p>
                </div>
                <div class="text-yellow-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm uppercase tracking-wide">Total Revenue</p>
                    <p class="text-2xl font-bold">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="text-indigo-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">System Health</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($systemHealth as $service => $health)
                <div class="flex items-center p-4 border rounded-lg {{ $health['status'] === 'healthy' ? 'border-green-200 bg-green-50' : ($health['status'] === 'warning' ? 'border-yellow-200 bg-yellow-50' : 'border-red-200 bg-red-50') }}">
                    <div class="flex-shrink-0">
                        @if($health['status'] === 'healthy')
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @elseif($health['status'] === 'warning')
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $service) }}</p>
                        <p class="text-xs text-gray-600">{{ $health['message'] }}</p>
                        @if(isset($health['response_time']))
                            <p class="text-xs text-gray-500">{{ $health['response_time'] }}ms</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Trends Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Trends</h3>
                <button wire:click="exportData('orders')" 
                        class="text-sm text-blue-600 hover:text-blue-800">Export</button>
            </div>
            <div id="order-trends-chart" class="h-80"></div>
        </div>

        <!-- User Growth Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Growth</h3>
                <button wire:click="exportData('users')" 
                        class="text-sm text-blue-600 hover:text-blue-800">Export</button>
            </div>
            <div id="user-growth-chart" class="h-80"></div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue Analysis</h3>
                <button wire:click="exportData('revenue')" 
                        class="text-sm text-blue-600 hover:text-blue-800">Export</button>
            </div>
            <div id="revenue-chart" class="h-80"></div>
        </div>

        <!-- Role Distribution Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Role Distribution</h3>
                <button wire:click="exportData('roles')" 
                        class="text-sm text-blue-600 hover:text-blue-800">Export</button>
            </div>
            <div id="role-distribution-chart" class="h-80"></div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Activities</h3>
        <div class="space-y-4">
            @forelse($recentActivities as $activity)
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-{{ $activity['color'] ?? 'gray' }}-100 rounded-full flex items-center justify-center">
                            @if(($activity['icon'] ?? '') === 'shopping-cart')
                                <svg class="w-5 h-5 text-{{ $activity['color'] ?? 'gray' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z" />
                                </svg>
                            @elseif(($activity['icon'] ?? '') === 'user-plus')
                                <svg class="w-5 h-5 text-{{ $activity['color'] ?? 'gray' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-{{ $activity['color'] ?? 'gray' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['title'] ?? $activity['message'] ?? 'Activity' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activity['description'] ?? $activity['message'] ?? 'Recent activity' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">{{ $activity['time'] ?? 'Just now' }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activities</p>
            @endforelse
        </div>
    </div>

    <!-- Real-time Alerts Section -->
    @if(!empty($lowStockAlerts))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Real-time Inventory Alerts</h3>
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ count($lowStockAlerts) }} alerts
                </span>
            </div>
            <div class="space-y-3">
                @foreach($lowStockAlerts as $alert)
                    <div class="flex items-center justify-between p-3 {{ $alert['severity'] === 'critical' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }} rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 {{ $alert['severity'] === 'critical' ? 'text-red-500' : 'text-yellow-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 13.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $alert['item'] }}</p>
                                <p class="text-xs text-gray-500">Current stock: {{ $alert['current_stock'] }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium {{ $alert['severity'] === 'critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                            {{ ucfirst($alert['severity']) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    @if($loading)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Refreshing dashboard...</span>
            </div>
        </div>
    @endif

    <!-- ApexCharts Scripts -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            initAdminCharts();
        });

        function initAdminCharts() {
            if (typeof ApexCharts === 'undefined') return;

            // Order Trends Chart
            const orderTrendsData = @json($orderTrends);
            const orderTrendsOptions = {
                chart: {
                    type: 'line',
                    height: 320,
                    toolbar: { show: true }
                },
                series: [{
                    name: 'Orders',
                    data: orderTrendsData.orders || []
                }, {
                    name: 'Revenue',
                    data: orderTrendsData.revenue || [],
                    yAxis: 1
                }],
                xaxis: {
                    categories: orderTrendsData.dates || []
                },
                yaxis: [{
                    title: { text: 'Orders' }
                }, {
                    opposite: true,
                    title: { text: 'Revenue ($)' }
                }],
                colors: ['#3B82F6', '#10B981']
            };
            new ApexCharts(document.querySelector("#order-trends-chart"), orderTrendsOptions).render();

            // User Growth Chart
            const userGrowthData = @json($userGrowth);
            const userGrowthOptions = {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: true }
                },
                series: [{
                    name: 'New Users',
                    data: userGrowthData.users || []
                }],
                xaxis: {
                    categories: userGrowthData.dates || []
                },
                colors: ['#8B5CF6'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        colorStops: [{
                            offset: 0,
                            color: '#8B5CF6',
                            opacity: 0.8
                        }, {
                            offset: 100,
                            color: '#8B5CF6',
                            opacity: 0.1
                        }]
                    }
                }
            };
            new ApexCharts(document.querySelector("#user-growth-chart"), userGrowthOptions).render();

            // Revenue Chart
            const revenueData = @json($revenueChart);
            const revenueOptions = {
                chart: {
                    type: 'bar',
                    height: 320,
                    stacked: true,
                    toolbar: { show: true }
                },
                series: [{
                    name: 'Completed',
                    data: revenueData.completed || []
                }, {
                    name: 'Pending',
                    data: revenueData.pending || []
                }],
                xaxis: {
                    categories: revenueData.dates || []
                },
                colors: ['#10B981', '#F59E0B']
            };
            new ApexCharts(document.querySelector("#revenue-chart"), revenueOptions).render();

            // Role Distribution Chart
            const roleData = @json($roleDistribution);
            const roleOptions = {
                chart: {
                    type: 'donut',
                    height: 320
                },
                series: roleData.data || [],
                labels: roleData.labels || [],
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4']
            };
            new ApexCharts(document.querySelector("#role-distribution-chart"), roleOptions).render();
        }

        // Refresh charts when data updates
        window.addEventListener('livewire:navigated', function () {
            setTimeout(initAdminCharts, 100);
        });
    </script>
</div>
