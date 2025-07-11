<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Order Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage and track all orders in the system</p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="showCreateOrderModal"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Order
                    </button>
                    <button wire:click="resetFilters"
                            class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-lg font-medium text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filters & Search</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="statusFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <select wire:model.live="userFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <option value="">All Companies</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->company_name ?? $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" wire:model.live="dateFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                </div>

                <!-- Sort Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select wire:model.live="sortBy" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <option value="created_at">Date Created</option>
                        <option value="price">Order Value</option>
                        <option value="status">Status</option>
                        <option value="id">Order ID</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search orders..."
                               class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Grid/Table Section -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <!-- Mobile View (Cards) -->
        <div class="block md:hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-900">{{ $orders->total() }} Orders</span>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }}
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($orders as $order)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Order #{{ $order->id }}</h4>
                                <p class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($order->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Buyer:</span>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->getBuyerCompanyDisplay() }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->buyer->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Seller:</span>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->getSellerCompanyDisplay() }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->seller->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Value:</span>
                                <span class="text-lg font-bold text-gray-900">${{ number_format($order->price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Items:</span>
                                <span class="text-sm text-gray-900">{{ $order->items_count }} items</span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button wire:click="viewOrder({{ $order->id }})"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                View
                            </button>
                            @if($order->status === 'pending')
                                <button wire:click="showEditOrderModal({{ $order->id }})"
                                        class="inline-flex items-center px-3 py-1 border border-blue-300 rounded-md text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    Edit
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or create a new order.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Desktop View (Table) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('id')" class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200">
                                <span>Order</span>
                                @if($sortBy === 'id')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Companies</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('price')" class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200">
                                <span>Value</span>
                                @if($sortBy === 'price')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('status')" class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200">
                                <span>Status</span>
                                @if($sortBy === 'status')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700 transition-colors duration-200">
                                <span>Date</span>
                                @if($sortBy === 'created_at')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Order #{{ $order->id }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->items_count }} items</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $order->getBuyerCompanyDisplay() }}</div>
                                            <div class="text-xs text-gray-500">Buyer: {{ $order->buyer->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $order->getSellerCompanyDisplay() }}</div>
                                            <div class="text-xs text-gray-500">Seller: {{ $order->seller->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-gray-900">${{ number_format($order->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($order->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewOrder({{ $order->id }})"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>
                                    @if($order->status === 'pending')
                                        <button wire:click="showEditOrderModal({{ $order->id }})"
                                                class="inline-flex items-center px-3 py-1 border border-blue-300 rounded-md text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button wire:click="deleteOrder({{ $order->id }})"
                                                onclick="return confirm('Are you sure you want to delete this order?')"
                                                class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or create a new order.</p>
                                <div class="mt-6">
                                    <button wire:click="showCreateOrderModal"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Create Your First Order
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700">Loading...</span>
            </div>
        </div>
    </div>
</div>
