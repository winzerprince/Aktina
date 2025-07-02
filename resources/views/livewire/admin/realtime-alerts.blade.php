<div class="relative">
    <!-- Notifications Bell Icon -->
    <button wire:click="toggleNotifications" 
            class="relative p-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-3.405-3.405A6.969 6.969 0 0118 9a6.969 6.969 0 00-1.405-4.195L15 7M9 17v1a3 3 0 006 0v-1m-6 0h6m-6 0V9a7.003 7.003 0 011.6-4.5M15 7a7.003 7.003 0 00-1.6-4.5"/>
        </svg>
        
        @if($unreadNotifications > 0)
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
            </span>
        @endif
    </button>

    <!-- Notifications Dropdown -->
    @if($showNotifications)
        <div class="absolute right-0 top-12 z-50 w-96 max-h-96 overflow-y-auto bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Alerts & Notifications
                </h3>
                <div class="flex items-center space-x-2">
                    <button wire:click="toggleAutoRefresh" 
                            class="text-sm px-2 py-1 rounded {{ $autoRefresh ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        Auto: {{ $autoRefresh ? 'ON' : 'OFF' }}
                    </button>
                    <span class="text-xs text-gray-500">{{ $lastRefresh }}</span>
                </div>
            </div>

            <!-- Critical Alerts -->
            @if(count($criticalAlerts) > 0)
                <div class="p-3 bg-red-50 dark:bg-red-900/20">
                    <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Critical Alerts</h4>
                    @foreach($criticalAlerts as $alert)
                        <div class="flex items-start space-x-3 p-2 bg-white dark:bg-gray-800 rounded border-l-4 border-red-500 mb-2">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alert['title'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $alert['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $alert['timestamp'] }}</p>
                            </div>
                            <div class="flex flex-col space-y-1">
                                <button wire:click="resolveAlert('{{ $alert['id'] }}', '{{ $alert['type'] }}')" 
                                        class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200">
                                    Resolve
                                </button>
                                <button wire:click="dismissAlert('{{ $alert['id'] }}', '{{ $alert['type'] }}')" 
                                        class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- System Alerts -->
            @if(count($systemAlerts) > 0)
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20">
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">System Alerts</h4>
                    @foreach($systemAlerts as $alert)
                        <div class="flex items-start space-x-3 p-2 bg-white dark:bg-gray-800 rounded border-l-4 border-yellow-500 mb-2">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alert['title'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $alert['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $alert['timestamp'] }}</p>
                            </div>
                            <div class="flex flex-col space-y-1">
                                <button wire:click="markAsRead('{{ $alert['id'] }}', '{{ $alert['type'] }}')" 
                                        class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">
                                    Mark Read
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Inventory Alerts -->
            @if(count($inventoryAlerts) > 0)
                <div class="p-3">
                    <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Inventory Alerts</h4>
                    @foreach($inventoryAlerts as $alert)
                        <div class="flex items-start space-x-3 p-2 bg-gray-50 dark:bg-gray-700 rounded border-l-4 border-orange-500 mb-2">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alert['title'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $alert['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $alert['timestamp'] }}</p>
                            </div>
                            <div class="flex flex-col space-y-1">
                                <button wire:click="resolveAlert('{{ $alert['id'] }}', '{{ $alert['type'] }}')" 
                                        class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200">
                                    Resolve
                                </button>
                                <button wire:click="dismissAlert('{{ $alert['id'] }}', '{{ $alert['type'] }}')" 
                                        class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- No Alerts -->
            @if(count($criticalAlerts) === 0 && count($systemAlerts) === 0 && count($inventoryAlerts) === 0)
                <div class="p-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300">All systems operational</p>
                    <p class="text-xs text-gray-500 mt-1">No active alerts</p>
                </div>
            @endif

            <!-- Footer -->
            <div class="p-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <button wire:click="loadAlerts" 
                        class="w-full text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Refresh Alerts
                </button>
            </div>
        </div>
    @endif

    <!-- Auto-refresh wire:poll -->
    @if($autoRefresh)
        <div wire:poll.10s="loadAlerts"></div>
    @endif
</div>

<script>
    // Close notifications when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[wire\\:click="toggleNotifications"]') && 
            !event.target.closest('.absolute.right-0')) {
            @this.showNotifications = false;
        }
    });
</script>
