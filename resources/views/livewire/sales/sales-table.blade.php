<div class="space-y-6">
<div class="space-y-6">
    {{-- Header Section with Date Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Sales Overview</h3>
                <p class="text-sm text-gray-500">Track and manage your sales performance</p>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                {{-- Date Filter Controls --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input
                            type="date"
                            wire:model.live="startDate"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                    </div>

                    <div class="min-w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input
                            type="date"
                            wire:model.live="endDate"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                    </div>

                    <button
                        wire:click="$refresh"
                        class="self-end inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>

            {{-- Quick Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                            <p class="text-3xl font-bold">${{ number_format($sales->sum('price'), 2) }}</p>
                            <p class="text-green-100 text-xs">Total revenue from sales</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Orders</p>
                            <p class="text-3xl font-bold">{{ $sales->count() }}</p>
                            <p class="text-blue-100 text-xs">Number of orders</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Average Order Value</p>
                            <p class="text-3xl font-bold">${{ $sales->count() > 0 ? number_format($sales->avg('price'), 2) : '0.00' }}</p>
                            <p class="text-purple-100 text-xs">Average value per order</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Sales Records</h3>
                <p class="text-sm text-gray-500">Click on any row to view order details</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="w-20 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $order)
                            <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-200" wire:click="viewOrder({{ $order->id }})">
                                {{-- Order ID Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                {{-- Buyer Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-sm font-medium text-blue-600">
                                                {{ $order->buyer ? substr($order->buyer->name, 0, 2) : 'UK' }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $order->buyer->name ?? 'Unknown Buyer' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $order->buyer->email ?? 'N/A' }}
                                            </div>
                                            @if($order->buyer && $order->buyer->company_name)
                                                <div class="text-xs text-gray-400">
                                                    {{ $order->buyer->company_name }}
                                                    @if($order->buyer->role)
                                                        • {{ $order->buyer->role }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Price Column --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-green-600">
                                        ${{ number_format($order->price, 2) }}
                                    </div>
                                    @if($order->items && is_array($order->items))
                                        <div class="text-xs text-gray-500">
                                            {{ count($order->items) }} item(s) • {{ collect($order->items)->sum('quantity') }} units
                                        </div>
                                    @endif
                                </td>

                                {{-- Date Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $order->created_at->format('g:i A') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-lg font-medium">No sales found</p>
                                            <p class="text-gray-400 text-sm">Try adjusting your date range or check back later</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($sales->hasPages())
                <div class="mt-4">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Enhanced Modal --}}
    @if($showOrderModal && $selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{}" x-show="true" x-transition.opacity>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 backdrop-blur-sm" wire:click="closeModal"></div>

                <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Order Details</h3>
                                <p class="text-sm text-gray-500">Complete order information and purchased items</p>
                            </div>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6 space-y-8">
                        {{-- Order Header --}}
                        <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-green-900">
                                            Order #{{ str_pad($selectedOrder->id, 4, '0', STR_PAD_LEFT) }}
                                        </h3>
                                        <p class="text-green-700 text-lg">
                                            Placed on {{ $selectedOrder->created_at->format('F j, Y \a\t g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-4xl font-bold text-green-900 mb-2">
                                        ${{ number_format($selectedOrder->price, 2) }}
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Customer and Order Information Grid --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {{-- Customer Information --}}
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 rounded-lg p-6">
                                <h4 class="text-lg font-semibold text-blue-900 mb-4">Customer Information</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="h-16 w-16 rounded-full bg-blue-200 flex items-center justify-center text-xl font-medium text-blue-600">
                                            {{ $selectedOrder->buyer ? substr($selectedOrder->buyer->name, 0, 2) : 'UK' }}
                                        </div>
                                        <div>
                                            <div class="text-lg font-bold text-blue-900">
                                                {{ $selectedOrder->buyer->name ?? 'Unknown Buyer' }}
                                            </div>
                                            <div class="text-blue-700">
                                                {{ $selectedOrder->buyer->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($selectedOrder->buyer && $selectedOrder->buyer->company_name)
                                        <hr class="border-blue-200">
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-blue-700 font-medium">Company:</span>
                                                <span class="text-blue-900">{{ $selectedOrder->buyer->company_name }}</span>
                                            </div>
                                            @if($selectedOrder->buyer->role)
                                                <div class="flex justify-between">
                                                    <span class="text-blue-700 font-medium">Role:</span>
                                                    <span class="text-blue-900">{{ $selectedOrder->buyer->role }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Company</p>
                                        <p class="text-blue-900 dark:text-blue-100 font-medium">{{ $selectedOrder->buyer->company_name }}</p>
                                    </div>
                                    @if($selectedOrder->buyer->role)
                                        <div>
                                            <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Role</p>
                                            <p class="text-blue-900 dark:text-blue-100">{{ $selectedOrder->buyer->role }}</p>
                                        </div>
                                    @endif
                                </div>
                        </div>
                    </x-card>

                    {{-- Order Summary --}}
                    <div class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 border border-purple-200 dark:border-purple-700 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Order ID</div>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">#{{ str_pad($selectedOrder->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</div>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedOrder->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>

                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($selectedOrder->price, 2) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $selectedOrder->created_at->format('g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Items Section --}}
                @if($selectedOrder->items && is_array($selectedOrder->items) && count($selectedOrder->items) > 0)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Purchased Items</h3>
                            <div class="text-sm text-gray-500">{{ count($selectedOrder->items) }} Items • {{ collect($selectedOrder->items)->sum('quantity') }} Units</div>
                        </div>
                        <div class="space-y-6">
                            @php
                                $totalItemsValue = 0;
                            @endphp
                            @foreach($selectedOrder->items as $index => $item)
                                @php
                                    $product = null;
                                    $quantity = $item['quantity'] ?? 1;
                                    $unitPrice = $item['price'] ?? 0;
                                    $itemTotal = $quantity * $unitPrice;
                                    $totalItemsValue += $itemTotal;

                                    if (isset($item['product_id'])) {
                                        $product = \App\Models\Product::find($item['product_id']);
                                    }
                                @endphp

                                <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                                    <div class="flex items-start space-x-6">
                                        {{-- Product Image/Icon --}}
                                        <div class="flex-shrink-0">
                                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>

                                        {{-- Product Details --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h5 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                                        {{ $product->name ?? $item['name'] ?? $item['product_name'] ?? 'Product' }}
                                                    </h5>

                                                    {{-- Product Meta Information --}}
                                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                                                        @if($product || isset($item['model']))
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                                Model: {{ $product->model ?? $item['model'] }}
                                                            </span>
                                                        @endif
                                                        @if($product || isset($item['sku']))
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                                SKU: {{ $product->sku ?? $item['sku'] }}
                                                            </span>
                                                        @endif
                                                        @if(isset($item['item_id']))
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                                ID: {{ $item['item_id'] }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Product Categories --}}
                                                    <div class="flex flex-wrap gap-2 mb-4">
                                                        @if($product && $product->category)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ ucfirst($product->category) }}
                                                            </span>
                                                        @endif
                                                        @if($product && $product->target_market)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->target_market === 'flagship' ? 'bg-blue-100 text-blue-800' : ($product->target_market === 'mid-range' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                                {{ ucfirst(str_replace('-', ' ', $product->target_market)) }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Product Description --}}
                                                    @if($product && $product->description)
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                            {{ $product->description }}
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Pricing Section --}}
                                                <div class="flex-shrink-0 text-right ml-6">
                                                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 min-w-[160px] rounded-lg border border-gray-200 shadow-sm p-4">
                                                        {{-- Quantity --}}
                                                        <div class="text-center mb-3">
                                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</div>
                                                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $quantity }}</div>
                                                        </div>

                                                        <hr class="border-gray-200 dark:border-gray-700 my-3">

                                                        {{-- Unit Price --}}
                                                        <div class="text-center mb-3">
                                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit Price</div>
                                                            <div class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($unitPrice, 2) }}</div>
                                                        </div>

                                                        {{-- Total Price --}}
                                                        <div class="bg-green-100 dark:bg-green-800/50 rounded-lg border border-gray-200 p-3">
                                                            <div class="text-center">
                                                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</div>
                                                                <div class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($itemTotal, 2) }}</div>
                                                            </div>
                                                        </div>

                                                        {{-- MSRP Comparison --}}
                                                        @if($product && $product->msrp && $product->msrp != $unitPrice)
                                                            <hr class="border-gray-200 dark:border-gray-700 my-3">
                                                            <div class="text-center">
                                                                <p class="text-xs text-green-600 dark:text-green-400">
                                                                    MSRP: ${{ number_format($product->msrp, 2) }}
                                                                </p>
                                                                @php
                                                                    $discount = (($product->msrp - $unitPrice) / $product->msrp) * 100;
                                                                @endphp
                                                                @if($discount > 0)
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                                                        {{ number_format($discount, 1) }}% OFF
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Order Summary Footer --}}
                            <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/30 dark:via-purple-900/30 dark:to-pink-900/30 border-2 border-dashed border-indigo-200 dark:border-indigo-700 rounded-lg p-6">
                                <div class="flex justify-between items-center">
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-indigo-100 dark:bg-indigo-900/50 rounded-xl px-4 py-2">
                                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Items</div>
                                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ count($selectedOrder->items) }}</div>
                                            </div>
                                            <div class="bg-purple-100 dark:bg-purple-900/50 rounded-xl px-4 py-2">
                                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Quantity</div>
                                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ collect($selectedOrder->items)->sum('quantity') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Order Total</p>
                                        <p class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                            ${{ number_format($selectedOrder->price, 2) }}
                                        </p>
                                        @if($totalItemsValue != $selectedOrder->price)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Items: ${{ number_format($totalItemsValue, 2) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">No items found</p>
                            <p class="text-gray-500 dark:text-gray-400">This order doesn't contain detailed item information.</p>
                        </div>
                    </div>
                @endif
            </div>

        {{-- Modal Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    @if($selectedOrder)
                        Order created {{ $selectedOrder->created_at->diffForHumans() }}
                    @endif
                </div>
                <div class="flex space-x-4">
                    <button
                        wire:click="exportOrder({{ $selectedOrder?->id }})"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Order
                    </button>

                    <button
                        @click="$wire.closeModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('export-order', (event) => {
            console.log('Exporting order:', event.orderId);
        });
    });
</script>
@endpush
