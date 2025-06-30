<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Create New Order</h2>
        <div>
            <a href="{{ route('orders.index') }}">
                <x-mary-button color="blue" outline>
                    <x-mary-icon name="arrow-left" class="mr-1" /> Back to Orders
                </x-mary-button>
            </a>
        </div>
    </div>

    <form wire:submit="createOrder">
        <!-- Buyer and Seller Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <x-mary-label for="buyer">Buyer *</x-mary-label>
                <x-mary-select id="buyer" wire:model="selectedBuyer" placeholder="Select a buyer...">
                    @foreach($buyerOptions as $buyer)
                        <option value="{{ $buyer->id }}">
                            {{ $buyer->name }} ({{ ucfirst($buyer->role) }})
                        </option>
                    @endforeach
                </x-mary-select>
                @error('selectedBuyer')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-mary-label for="seller">Seller (Aktina) *</x-mary-label>
                <x-mary-select id="seller" wire:model="selectedSeller" placeholder="Select a seller...">
                    @foreach($sellerOptions as $seller)
                        <option value="{{ $seller->id }}">
                            {{ $seller->name }} ({{ ucfirst($seller->role) }})
                        </option>
                    @endforeach
                </x-mary-select>
                @error('selectedSeller')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">Order Items</h3>
                <x-mary-button type="button" wire:click="addItem" color="blue" sm>
                    <x-mary-icon name="plus" class="mr-1" /> Add Item
                </x-mary-button>
            </div>

            <div class="space-y-4">
                @foreach($selectedItems as $index => $item)
                    <div class="flex flex-col md:flex-row md:items-end gap-4 p-4 rounded-lg bg-gray-50">
                        <div class="flex-1">
                            <x-mary-label for="product-{{ $index }}">Product *</x-mary-label>
                            <x-mary-select id="product-{{ $index }}" wire:model.live="selectedItems.{{ $index }}.product_id" placeholder="Select a product...">
                                @foreach($productOptions as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} - ${{ number_format($product->price, 2) }}</option>
                                @endforeach
                            </x-mary-select>
                            @error("selectedItems.{$index}.product_id")
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:w-32">
                            <x-mary-label for="quantity-{{ $index }}">Quantity *</x-mary-label>
                            <x-mary-input id="quantity-{{ $index }}" type="number" min="1" wire:model.live="selectedItems.{{ $index }}.quantity" />
                            @error("selectedItems.{$index}.quantity")
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:w-32">
                            <x-mary-label>Price</x-mary-label>
                            <p class="font-semibold">
                                ${{ number_format($item['price'] * $item['quantity'], 2) }}
                            </p>
                        </div>

                        <div>
                            <x-mary-button type="button" wire:click="removeItem({{ $index }})" color="red" sm outline>
                                <x-mary-icon name="trash" />
                            </x-mary-button>
                        </div>
                    </div>

                    <!-- Stock Level Warning -->
                    @if(!empty($stockLevels))
                        @foreach($stockLevels as $stockCheck)
                            @if($stockCheck['product_id'] == $item['product_id'])
                                @if(!$stockCheck['in_stock'])
                                    <x-mary-alert title="Stock Issue" color="red">
                                        This product is out of stock.
                                    </x-mary-alert>
                                @elseif($stockCheck['has_warning'])
                                    <x-mary-alert title="Stock Warning" color="amber">
                                        Ordering a large quantity may affect stock levels.
                                    </x-mary-alert>
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="mb-6">
            <x-mary-card>
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Order Summary</h3>
                    <p class="text-xl font-bold">
                        Total: ${{ number_format($totalPrice, 2) }}
                    </p>
                </div>
            </x-mary-card>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <x-mary-button type="submit" color="blue" size="lg" wire:loading.attr="disabled">
                <span wire:loading.remove>Create Order</span>
                <span wire:loading>Processing...</span>
            </x-mary-button>
        </div>
    </form>
</div>
