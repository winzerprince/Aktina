<div class="relative" wire:poll.30s="loadNotifications">
    <!-- Notification Bell Button -->
    <button wire:click="toggleDropdown" 
            class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-5 5v-5zM11 7a4 4 0 118 0v1l-4 4H7l-4-4V7z"/>
        </svg>
        
        <!-- Notification Count Badge -->
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    @if($showDropdown)
        <div class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" 
                            class="text-sm text-blue-600 hover:text-blue-800">
                        Mark all as read
                    </button>
                @endif
            </div>

            <!-- Notifications List -->
            <div class="max-h-96 overflow-y-auto">
                @forelse($notifications as $index => $notification)
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                         wire:click="markAsRead({{ $index }})">
                        <div class="flex items-start space-x-3">
                            <!-- Notification Icon -->
                            <div class="flex-shrink-0">
                                @if($notification['type'] === 'low_stock')
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 13.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                @elseif($notification['type'] === 'order_status')
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Notification Content -->
                            <div class="flex-1 min-w-0">
                                <div class="text-sm text-gray-900">
                                    @if($notification['type'] === 'low_stock')
                                        <span class="font-medium">Low Stock Alert:</span> 
                                        {{ $notification['item'] }} is running low ({{ $notification['current_stock'] }} remaining)
                                    @else
                                        {{ $notification['message'] }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Severity Indicator -->
                            @if(isset($notification['severity']))
                                <div class="flex-shrink-0">
                                    @if($notification['severity'] === 'critical')
                                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                    @elseif($notification['severity'] === 'warning')
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                    @else
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 7a4 4 0 118 0v1l-4 4H7l-4-4V7z"/>
                        </svg>
                        <p>No notifications</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer -->
            <div class="px-4 py-3 border-t border-gray-200">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    View all notifications
                </a>
            </div>
        </div>
    @endif

    <!-- Click outside to close dropdown -->
    @if($showDropdown)
        <div class="fixed inset-0 z-40" wire:click="toggleDropdown"></div>
    @endif
</div>
