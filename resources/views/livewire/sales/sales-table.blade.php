<div class="space-y-6">
    {{-- Header Section with Date Filters --}}
    <x-card title="Sales Overview" subtitle="Track and manage your sales performance" class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
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

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-stat
                title="Total Revenue"
                description="Total revenue from sales"
                value="${{ number_format($sales->sum('price'), 2) }}"
                icon="o-currency-dollar"
                color="text-green-500"
            />

            <x-stat
                title="Total Orders"
                description="Number of orders"
                value="{{ $sales->count() }}"
                icon="o-shopping-bag"
                color="text-blue-500"
            />

            <x-stat
                title="Average Order Value"
                description="Average value per order"
                value="${{ $sales->count() > 0 ? number_format($sales->avg('price'), 2) : '0.00' }}"
                icon="o-chart-bar"
                color="text-purple-500"
            />
        </div>
    </x-card>

    {{-- Sales Table with Mary UI --}}
    <x-card title="Sales Records" subtitle="Click on any row to view order details">
        @php
            $headers = [
                ['key' => 'id', 'label' => 'Order ID', 'class' => 'w-20'],
                ['key' => 'buyer', 'label' => 'Buyer'],
                ['key' => 'price', 'label' => 'Total Amount', 'class' => 'text-right'],
                ['key' => 'created_at', 'label' => 'Date'],
            ];
        @endphp

        <x-table
            :headers="$headers"
            :rows="$sales"
            @row-click="viewOrder($event.detail.id)"
            striped
        >
            {{-- Order ID Column --}}
            @scope('cell_id', $order)
                <x-badge
                    value="#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}"
                    class="badge-primary"
                />
            @endscope

            {{-- Buyer Column --}}
            @scope('cell_buyer', $order)
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <x-avatar
                            :title="$order->buyer ? substr($order->buyer->name, 0, 2) : 'UK'"
                            class="!w-10 !h-10 !text-sm"
                        />
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
            @endscope

            {{-- Price Column --}}
            @scope('cell_price', $order)
                <div class="text-right">
                    <div class="text-sm font-bold text-green-600 dark:text-green-400">
                        ${{ number_format($order->price, 2) }}
                    </div>
                    @if($order->items && is_array($order->items))
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ count($order->items) }} item(s) • {{ collect($order->items)->sum('quantity') }} units
                        </div>
                    @endif
                </div>
            @endscope

            {{-- Date Column --}}
            @scope('cell_created_at', $order)
                <div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        {{ $order->created_at->format('M d, Y') }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $order->created_at->format('g:i A') }}
                    </div>
                </div>
            @endscope

            {{-- Empty State --}}
            <x-slot:empty>
                <x-icon name="o-document-text" label="No sales found. Try adjusting your date range or check back later." />
            </x-slot:empty>
        </x-table>

        {{-- Pagination --}}
        @if($sales->hasPages())
            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        @endif
    </x-card>

    {{-- Enhanced Spotlight Modal with Mary UI --}}
    <x-modal wire:model="showOrderModal" title="Order Details" subtitle="Complete order information and purchased items" class="backdrop-blur">
        @if($selectedOrder)
            <div class="space-y-8">
                {{-- Order Header --}}
                <x-card class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/30 dark:via-emerald-900/30 dark:to-teal-900/30 border-green-200 dark:border-green-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl">
                                <x-icon name="o-shopping-bag" class="w-8 h-8 text-white" />
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
                            <x-badge value="Completed" class="badge-success" />
                        </div>
                    </div>
                </x-card>

                {{-- Customer and Order Information Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Customer Information --}}
                    <x-card title="Customer Information" class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 border-blue-200 dark:border-blue-700">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <x-avatar
                                    :title="$selectedOrder->buyer ? substr($selectedOrder->buyer->name, 0, 2) : 'UK'"
                                    class="!w-16 !h-16 !text-xl"
                                />
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
                                <x-hr />
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
                            @endif
                        </div>
                    </x-card>

                    {{-- Order Summary --}}
                    <x-card title="Order Summary" class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 border-purple-200 dark:border-purple-700">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <x-stat
                                    title="Order ID"
                                    value="#{{ str_pad($selectedOrder->id, 4, '0', STR_PAD_LEFT) }}"
                                    class="text-center"
                                />
                                <x-stat
                                    title="Date"
                                    value="{{ $selectedOrder->created_at->format('M d, Y') }}"
                                    class="text-center"
                                />
                            </div>

                            <x-stat
                                title="Total Amount"
                                value="${{ number_format($selectedOrder->price, 2) }}"
                                description="{{ $selectedOrder->created_at->format('g:i A') }}"
                                class="text-center"
                            />
                        </div>
                    </x-card>
                </div>

                {{-- Order Items Section --}}
                @if($selectedOrder->items && is_array($selectedOrder->items) && count($selectedOrder->items) > 0)
                    <x-card title="Purchased Items" subtitle="{{ count($selectedOrder->items) }} Items • {{ collect($selectedOrder->items)->sum('quantity') }} Units">
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

                                <x-card class="shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start space-x-6">
                                        {{-- Product Image/Icon --}}
                                        <div class="flex-shrink-0">
                                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                                                <x-icon name="o-device-phone-mobile" class="w-10 h-10 text-white" />
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
                                                            <x-badge
                                                                value="Model: {{ $product->model ?? $item['model'] }}"
                                                                class="badge-primary badge-outline"
                                                            />
                                                        @endif
                                                        @if($product || isset($item['sku']))
                                                            <x-badge
                                                                value="SKU: {{ $product->sku ?? $item['sku'] }}"
                                                                class="badge-secondary badge-outline"
                                                            />
                                                        @endif
                                                        @if(isset($item['item_id']))
                                                            <x-badge
                                                                value="ID: {{ $item['item_id'] }}"
                                                                class="badge-accent badge-outline"
                                                            />
                                                        @endif
                                                    </div>

                                                    {{-- Product Categories --}}
                                                    <div class="flex flex-wrap gap-2 mb-4">
                                                        @if($product && $product->category)
                                                            <x-badge
                                                                value="{{ ucfirst($product->category) }}"
                                                                class="badge-info"
                                                            />
                                                        @endif
                                                        @if($product && $product->target_market)
                                                            <x-badge
                                                                value="{{ ucfirst(str_replace('-', ' ', $product->target_market)) }}"
                                                                class="{{ $product->target_market === 'flagship' ? 'badge-primary' : ($product->target_market === 'mid-range' ? 'badge-success' : 'badge-warning') }}"
                                                            />
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
                                                    <x-card class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 min-w-[160px]">
                                                        {{-- Quantity --}}
                                                        <x-stat
                                                            title="Quantity"
                                                            value="{{ $quantity }}"
                                                            class="text-center mb-3"
                                                        />

                                                        <x-hr />

                                                        {{-- Unit Price --}}
                                                        <x-stat
                                                            title="Unit Price"
                                                            value="${{ number_format($unitPrice, 2) }}"
                                                            class="text-center mb-3"
                                                        />

                                                        {{-- Total Price --}}
                                                        <x-card class="bg-green-100 dark:bg-green-800/50">
                                                            <x-stat
                                                                title="Total"
                                                                value="${{ number_format($itemTotal, 2) }}"
                                                                class="text-center"
                                                            />
                                                        </x-card>

                                                        {{-- MSRP Comparison --}}
                                                        @if($product && $product->msrp && $product->msrp != $unitPrice)
                                                            <x-hr />
                                                            <div class="text-center">
                                                                <p class="text-xs text-green-600 dark:text-green-400">
                                                                    MSRP: ${{ number_format($product->msrp, 2) }}
                                                                </p>
                                                                @php
                                                                    $discount = (($product->msrp - $unitPrice) / $product->msrp) * 100;
                                                                @endphp
                                                                @if($discount > 0)
                                                                    <x-badge
                                                                        value="{{ number_format($discount, 1) }}% OFF"
                                                                        class="badge-error mt-1"
                                                                    />
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </x-card>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </x-card>
                            @endforeach

                            {{-- Order Summary Footer --}}
                            <x-card class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/30 dark:via-purple-900/30 dark:to-pink-900/30 border-2 border-dashed border-indigo-200 dark:border-indigo-700">
                                <div class="flex justify-between items-center">
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-4">
                                            <x-stat
                                                title="Total Items"
                                                value="{{ count($selectedOrder->items) }}"
                                                class="bg-indigo-100 dark:bg-indigo-900/50 rounded-xl px-4 py-2"
                                            />
                                            <x-stat
                                                title="Total Quantity"
                                                value="{{ collect($selectedOrder->items)->sum('quantity') }}"
                                                class="bg-purple-100 dark:bg-purple-900/50 rounded-xl px-4 py-2"
                                            />
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
                            </x-card>
                        </div>
                    </x-card>
                @else
                    <x-card>
                        <div class="text-center py-8">
                            <x-icon name="o-inbox" class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                            <p class="text-lg font-medium text-gray-900 dark:text-white">No items found</p>
                            <p class="text-gray-500 dark:text-gray-400">This order doesn't contain detailed item information.</p>
                        </div>
                    </x-card>
                @endif
            </div>
        @endif

        {{-- Modal Actions --}}
        <x-slot:actions>
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    @if($selectedOrder)
                        Order created {{ $selectedOrder->created_at->diffForHumans() }}
                    @endif
                </div>
                <div class="flex space-x-4">
                    <x-button
                        label="Export Order"
                        icon="o-arrow-down-tray"
                        class="btn-outline"
                        wire:click="exportOrder({{ $selectedOrder?->id }})"
                    />

                    <x-button
                        label="Close"
                        class="btn-primary"
                        @click="$wire.closeModal()"
                    />
                </div>
            </div>
        </x-slot:actions>
    </x-modal>
</div>
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
