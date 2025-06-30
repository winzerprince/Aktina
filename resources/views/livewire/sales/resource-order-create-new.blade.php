<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Create New Resource Order</h2>
        <div>
            <a href="{{ route('resource-orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Resource Orders
            </a>
        </div>
    </div>

    <form wire:submit="createResourceOrder">
        <!-- Buyer and Seller Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                <select id="supplier" wire:model="selectedSupplier"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select a supplier...</option>
                    @foreach($supplierOptions as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->name }} ({{ $supplier->company_name }})
                        </option>
                    @endforeach
                </select>
                @error('selectedSupplier')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="buyer" class="block text-sm font-medium text-gray-700 mb-1">Aktina Representative *</label>
                <select id="buyer" wire:model="selectedBuyer"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select an Aktina representative...</option>
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
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">Order Resources</h3>
                <button type="button" wire:click="addItem"
                        class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Resource
                </button>
            </div>

            @if(!empty($resourceItems))
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($resourceItems as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select wire:model="resourceItems.{{ $index }}.resource_id"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="">Select a resource...</option>
                                            @foreach($resourceOptions as $resource)
                                                <option value="{{ $resource->id }}">{{ $resource->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("resourceItems.{$index}.resource_id")
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number"
                                               wire:model="resourceItems.{{ $index }}.quantity"
                                               min="1"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                               placeholder="Qty">
                                        @error("resourceItems.{$index}.quantity")
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number"
                                               wire:model="resourceItems.{{ $index }}.price"
                                               step="0.01"
                                               min="0"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                               placeholder="0.00">
                                        @error("resourceItems.{$index}.price")
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button type="button" wire:click="removeItem({{ $index }})"
                                                class="inline-flex items-center px-2 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <p class="text-gray-500">No resources added yet. Click "Add Resource" to get started.</p>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Order Summary</h3>
            <div class="flex justify-between items-center text-lg font-bold">
                <span>Total Amount:</span>
                <span class="text-green-600">${{ number_format($this->getTotalPrice(), 2) }}</span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors duration-200">
                <span wire:loading.remove>Create Resource Order</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </form>
</div>
