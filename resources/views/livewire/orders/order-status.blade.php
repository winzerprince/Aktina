<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Order Status Tracking</h2>
            <button wire:click="resetFilters" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                Reset Filters
            </button>
        </div>

        <!-- Status Overview Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            @foreach($statusStats as $status => $count)
                @php
                    $colors = [
                        'pending' => 'bg-yellow-500',
                        'accepted' => 'bg-blue-500',
                        'in_fulfillment' => 'bg-indigo-500',
                        'shipped' => 'bg-purple-500',
                        'completed' => 'bg-green-500',
                        'rejected' => 'bg-red-500',
                    ];
                @endphp
                <div class="bg-white rounded-lg p-4 shadow border-l-4 {{ $colors[$status] ?? 'border-gray-500' }}">
                    <div class="text-sm font-medium text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model="statusFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Statuses</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select wire:model="dateRangeFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <select wire:model="userFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Users</option>
                    <!-- User options would be populated here -->
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" wire:model.debounce.300ms="search" placeholder="Search orders..." 
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timeline</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Order #{{ $order->id }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <div><strong>Buyer:</strong> {{ $order->buyer->name ?? 'N/A' }}</div>
                                    <div><strong>Seller:</strong> {{ $order->seller->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $color = $this->getStatusColor($order->status); @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="space-y-1">
                                    @if($order->approved_at)
                                        <div class="text-green-600">✓ Approved {{ $order->approved_at->format('M d') }}</div>
                                    @endif
                                    @if($order->fulfillment_started_at)
                                        <div class="text-blue-600">📦 In Fulfillment {{ $order->fulfillment_started_at->format('M d') }}</div>
                                    @endif
                                    @if($order->shipped_at)
                                        <div class="text-purple-600">🚚 Shipped {{ $order->shipped_at->format('M d') }}</div>
                                    @endif
                                    @if($order->completed_at)
                                        <div class="text-green-600">✅ Completed {{ $order->completed_at->format('M d') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${{ number_format($order->price, 2) }}</div>
                                <div class="text-sm text-gray-500">{{ count($order->items ?? []) }} items</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <button wire:click="viewOrder({{ $order->id }})" 
                                            class="text-blue-600 hover:text-blue-900">View</button>
                                    <button wire:click="trackOrder({{ $order->id }})" 
                                            class="text-green-600 hover:text-green-900">Track</button>
                                    @if(auth()->user()->hasRole(['admin', 'production_manager']) && in_array($order->status, ['pending', 'accepted', 'in_fulfillment']))
                                        <button wire:click="showUpdateStatusModal({{ $order->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900">Update</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Order Details Modal -->
    @if($selectedOrder && !$showUpdateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeOrderModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Order #{{ $selectedOrder->id }} Tracking</h3>
                            <button wire:click="closeOrderModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Status Timeline -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold mb-3">Order Timeline</h4>
                            <div class="flex items-center space-x-4">
                                @php
                                    $statuses = [
                                        ['key' => 'pending', 'label' => 'Ordered', 'date' => $selectedOrder->created_at],
                                        ['key' => 'accepted', 'label' => 'Approved', 'date' => $selectedOrder->approved_at],
                                        ['key' => 'in_fulfillment', 'label' => 'In Fulfillment', 'date' => $selectedOrder->fulfillment_started_at],
                                        ['key' => 'shipped', 'label' => 'Shipped', 'date' => $selectedOrder->shipped_at],
                                        ['key' => 'completed', 'label' => 'Completed', 'date' => $selectedOrder->completed_at],
                                    ];
                                    $currentStatusIndex = array_search($selectedOrder->status, array_column($statuses, 'key'));
                                @endphp
                                
                                @foreach($statuses as $index => $status)
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                                    {{ $index <= $currentStatusIndex ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="text-xs mt-1 text-center">
                                            <div class="font-medium">{{ $status['label'] }}</div>
                                            @if($status['date'])
                                                <div class="text-gray-500">{{ $status['date']->format('M d') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($index < count($statuses) - 1)
                                        <div class="flex-1 h-0.5 {{ $index < $currentStatusIndex ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-lg font-semibold mb-3">Order Information</h4>
                                <div class="space-y-2">
                                    <p><strong>Buyer:</strong> {{ $selectedOrder->buyer->name ?? 'N/A' }}</p>
                                    <p><strong>Seller:</strong> {{ $selectedOrder->seller->name ?? 'N/A' }}</p>
                                    <p><strong>Current Status:</strong> 
                                        @php $color = $this->getStatusColor($selectedOrder->status); @endphp
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                            {{ ucfirst(str_replace('_', ' ', $selectedOrder->status)) }}
                                        </span>
                                    </p>
                                    <p><strong>Total Value:</strong> ${{ number_format($selectedOrder->price, 2) }}</p>
                                    @if($selectedOrder->expected_delivery_date)
                                        <p><strong>Expected Delivery:</strong> {{ $selectedOrder->expected_delivery_date->format('M d, Y') }}</p>
                                    @endif
                                    @if($selectedOrder->assignedWarehouse)
                                        <p><strong>Warehouse:</strong> {{ $selectedOrder->assignedWarehouse->name }}</p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h4 class="text-lg font-semibold mb-3">Order Items</h4>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    @foreach($selectedOrder->items ?? [] as $item)
                                        <div class="border rounded p-2">
                                            <p class="font-medium">{{ $item['resource_name'] ?? 'Item' }}</p>
                                            <p class="text-sm text-gray-600">
                                                Qty: {{ $item['quantity'] }} × ${{ number_format($item['unit_price'] ?? 0, 2) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($selectedOrder->notes)
                            <div class="mt-4">
                                <h4 class="text-lg font-semibold mb-2">Notes</h4>
                                <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $selectedOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Update Status Modal -->
    @if($showUpdateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeUpdateModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="updateOrderStatus">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Update Order Status</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                                    <select wire:model="newStatus" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('newStatus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Notes (Optional)</label>
                                    <textarea wire:model="statusNotes" rows="3" 
                                             class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                             placeholder="Add any notes about this status update..."></textarea>
                                    @error('statusNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Update Status
                            </button>
                            <button type="button" wire:click="closeUpdateModal" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50" role="alert">
            {{ session('error') }}
        </div>
    @endif
</div>
