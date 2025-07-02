<div class="space-y-6" wire:poll.{{ $refreshInterval }}ms>
    <!-- Header with Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Live Activity Feed</h2>
                <p class="text-gray-600">Real-time system activity and user interactions</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Refresh Rate Selector -->
                <select wire:model.live="refreshInterval" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="1000">1 second</option>
                    <option value="2000">2 seconds</option>
                    <option value="5000">5 seconds</option>
                    <option value="10000">10 seconds</option>
                </select>
                
                <!-- Auto Scroll Toggle -->
                <button 
                    wire:click="toggleAutoScroll"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium {{ $autoScroll ? 'text-white bg-blue-600 hover:bg-blue-700' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path>
                    </svg>
                    Auto Scroll
                </button>
                
                <!-- Filters Toggle -->
                <button 
                    wire:click="toggleFilters"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    Filters
                </button>
                
                <!-- Export Button -->
                <button wire:click="exportActivities('csv')" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
        
        <!-- Filters -->
        @if($showFilters)
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex flex-wrap gap-2">
                    @foreach(['all', 'users', 'orders', 'system', 'products'] as $type)
                        <button 
                            wire:click="toggleActivityType('{{ $type }}')"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ in_array($type, $activityTypes) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}"
                        >
                            {{ ucfirst($type) }}
                            @if(in_array($type, $activityTypes))
                                <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Activity Stats -->
    @if($this->activityStats)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Today</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($this->activityStats['total_today']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($this->activityStats['active_users']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Peak Hour</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->activityStats['peak_hour'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Orders</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($this->activityStats['activity_breakdown']['orders']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Activity Feed -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                <p class="text-sm text-gray-600">Live updates from across the system</p>
            </div>
            
            <div class="max-h-96 overflow-y-auto" id="activity-feed-container">
                @if($this->recentActivities)
                    <div class="divide-y divide-gray-200">
                        @foreach($this->recentActivities as $activity)
                            <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-start space-x-3">
                                    <!-- Activity Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center">
                                            @if($activity['icon'] === 'user-plus')
                                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                                </svg>
                                            @elseif($activity['icon'] === 'login')
                                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                </svg>
                                            @elseif($activity['icon'] === 'shopping-cart')
                                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6.28"></path>
                                                </svg>
                                            @elseif($activity['icon'] === 'refresh')
                                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            @elseif($activity['icon'] === 'server')
                                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                                </svg>
                                            @elseif($activity['icon'] === 'package')
                                                <svg class="w-4 h-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Activity Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}</p>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $activity['description'] }}</p>
                                        
                                        @if($activity['user'])
                                            <div class="flex items-center mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst($activity['user']['role']) }}
                                                </span>
                                                <span class="text-xs text-gray-500 ml-2">{{ $activity['user']['name'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p>No recent activity to display</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Active Sessions & System Events -->
        <div class="space-y-6">
            <!-- Active Sessions -->
            @if($this->userSessions)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Active Sessions</h3>
                        <p class="text-sm text-gray-600">{{ count($this->userSessions) }} users online</p>
                    </div>
                    
                    <div class="max-h-64 overflow-y-auto">
                        <div class="divide-y divide-gray-200">
                            @foreach(array_slice($this->userSessions, 0, 10) as $session)
                                <div class="p-3 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-{{ $session['status'] === 'active' ? 'green' : 'yellow' }}-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-gray-900">{{ $session['user_name'] }}</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($session['user_role']) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $session['location'] }} • {{ $session['device'] }} • {{ \Carbon\Carbon::parse($session['last_activity'])->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- System Events -->
            @if($this->systemEvents)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">System Events</h3>
                        <p class="text-sm text-gray-600">Recent system activities</p>
                    </div>
                    
                    <div class="max-h-64 overflow-y-auto">
                        <div class="divide-y divide-gray-200">
                            @foreach(array_slice($this->systemEvents, 0, 8) as $event)
                                <div class="p-3 hover:bg-gray-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-2">
                                            <div class="w-2 h-2 bg-{{ $event['severity'] === 'info' ? 'blue' : ($event['severity'] === 'warning' ? 'yellow' : 'red') }}-500 rounded-full mt-1.5"></div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $event['type'])) }}</p>
                                                <p class="text-xs text-gray-600 mt-1">{{ $event['message'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($event['timestamp'])->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function () {
    initializeActivityFeed();
});

document.addEventListener('DOMContentLoaded', function () {
    initializeActivityFeed();
});

function initializeActivityFeed() {
    const container = document.getElementById('activity-feed-container');
    let autoScroll = @json($autoScroll);
    
    // Auto-scroll to bottom when new activities arrive
    if (autoScroll && container) {
        container.scrollTop = container.scrollHeight;
    }
}

// Livewire listeners
document.addEventListener('livewire:init', () => {
    Livewire.on('activity-filter-changed', () => {
        setTimeout(() => initializeActivityFeed(), 100);
    });
    
    Livewire.on('auto-scroll-toggled', (event) => {
        autoScroll = event.enabled;
    });
    
    Livewire.on('activities-cleared', () => {
        const container = document.getElementById('activity-feed-container');
        if (container) {
            container.innerHTML = '<div class="p-8 text-center text-gray-500">Activity feed cleared</div>';
        }
    });
    
    Livewire.on('download-activities', (event) => {
        const data = event.data;
        const format = event.format;
        const filename = event.filename;
        
        const content = JSON.stringify(data, null, 2);
        downloadFile(content, filename + '.' + format, 'application/json');
    });
});

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
