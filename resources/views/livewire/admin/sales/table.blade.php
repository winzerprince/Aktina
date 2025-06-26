<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border-2 border-zinc-200 dark:border-zinc-700 overflow-hidden">
    {{-- Table Header --}}
    <div class="bg-zinc-50 dark:bg-zinc-900 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">Sales Orders</h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">View and manage all sales orders</p>
    </div>

    {{-- Table Content --}}
    <div class="overflow-x-auto">
        @php
            $headers = [
                ['key' => 'id', 'label' => 'Order #', 'class' => 'w-20 text-center'],
                ['key' => 'buyer_name', 'label' => 'Buyer', 'class' => 'w-64'],
                ['key' => 'seller_name', 'label' => 'Seller', 'class' => 'w-64'],
                ['key' => 'price', 'label' => 'Price', 'class' => 'w-32 text-right'],
                ['key' => 'created_at', 'label' => 'Date', 'class' => 'w-40'],
            ];
        @endphp

        <x-table
            :headers="$headers"
            :rows="$orders"
            striped
            class="min-w-full dark:bg-zinc-800"
            with-pagination
            per-page="5"
        >
            {{-- Order ID Cell --}}
            @scope('cell_id', $order)
                <div class="text-center">
                    <x-badge :value="'#' . $order->id" class="badge-ghost dark:bg-zinc-700 dark:text-zinc-300 font-mono text-xs" />
                </div>
            @endscope

            {{-- Buyer Cell --}}
            @scope('cell_buyer_name', $order)
                <div class="flex items-center space-x-3 py-2">
                    <x-avatar :image="null" :label="$order->buyer->name" class="!w-10 !h-10" />
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $order->buyer->name }}</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->buyer->email }}</div>
                    </div>
                </div>
            @endscope

            {{-- Seller Cell with Production Manager highlighting --}}
            @scope('cell_seller_name', $order)
                <div class="flex items-center space-x-3 py-2">
                    <x-avatar :image="null" :label="$order->seller->name" class="!w-10 !h-10" />
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-medium {{ $order->seller->productionManager ? 'text-green-700 dark:text-green-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                                {{ $order->seller->name }}
                            </span>
                            @if($order->seller->productionManager)
                                <x-badge value="PM" class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 badge-sm rounded-full font-semibold flex items-center justify-center px-2 shadow-sm border border-green-200 dark:border-green-800" />
                            @endif
                        </div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->seller->email }}</div>
                    </div>
                </div>
            @endscope

            {{-- Price Cell --}}
            @scope('cell_price', $order)
            <div class="text-right">
    <span class="text-lg font-semibold
        @if($order->price < 1000)
            text-red-600 dark:text-red-400
        @elseif($order->price <= 10000)
            text-yellow-600 dark:text-yellow-400
        @else
            text-green-600 dark:text-green-400
        @endif
    ">${{ number_format($order->price, 2) }}</span>
            </div>
            @endscope

            {{-- Date Cell --}}
            @scope('cell_created_at', $order)
                <div class="py-2 px-5">
                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $order->created_at->format('M d, Y') }}</div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->created_at->format('H:i A') }}</div>
                </div>
            @endscope

            {{-- Empty State --}}
            <x-slot:empty>
                <div class="text-center py-12">
                    <x-icon name="o-shopping-bag" class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No orders found</h3>
                    <p class="text-zinc-500 dark:text-zinc-400">There are no sales orders to display at this time.</p>
                </div>
            </x-slot:empty>
        </x-table>
    </div>
</div>
