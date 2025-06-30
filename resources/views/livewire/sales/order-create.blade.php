<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Create New Order</h2>
        <div>
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Orders
            </a>
        </div>
    </div>

    <form wire:submit="createOrder">
        <!-- Buyer and Seller Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="buyer" class="block text-sm font-medium text-gray-700 mb-1">Buyer *</label>
                <select id="buyer" wire:model="selectedBuyer"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select a buyer...</option>
                    @foreach($buyerOptions as $buyer)
                        <option value="{{ $buyer->id }}">
                            {{ $buyer->name }} ({{ ucfirst($buyer->role) }})
                        </option>
                    @endforeach
                </select>
                @error('selectedBuyer')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="seller" class="block text-sm font-medium text-gray-700 mb-1">Seller (Aktina) *</label>
                <select id="seller" wire:model="selectedSeller"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select a seller...</option>
                    @foreach($sellerOptions as $seller)
                        <option value="{{ $seller->id }}">
                            {{ $seller->name }} ({{ ucfirst($seller->role) }})
                        </option>
                    @endforeach
                </select>
                @error('selectedSeller')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">Order Items</h3>
                <button type="button" wire:click="addItem"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Item
                </button>
            </div>

            <div class="space-y-4">
                @foreach($selectedItems as $index => $item)
                    <div class="flex flex-col md:flex-row md:items-end gap-4 p-4 rounded-lg bg-gray-50">
                        <div class="flex-1">
                            <label for="product-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                            <select id="product-{{ $index }}" wire:model.live="selectedItems.{{ $index }}.product_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select a product...</option>
                                @foreach($productOptions as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} - ${{ number_format($product->msrp, 2) }}</option>
                                @endforeach
                            </select>
                            @error("selectedItems.{$index}.product_id")
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:w-32">
                            <label for="quantity-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input id="quantity-{{ $index }}" type="number" min="1" wire:model.live="selectedItems.{{ $index }}.quantity"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            @error("selectedItems.{$index}.quantity")
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:w-32">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <p class="font-semibold">
                                ${{ number_format($item['price'] * $item['quantity'], 2) }}
                            </p>
                        </div>

                        <div>
                            <button type="button" wire:click="removeItem({{ $index }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Stock Level Warning -->
                    @if(!empty($stockLevels))
                        @foreach($stockLevels as $stockCheck)
                            @if($stockCheck['product_id'] == $item['product_id'])
                                @if(!$stockCheck['in_stock'])
                                    <div class="border-l-4 border-red-400 bg-red-50 p-4 rounded-md mt-2">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Stock Issue</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <p>This product is out of stock.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($stockCheck['has_warning'])
                                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded-md mt-2">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-yellow-800">Stock Warning</h3>
                                                <div class="mt-2 text-sm text-yellow-700">
                                                    <p>Ordering a large quantity may affect stock levels.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="mb-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Order Summary</h3>
                    <p class="text-xl font-bold">
                        Total: ${{ number_format($totalPrice, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors duration-200">
                <span wire:loading.remove>Create Order</span>
                <span wire:loading>Processing...</span>
            </button>
        </div>
    </form>
</div>
