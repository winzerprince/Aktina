@props([
    'show' => false,
    'order' => null,
    'role' => 'retailer', // retailer, vendor, production_manager, admin
    'allowActions' => true
])

@if($show && $order)
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-7xl w-full mx-4 my-6 max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Order #{{ $order->id }}</h3>
                        <p class="text-sm text-gray-600">Created {{ $order->created_at->format('M j, Y \a\t H:i') }}</p>
                    </div>
                </div>
                <button wire:click="closeOrderDetails" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session()->has('message'))
            <div class="mx-6 mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mx-6 mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Content -->
        <div class="flex flex-col lg:flex-row h-full">
            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-6 space-y-6">
                    <!-- Order Status Timeline -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Order Timeline
                        </h4>

                        <div class="relative">
                            <!-- Timeline Line -->
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-300"></div>

                            @php
                                $statuses = collect([
                                    ['status' => 'pending', 'label' => 'Order Placed', 'icon' => 'shopping-cart'],
                                    ['status' => 'confirmed', 'label' => 'Order Confirmed', 'icon' => 'check-circle'],
                                    ['status' => 'processing', 'label' => 'Processing', 'icon' => 'cog'],
                                    ['status' => 'shipped', 'label' => 'Shipped', 'icon' => 'truck'],
                                    ['status' => 'delivered', 'label' => 'Delivered', 'icon' => 'check']
                                ]);

                                $currentStatusIndex = $statuses->search(function($item) use ($order) {
                                    return $item['status'] === $order->status;
                                });
                            @endphp

                            @foreach($statuses as $index => $statusItem)
                                @php
                                    $isActive = $index <= $currentStatusIndex;
                                    $isCurrent = $statusItem['status'] === $order->status;
                                @endphp

                                <div class="relative flex items-center mb-6 last:mb-0">
                                    <!-- Status Icon -->
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $isActive ? 'bg-indigo-600 text-white' : 'bg-gray-300 text-gray-500' }} border-4 border-white shadow-sm z-10">
                                        @if($statusItem['icon'] === 'shopping-cart')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                                            </svg>
                                        @elseif($statusItem['icon'] === 'check-circle')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($statusItem['icon'] === 'cog')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        @elseif($statusItem['icon'] === 'truck')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V10a1 1 0 00-1-1H3V4zM14 7a1 1 0 00-1 1v8.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-5a1 1 0 00-.293-.707L17 6.586A1 1 0 0016.414 6H14V7z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Status Content -->
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h5 class="text-sm font-medium {{ $isActive ? 'text-gray-900' : 'text-gray-500' }}">
                                                {{ $statusItem['label'] }}
                                            </h5>
                                            @if($isCurrent)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    Current
                                                </span>
                                            @endif
                                        </div>
                                        @if($isActive && $isCurrent)
                                            <p class="text-xs text-gray-500 mt-1">Updated {{ $order->updated_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Information Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Order Details -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Order Details
                            </h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                                    <dd class="text-sm font-semibold text-gray-900">#{{ $order->id }}</dd>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->created_at->format('M j, Y \a\t H:i') }}</dd>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                    <dd class="text-lg font-bold text-green-600">${{ number_format($order->total_amount, 2) }}</dd>
                                </div>
                                @if($order->metadata && isset($order->metadata['priority']))
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $order->metadata['priority'] === 'high' ? 'bg-red-100 text-red-800' :
                                               ($order->metadata['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($order->metadata['priority'] ?? 'normal') }}
                                        </span>
                                    </dd>
                                </div>
                                @endif
                                @if($order->metadata && isset($order->metadata['production_schedule']))
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Scheduled Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->metadata['production_schedule']['scheduled_date'] ?? 'Not scheduled' }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Customer/Buyer Information -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Customer Information
                            </h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $order->buyer->name }}</dd>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->buyer->email }}</dd>
                                </div>
                                @if($order->buyer->phone)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->buyer->phone }}</dd>
                                </div>
                                @endif
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst(str_replace('_', ' ', $order->buyer->role)) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Order Items ({{ $order->orderItems->count() }} items)
                        </h4>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-medium text-gray-900">Product</th>
                                        <th class="text-center py-3 px-4 font-medium text-gray-900">Quantity</th>
                                        <th class="text-right py-3 px-4 font-medium text-gray-900">Unit Price</th>
                                        <th class="text-right py-3 px-4 font-medium text-gray-900">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($order->orderItems as $item)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                                                        @if($item->product->sku)
                                                            <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-right text-sm font-medium text-gray-900">
                                                ${{ number_format($item->price, 2) }}
                                            </td>
                                            <td class="py-4 px-4 text-right text-sm font-bold text-gray-900">
                                                ${{ number_format($item->quantity * $item->price, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-200 bg-gray-50">
                                        <td colspan="3" class="py-4 px-4 text-right text-lg font-bold text-gray-900">Total:</td>
                                        <td class="py-4 px-4 text-right text-xl font-bold text-green-600">
                                            ${{ number_format($order->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @if($order->metadata && isset($order->metadata['notes']))
                    <!-- Notes Section -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1.586l-4.414 4.414z" />
                            </svg>
                            Notes
                        </h4>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700">{{ $order->metadata['notes'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Sidebar (Role-specific) -->
            @if($allowActions)
            <div class="w-full lg:w-80 border-t lg:border-t-0 lg:border-l border-gray-200 bg-gray-50 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>

                @if($role === 'retailer')
                    <!-- Retailer Actions -->
                    <div class="space-y-3">
                        @if($order->status === 'pending')
                            <button wire:click="cancelOrder({{ $order->id }})"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Cancel Order
                            </button>
                        @endif
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Contact Vendor
                        </button>
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Track Order
                        </button>
                    </div>
                @elseif($role === 'vendor')
                    <!-- Vendor Actions -->
                    <div class="space-y-3">
                        @if(in_array($order->status, ['pending', 'confirmed']))
                            <button wire:click="showOrderFulfillment({{ $order->id }})"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Process Order
                            </button>
                        @endif
                        @if($order->status === 'pending')
                            <button wire:click="confirmOrder({{ $order->id }})"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Confirm Order
                            </button>
                            <button wire:click="rejectOrder({{ $order->id }})"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Reject Order
                            </button>
                        @endif
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Contact Customer
                        </button>
                    </div>
                @elseif($role === 'production_manager')
                    <!-- Production Manager Actions -->
                    <div class="space-y-3">
                        @if($order->status === 'confirmed')
                            <button wire:click="startProduction({{ $order->id }})"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Start Production
                            </button>
                        @endif
                        @if(in_array($order->status, ['processing', 'confirmed']))
                            <button wire:click="scheduleProduction({{ $order->id }})"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Schedule Production
                            </button>
                        @endif
                        <button wire:click="assignEmployee({{ $order->id }})"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Assign Employee
                        </button>
                        <button wire:click="checkResources({{ $order->id }})"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Check Resources
                        </button>
                    </div>
                @endif

                <!-- Common Actions -->
                <div class="border-t border-gray-300 mt-6 pt-6 space-y-3">
                    <button class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Export PDF
                    </button>
                    <button class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Print Order
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
