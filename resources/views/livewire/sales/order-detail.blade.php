<div class="p-6 bg-white rounded-lg shadow-md">
    @if ($order)
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Order #{{ $order->id }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders') }}"
                   class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Orders
                </a>

                @if ($order->status === 'pending')
                    <button wire:click="acceptOrder" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors duration-200">
                        Accept Order
                    </button>
                @endif

                @if ($order->status === 'accepted')
                    <button wire:click="completeOrder" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 transition-colors duration-200">
                        Complete Order
                    </button>
                @endif
            </div>
        </div>

        <!-- Order Status -->
        <div class="mb-6">
            @if ($order->status === 'pending')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    Pending
                </span>
            @elseif ($order->status === 'accepted')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    In Progress
                </span>
            @elseif ($order->status === 'complete')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Completed
                </span>
            @endif
        </div>

        <!-- Order Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div>
                    <h3 class="font-semibold text-lg mb-2">Order Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Date:</span> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                        <p><span class="font-medium">Total:</span> ${{ number_format($order->price, 2) }}</p>
                        <p><span class="font-medium">Status:</span> {{ ucfirst($order->status) }}</p>
                        <p><span class="font-medium">Items Count:</span> {{ $order->total_items }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div>
                    <h3 class="font-semibold text-lg mb-2">Customer Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Name:</span> {{ $order->buyer->name ?? 'N/A' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->buyer->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->buyer->phone ?? 'N/A' }}</p>
                        <p><span class="font-medium">Type:</span> {{ $order->buyer->role ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-4">Order Items</h3>
            <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->getItemsAsArray() as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $product = \App\Models\Product::find($item['product_id'] ?? 0);
                                    @endphp
                                    {{ $product ? $product->name : 'Unknown Product' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item['quantity'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item['price'] ?? 0, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format(($item['quantity'] * ($item['price'] ?? 0)), 2) }}</td>
                            </tr>
                        @endforeach

                        <!-- Total row -->
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-sm text-gray-900">Total:</td>
                            <td class="px-6 py-4 font-bold text-sm text-gray-900">${{ number_format($order->price, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Employees Section -->
        @if ($order->status === 'accepted' || $order->status === 'complete')
            <div class="mb-6">
                <h3 class="font-semibold text-lg mb-4">Assigned Employees</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($assignedEmployees as $employee)
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium">{{ $employee['name'] }}</h4>
                                    <p class="text-sm text-gray-500">{{ $employee['position'] }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Employees Assigned</h3>
                                <p class="text-gray-500">There are currently no employees assigned to this order.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Employee Assignment -->
        @if ($order->status === 'pending')
            <div class="mb-6">
                <h3 class="font-semibold text-lg mb-4">Assign Employees</h3>

                @if (count($availableEmployees) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        @foreach ($availableEmployees as $index => $employee)
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="employee-{{ $employee['id'] }}"
                                        type="checkbox"
                                        wire:model.live="selectedEmployees"
                                        value="{{ $employee['id'] }}"
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="employee-{{ $employee['id'] }}" class="font-medium text-gray-700 cursor-pointer">
                                        {{ $employee['name'] }} - {{ $employee['position'] }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">No Employees Available</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>There are no available employees to assign to this order.</p>
                                    <p class="text-sm">All employees are currently occupied with other tasks.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Order Timeline -->
        <div>
            <h3 class="font-semibold text-lg mb-4">Order Timeline</h3>
            <ol class="relative border-l border-gray-200 ml-4">
                <li class="mb-10 ml-6">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -left-4 ring-4 ring-white">
                        <svg class="w-4 h-4 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0H17M9 9h8"/>
                        </svg>
                    </span>
                    <h4 class="font-semibold text-gray-900">Order Created</h4>
                    <time class="block text-sm font-normal leading-none text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</time>
                    <p class="text-base font-normal text-gray-500">Order was placed by {{ $order->buyer->name ?? 'Unknown' }}</p>
                </li>

                @if ($order->status !== 'pending')
                    <li class="mb-10 ml-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full -left-4 ring-4 ring-white">
                            <svg class="w-4 h-4 text-indigo-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <h4 class="font-semibold text-gray-900">Order Accepted</h4>
                        <time class="block text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</time>
                        <p class="text-base font-normal text-gray-500">Order was accepted and processing started</p>
                    </li>
                @endif

                @if ($order->status === 'complete')
                    <li class="ml-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 bg-green-100 rounded-full -left-4 ring-4 ring-white">
                            <svg class="w-4 h-4 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <h4 class="font-semibold text-gray-900">Order Completed</h4>
                        <time class="block text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</time>
                        <p class="text-base font-normal text-gray-500">Order has been successfully completed</p>
                    </li>
                @endif
            </ol>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Order not found</h3>
            <p class="mt-1 text-gray-500">The requested order does not exist.</p>
            <div class="mt-6">
                <a href="{{ route('orders.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    Back to Orders
                </a>
            </div>
        </div>
    @endif
</div>
