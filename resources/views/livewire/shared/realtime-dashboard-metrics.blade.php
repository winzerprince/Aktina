<div class="space-y-6" wire:poll.{{ $refreshInterval }}ms="loadMetrics">
    <!-- Header with Last Updated -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Real-time Metrics</h2>
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Last updated: {{ $lastUpdated }}</span>
            <button wire:click="refresh" 
                    class="ml-2 p-1 text-gray-400 hover:text-gray-600 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_users'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Active Orders</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['active_orders'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ ($metrics['low_stock_items'] ?? 0) > 0 ? 'bg-red-500' : 'bg-yellow-500' }} rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 13.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Low Stock Items</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['low_stock_items'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">System Status</h3>
                    <p class="text-sm font-semibold text-green-600">{{ $metrics['system_status']['status'] ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $metrics['system_status']['uptime'] ?? 'N/A' }} uptime</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
        </div>
        <div class="p-6">
            @if(!empty($metrics['recent_activities']))
                <div class="space-y-4">
                    @foreach($metrics['recent_activities'] as $activity)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if($activity['type'] === 'order')
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No recent activities</p>
            @endif
        </div>
    </div>

    <!-- Inventory Alerts -->
    @if(!empty($inventoryData['alerts']))
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Inventory Alerts</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($inventoryData['alerts'] as $alert)
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
        </div>
    @endif
</div>
