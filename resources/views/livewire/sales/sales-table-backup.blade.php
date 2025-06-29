<div class="space-y-6">
    {{-- Header Section with Date Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Overview</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track and manage your sales performance</p>
            </div>

            {{-- Date Filter Controls --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <x-datetime
                    label="Start Date"
                    wire:model.live="startDate"
                    type="date"
                    class="min-w-40"
                />

                <x-datetime
                    label="End Date"
                    wire:model.live="endDate"
                    type="date"
                    class="min-w-40"
                />

                <x-button
                    label="Apply Filters"
                    icon="o-funnel"
                    class="btn-primary self-end"
                    wire:click="$refresh"
                />
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <x-icon name="o-currency-dollar" class="w-5 h-5 text-white" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Total Revenue</p>
                        <p class="text-lg font-bold text-green-900 dark:text-green-100">
                            ${{ number_format($sales->sum('price'), 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <x-icon name="o-shopping-bag" class="w-5 h-5 text-white" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Total Orders</p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $sales->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-500 rounded-lg">
                        <x-icon name="o-chart-bar" class="w-5 h-5 text-white" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-purple-800 dark:text-purple-200">Avg. Order Value</p>
                        <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                            ${{ $sales->count() > 0 ? number_format($sales->avg('price'), 2) : '0.00' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sales Records</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Click on any row to view order details</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Order ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Buyer
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Total Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($sales as $order)
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200 group"
                            wire:click="viewOrder({{ $order->id }})"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ $order->buyer ? substr($order->buyer->name, 0, 2) : 'UK' }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $order->buyer->name ?? 'Unknown Buyer' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $order->buyer->email ?? 'N/A' }}
                                        </div>
                                        @if($order->buyer && $order->buyer->company_name)
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $order->buyer->company_name }}
                                                @if($order->buyer->role)
                                                    • {{ $order->buyer->role }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-green-600 dark:text-green-400">
                                    ${{ number_format($order->price, 2) }}
                                </div>
                                @if($order->items && is_array($order->items))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ count($order->items) }} item(s) • {{ collect($order->items)->sum('quantity') }} units
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $order->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $order->created_at->format('g:i A') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white">No sales found</p>
                                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your date range or check back later.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

    {{-- Enhanced Spotlight Modal --}}
    @if($showOrderModal && $selectedOrder)
        <div
            class="fixed inset-0 z-50 overflow-hidden"
            x-data="{ show: @entangle('showOrderModal') }"
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="$wire.closeModal()"
            style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
        >
            {{-- Enhanced Backdrop with Blur --}}
            <div
                class="fixed inset-0 bg-black/60 backdrop-blur-md"
                style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"
                @click="$wire.closeModal()"
            ></div>

            {{-- Spotlight Container --}}
            <div class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto">
                <div
                    class="relative bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-6xl w-full max-h-[95vh] overflow-hidden border border-gray-200 dark:border-gray-700"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-90 translate-y-8"
                    @click.stop
                >
                    {{-- Modal Header with Enhanced Design --}}
                    <div class="flex items-center justify-between p-8 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Order Details</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Complete order information and purchased items</p>
                        </div>
                        <button
                            wire:click="closeModal"
                            class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                        >
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable Modal Body --}}
                    <div class="overflow-y-auto max-h-[calc(95vh-200px)]">
                        <div class="p-8 space-y-8">
                            {{-- Order Header with Enhanced Styling --}}
                            <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/30 dark:via-emerald-900/30 dark:to-teal-900/30 rounded-2xl p-6 border border-green-200 dark:border-green-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-green-900 dark:text-green-100">
                                                Order #{{ str_pad($selectedOrder->id, 4, '0', STR_PAD_LEFT) }}
                                            </h3>
                                            <p class="text-green-700 dark:text-green-300 text-lg">
                                                Placed on {{ $selectedOrder->created_at->format('F j, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-4xl font-bold text-green-900 dark:text-green-100 mb-2">
                                            ${{ number_format($selectedOrder->price, 2) }}
                                        </div>
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Completed
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Customer and Order Information Grid --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                {{-- Enhanced Customer Information --}}
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-700">
                                    <h4 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Customer Information
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                                {{ $selectedOrder->buyer ? substr($selectedOrder->buyer->name, 0, 2) : 'UK' }}
                                            </div>
                                            <div>
                                                <div class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                                    {{ $selectedOrder->buyer->name ?? 'Unknown Buyer' }}
                                                </div>
                                                <div class="text-blue-700 dark:text-blue-300">
                                                    {{ $selectedOrder->buyer->email ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>

                                        @if($selectedOrder->buyer && $selectedOrder->buyer->company_name)
                                            <div class="pt-4 border-t border-blue-200 dark:border-blue-600">
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
                                        @endif
                                    </div>
                                </div>

                                {{-- Enhanced Order Summary --}}
                                <div class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 border border-purple-200 dark:border-purple-700">
                                    <h4 class="text-xl font-bold text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Order Summary
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-white dark:bg-purple-900/30 rounded-xl p-3">
                                                <p class="text-sm font-medium text-purple-600 dark:text-purple-300">Order ID</p>
                                                <p class="text-lg font-bold text-purple-900 dark:text-purple-100 font-mono">
                                                    #{{ str_pad($selectedOrder->id, 4, '0', STR_PAD_LEFT) }}
                                                </p>
                                            </div>
                                            <div class="bg-white dark:bg-purple-900/30 rounded-xl p-3">
                                                <p class="text-sm font-medium text-purple-600 dark:text-purple-300">Date</p>
                                                <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                                                    {{ $selectedOrder->created_at->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="bg-white dark:bg-purple-900/30 rounded-xl p-4">
                                            <p class="text-sm font-medium text-purple-600 dark:text-purple-300 mb-2">Total Amount</p>
                                            <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">
                                                ${{ number_format($selectedOrder->price, 2) }}
                                            </p>
                                            <p class="text-purple-700 dark:text-purple-300 text-sm mt-1">
                                                {{ $selectedOrder->created_at->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Enhanced Order Items Section --}}
                            @if($selectedOrder->items && is_array($selectedOrder->items) && count($selectedOrder->items) > 0)
                                <div class="bg-gradient-to-br from-gray-50 to-slate-100 dark:from-gray-800/50 dark:to-slate-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                                            <svg class="w-7 h-7 mr-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            Purchased Items
                                        </h4>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="bg-indigo-100 dark:bg-indigo-900/30 px-3 py-1 rounded-full font-medium">
                                                {{ count($selectedOrder->items) }} Items
                                            </span>
                                            <span class="bg-blue-100 dark:bg-blue-900/30 px-3 py-1 rounded-full font-medium">
                                                {{ collect($selectedOrder->items)->sum('quantity') }} Units
                                            </span>
                                        </div>
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

                                            <div class="bg-white dark:bg-gray-700/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow duration-200">
                                                <div class="flex items-start space-x-6">
                                                    {{-- Enhanced Product Image/Icon --}}
                                                    <div class="flex-shrink-0">
                                                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                                                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
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
                                                                        <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-2">
                                                                            <p class="text-xs font-medium text-blue-600 dark:text-blue-300">Model</p>
                                                                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                                                                {{ $product->model ?? $item['model'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if($product || isset($item['sku']))
                                                                        <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-2">
                                                                            <p class="text-xs font-medium text-green-600 dark:text-green-300">SKU</p>
                                                                            <p class="text-sm font-semibold text-green-900 dark:text-green-100">
                                                                                {{ $product->sku ?? $item['sku'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($item['item_id']))
                                                                        <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-2">
                                                                            <p class="text-xs font-medium text-purple-600 dark:text-purple-300">Item ID</p>
                                                                            <p class="text-sm font-semibold text-purple-900 dark:text-purple-100">
                                                                                {{ $item['item_id'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                {{-- Product Specifications --}}
                                                                @if($product && $product->specifications)
                                                                    <div class="bg-gray-50 dark:bg-gray-600/50 rounded-xl p-4 mb-4">
                                                                        <h6 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Specifications</h6>
                                                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                                                            @foreach(['display', 'ram', 'storage', 'processor'] as $spec)
                                                                                @if(isset($product->specifications[$spec]))
                                                                                    <div class="text-center">
                                                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ ucfirst($spec) }}</p>
                                                                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                                            {{ $product->specifications[$spec] }}
                                                                                        </p>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                {{-- Product Categories --}}
                                                                <div class="flex flex-wrap gap-2 mb-4">
                                                                    @if($product && $product->category)
                                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                            {{ ucfirst($product->category) }}
                                                                        </span>
                                                                    @endif
                                                                    @if($product && $product->target_market)
                                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                                            {{ $product->target_market === 'flagship' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
                                                                               ($product->target_market === 'mid-range' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
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

                                                            {{-- Enhanced Pricing Section --}}
                                                            <div class="flex-shrink-0 text-right ml-6">
                                                                <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-2xl p-4 space-y-3 min-w-[160px]">
                                                                    {{-- Quantity --}}
                                                                    <div class="text-center">
                                                                        <p class="text-xs font-semibold text-green-600 dark:text-green-300 uppercase tracking-wide">Quantity</p>
                                                                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $quantity }}</p>
                                                                    </div>

                                                                    <div class="border-t border-green-200 dark:border-green-700 pt-3">
                                                                        {{-- Unit Price --}}
                                                                        <div class="text-center mb-3">
                                                                            <p class="text-xs font-semibold text-green-600 dark:text-green-300 uppercase tracking-wide">Unit Price</p>
                                                                            <p class="text-lg font-bold text-green-900 dark:text-green-100">
                                                                                ${{ number_format($unitPrice, 2) }}
                                                                            </p>
                                                                        </div>

                                                                        {{-- Total Price --}}
                                                                        <div class="text-center bg-green-100 dark:bg-green-800/50 rounded-xl p-3">
                                                                            <p class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wide">Total</p>
                                                                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                                                                ${{ number_format($itemTotal, 2) }}
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    {{-- MSRP Comparison --}}
                                                                    @if($product && $product->msrp && $product->msrp != $unitPrice)
                                                                        <div class="text-center pt-2 border-t border-green-200 dark:border-green-700">
                                                                            <p class="text-xs text-green-600 dark:text-green-400">
                                                                                MSRP: ${{ number_format($product->msrp, 2) }}
                                                                            </p>
                                                                            @php
                                                                                $discount = (($product->msrp - $unitPrice) / $product->msrp) * 100;
                                                                            @endphp
                                                                            @if($discount > 0)
                                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1">
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

                                        {{-- Enhanced Order Summary Footer --}}
                                        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/30 dark:via-purple-900/30 dark:to-pink-900/30 rounded-2xl p-6 border-2 border-dashed border-indigo-200 dark:border-indigo-700">
                                            <div class="flex justify-between items-center">
                                                <div class="space-y-2">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="bg-indigo-100 dark:bg-indigo-900/50 rounded-xl px-4 py-2">
                                                            <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">Total Items</p>
                                                            <p class="text-xl font-bold text-indigo-900 dark:text-indigo-100">{{ count($selectedOrder->items) }}</p>
                                                        </div>
                                                        <div class="bg-purple-100 dark:bg-purple-900/50 rounded-xl px-4 py-2">
                                                            <p class="text-sm font-semibold text-purple-700 dark:text-purple-300">Total Quantity</p>
                                                            <p class="text-xl font-bold text-purple-900 dark:text-purple-100">{{ collect($selectedOrder->items)->sum('quantity') }}</p>
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
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 text-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white">No items found</p>
                                    <p class="text-gray-500 dark:text-gray-400">This order doesn't contain detailed item information.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Enhanced Modal Footer --}}
                    <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Order created {{ $selectedOrder->created_at->diffForHumans() }}
                        </div>
                        <div class="flex space-x-4">
                            <button
                                wire:click="exportOrder({{ $selectedOrder->id }})"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Order
                            </button>

                            <button
                                wire:click="closeModal"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                            >
                                Close
                            </button>
                        </div>
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
