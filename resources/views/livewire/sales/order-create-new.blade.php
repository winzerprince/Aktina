<div>
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Order</h1>
                    <p class="mt-1 text-sm text-gray-600">Fill in the details to create a new order</p>
                </div>
                <div>
                    <a href="{{ route('orders.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
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

        <!-- Order Creation Form -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <form wire:submit="createOrder">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Order Information</h2>
                </div>

                <div class="p-6 space-y-8">
                    <!-- Step 1: Buyer and Seller Selection -->
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">1</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Select Buyer and Seller</h3>
                                <p class="text-sm text-gray-600">Choose who is buying and selling in this transaction</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 ml-12">
                            <!-- Buyer Selection -->
                            <div class="space-y-2">
                                <label for="buyer" class="block text-sm font-medium text-gray-700">
                                    Buyer <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="buyer" wire:model.live="selectedBuyer"
                                            class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg transition-colors duration-200 @error('selectedBuyer') border-red-300 @enderror">
                                        <option value="">Select a buyer...</option>
                                        @foreach($buyerOptions as $buyer)
                                            <option value="{{ $buyer->id }}">
                                                {{ $buyer->name }} ({{ ucfirst($buyer->role) }})
                                                @if($buyer->company_name) - {{ $buyer->company_name }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('selectedBuyer')
                                    <p class="text-red-600 text-sm mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Seller Selection -->
                            <div class="space-y-2">
                                <label for="seller" class="block text-sm font-medium text-gray-700">
                                    Seller (Aktina) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="seller" wire:model.live="selectedSeller"
                                            class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg transition-colors duration-200 @error('selectedSeller') border-red-300 @enderror">
                                        <option value="">Select a seller...</option>
                                        @foreach($sellerOptions as $seller)
                                            <option value="{{ $seller->id }}">
                                                {{ $seller->name }} ({{ ucfirst($seller->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('selectedSeller')
                                    <p class="text-red-600 text-sm mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Order Items -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold">2</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Add Order Items</h3>
                                    <p class="text-sm text-gray-600">Select products and quantities for this order</p>
                                </div>
                            </div>
                            <button type="button" wire:click="addItem"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div class="ml-12 space-y-4">
                            @forelse($selectedItems as $index => $item)
                                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 relative">
                                    <!-- Item Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-medium text-gray-900">Item #{{ $index + 1 }}</h4>
                                        @if(count($selectedItems) > 1)
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="inline-flex items-center px-2 py-1 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Remove
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Item Fields -->
                                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                        <!-- Product Selection -->
                                        <div class="lg:col-span-2">
                                            <label for="product-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                Product <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select id="product-{{ $index }}" wire:model.live="selectedItems.{{ $index }}.product_id"
                                                        class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg transition-colors duration-200 @error('selectedItems.'.$index.'.product_id') border-red-300 @enderror">
                                                    <option value="">Select a product...</option>
                                                    @foreach($productOptions as $product)
                                                        <option value="{{ $product->id }}">
                                                            {{ $product->name }}
                                                            @if($product->model) - {{ $product->model }} @endif
                                                            - ${{ number_format($product->msrp, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>
                                            @error('selectedItems.'.$index.'.product_id')
                                                <p class="text-red-600 text-xs mt-1 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- Quantity -->
                                        <div>
                                            <label for="quantity-{{ $index }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                Quantity <span class="text-red-500">*</span>
                                            </label>
                                            <input id="quantity-{{ $index }}" type="number" min="1" wire:model.live="selectedItems.{{ $index }}.quantity"
                                                   class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('selectedItems.'.$index.'.quantity') border-red-300 @enderror"
                                                   placeholder="1" />
                                            @error('selectedItems.'.$index.'.quantity')
                                                <p class="text-red-600 text-xs mt-1 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- Price Display -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal</label>
                                            <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    ${{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock Level Warnings -->
                                    @if(!empty($stockLevels))
                                        @foreach($stockLevels as $stockCheck)
                                            @if($stockCheck['product_id'] == $item['product_id'])
                                                @if(!$stockCheck['in_stock'])
                                                    <div class="mt-4 border-l-4 border-red-400 bg-red-50 p-3 rounded-md">
                                                        <div class="flex items-center">
                                                            <svg class="h-4 w-4 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span class="text-sm font-medium text-red-800">Out of Stock</span>
                                                        </div>
                                                        <p class="text-xs text-red-700 mt-1">This product is currently out of stock.</p>
                                                    </div>
                                                @elseif($stockCheck['has_warning'])
                                                    <div class="mt-4 border-l-4 border-yellow-400 bg-yellow-50 p-3 rounded-md">
                                                        <div class="flex items-center">
                                                            <svg class="h-4 w-4 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span class="text-sm font-medium text-yellow-800">Stock Warning</span>
                                                        </div>
                                                        <p class="text-xs text-yellow-700 mt-1">Large quantity may affect stock levels.</p>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No items added</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by adding your first item to the order.</p>
                                    <div class="mt-6">
                                        <button type="button" wire:click="addItem"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Add First Item
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Step 3: Order Summary -->
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">3</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
                                <p class="text-sm text-gray-600">Review your order details before submission</p>
                            </div>
                        </div>

                        <div class="ml-12">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="text-center md:text-left">
                                        <p class="text-sm font-medium text-gray-600">Total Items</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ count(array_filter($selectedItems, function($item) { return !empty($item['product_id']); })) }}</p>
                                    </div>
                                    <div class="text-center md:text-left">
                                        <p class="text-sm font-medium text-gray-600">Total Quantity</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ array_sum(array_column(array_filter($selectedItems, function($item) { return !empty($item['product_id']); }), 'quantity')) }}
                                        </p>
                                    </div>
                                    <div class="text-center md:text-right">
                                        <p class="text-sm font-medium text-gray-600">Order Total</p>
                                        <p class="text-3xl font-bold text-green-600">${{ number_format($totalPrice, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Note:</span> Orders will be created with pending status and require approval.
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('orders.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            <span wire:loading.remove>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Order
                            </span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
