<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Order Management</h2>
            <div class="flex gap-2">
                <button wire:click="showCreateOrderModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Order
                </button>
                <button wire:click="resetFilters" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model="statusFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="in_fulfillment">In Fulfillment</option>
                    <option value="shipped">Shipped</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <select wire:model="userFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" wire:model="dateFilter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select wire:model="sortBy" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="created_at">Date Created</option>
                    <option value="price">Value</option>
                    <option value="status">Status</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" wire:model.debounce.300ms="search" placeholder="Search orders..." 
                       class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('id')" class="flex items-center">
                                Order ID
                                @if($sortBy === 'id')
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('price')" class="flex items-center">
                                Value
                                @if($sortBy === 'price')
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('status')" class="flex items-center">
                                Status
                                @if($sortBy === 'status')
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('created_at')" class="flex items-center">
                                Date
                                @if($sortBy === 'created_at')
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Order #{{ $order->id }}</div>
                                <div class="text-sm text-gray-500">{{ count($order->items ?? []) }} items</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <div><strong>Buyer:</strong> {{ $order->buyer->name ?? 'N/A' }}</div>
                                    <div><strong>Seller:</strong> {{ $order->seller->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${{ number_format($order->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-blue-100 text-blue-800',
                                        'in_fulfillment' => 'bg-indigo-100 text-indigo-800',
                                        'shipped' => 'bg-purple-100 text-purple-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <button wire:click="viewOrder({{ $order->id }})" 
                                            class="text-blue-600 hover:text-blue-900">View</button>
                                    @if($order->status === 'pending')
                                        <button wire:click="showEditOrderModal({{ $order->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="deleteOrder({{ $order->id }})" 
                                                onclick="return confirm('Are you sure you want to delete this order?')"
                                                class="text-red-600 hover:text-red-900">Delete</button>
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
    @if($selectedOrder && !$showCreateModal && !$showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeOrderModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Order #{{ $selectedOrder->id }} Details</h3>
                            <button wire:click="closeOrderModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-lg font-semibold mb-3">Order Information</h4>
                                <div class="space-y-2">
                                    <p><strong>Buyer:</strong> {{ $selectedOrder->buyer->name ?? 'N/A' }}</p>
                                    <p><strong>Seller:</strong> {{ $selectedOrder->seller->name ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$selectedOrder->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $selectedOrder->status)) }}
                                        </span>
                                    </p>
                                    <p><strong>Total Value:</strong> ${{ number_format($selectedOrder->price, 2) }}</p>
                                    <p><strong>Created:</strong> {{ $selectedOrder->created_at->format('M d, Y H:i') }}</p>
                                    @if($selectedOrder->expected_delivery_date)
                                        <p><strong>Expected Delivery:</strong> {{ $selectedOrder->expected_delivery_date->format('M d, Y') }}</p>
                                    @endif
                                    @if($selectedOrder->delivery_address)
                                        <p><strong>Delivery Address:</strong> {{ $selectedOrder->delivery_address }}</p>
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
                                                Qty: {{ $item['quantity'] }} × ${{ number_format($item['unit_price'] ?? 0, 2) }} = 
                                                ${{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 2) }}
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

    <!-- Create/Edit Order Modal -->
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form wire:submit.prevent="{{ $showCreateModal ? 'createOrder' : 'updateOrder' }}">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $showCreateModal ? 'Create New Order' : 'Edit Order' }}
                                </h3>
                                <button type="button" wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" 
                                        class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Order Information -->
                                <div>
                                    <h4 class="text-lg font-semibold mb-3">Order Information</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Buyer</label>
                                            <select wire:model="buyer_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select Buyer</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                            @error('buyer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Seller</label>
                                            <select wire:model="seller_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select Seller</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                            @error('seller_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                                            <input type="date" wire:model="expected_delivery_date" 
                                                   class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                            @error('expected_delivery_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                                            <textarea wire:model="delivery_address" rows="3" 
                                                     class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                                     placeholder="Enter delivery address..."></textarea>
                                            @error('delivery_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                            <textarea wire:model="notes" rows="3" 
                                                     class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                                     placeholder="Add any notes about this order..."></textarea>
                                            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div>
                                    <h4 class="text-lg font-semibold mb-3">Order Items</h4>
                                    
                                    <!-- Add Item Form -->
                                    <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                                        <h5 class="font-medium mb-3">Add Item</h5>
                                        <div class="space-y-3">
                                            <div>
                                                <select wire:model="newItem.resource_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">Select Resource</option>
                                                    @foreach($resources as $resource)
                                                        <option value="{{ $resource->id }}">{{ $resource->name }} - ${{ number_format($resource->unit_price, 2) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <input type="number" wire:model="newItem.quantity" min="1" placeholder="Quantity" 
                                                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <input type="number" wire:model="newItem.unit_price" min="0" step="0.01" placeholder="Unit Price" 
                                                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>

                                            <button type="button" wire:click="addItem" 
                                                    class="w-full px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                Add Item
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Items List -->
                                    <div class="space-y-2 max-h-60 overflow-y-auto">
                                        @foreach($items as $index => $item)
                                            <div class="border rounded p-3 bg-white">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <p class="font-medium">{{ $item['resource_name'] ?? 'Item' }}</p>
                                                        <p class="text-sm text-gray-600">
                                                            Qty: {{ $item['quantity'] }} × ${{ number_format($item['unit_price'] ?? 0, 2) }} = 
                                                            ${{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 2) }}
                                                        </p>
                                                    </div>
                                                    <button type="button" wire:click="removeItem({{ $index }})" 
                                                            class="text-red-600 hover:text-red-900">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach

                                        @if(empty($items))
                                            <p class="text-gray-500 text-center py-4">No items added yet</p>
                                        @endif
                                    </div>

                                    @if(!empty($items))
                                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                            <p class="font-semibold text-blue-900">Total Order Value: ${{ number_format($totalValue, 2) }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $showCreateModal ? 'Create Order' : 'Update Order' }}
                            </button>
                            <button type="button" wire:click="{{ $showCreateModal ? 'closeCreateModal' : 'closeEditModal' }}" 
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
