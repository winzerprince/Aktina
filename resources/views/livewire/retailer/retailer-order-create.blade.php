<div class="bg-white shadow rounded-lg">
    <!-- Wizard Header -->
    <div class="p-4 sm:p-6 border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Create New Order
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            Follow these steps to create a new order.
        </p>
    </div>

    <!-- Progress Bar -->
    <div class="px-4 sm:px-6 py-4">
        <div class="flex items-center justify-between w-full mb-4">
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full {{ $step >= 1 ? 'bg-blue-600' : 'bg-gray-200' }} text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Select Seller</p>
                    </div>
                </div>
            </div>

            <div class="flex-1">
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200' }} text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Add Products</p>
                    </div>
                </div>
            </div>

            <div class="flex-1">
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }} text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Review & Submit</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-100">
            <div style="width: {{ ($step / 3) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 transition-all"></div>
        </div>
    </div>

    <!-- Success Message -->
    @if($successMessage)
    <div class="mx-4 sm:mx-6 mb-4 bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ $successMessage }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button wire:click="$set('successMessage', '')" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Error Message -->
    @if($errorMessage)
    <div class="mx-4 sm:mx-6 mb-4 bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ $errorMessage }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button wire:click="$set('errorMessage', '')" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Step Content -->
    <div class="px-4 sm:px-6 py-4">
        @if($step === 1)
            <!-- Step 1: Select Seller -->
            <div>
                <h4 class="text-base font-medium text-gray-900 mb-4">Select a Seller</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($sellers as $seller)
                        <div
                            class="border p-4 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors {{ $sellerId == $seller->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}"
                            wire:click="sellerSelected({{ $seller->id }})"
                        >
                            <div class="font-medium">{{ $seller->company_name }}</div>
                            <div class="text-sm text-gray-500">{{ $seller->name }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($step === 2)
            <!-- Step 2: Add Products -->
            <div>
                <h4 class="text-base font-medium text-gray-900 mb-4">Add Products to Your Order</h4>

                @if($availableProducts->isEmpty())
                    <div class="text-center py-8">
                        <div class="text-gray-500">No products available from this seller.</div>
                        <button
                            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            wire:click="previousStep"
                        >
                            Select Different Seller
                        </button>
                    </div>
                @else
                    @foreach($items as $index => $item)
                        <div class="flex flex-col md:flex-row gap-4 mb-6 pb-6 border-b border-gray-200">
                            <div class="flex-grow">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                                <select
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    wire:model="items.{{ $index }}.product_id"
                                    wire:change="productSelected({{ $index }}, $event.target.value)"
                                >
                                    <option value="">Select a product</option>
                                    @foreach($availableProducts as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-2 text-sm text-gray-500">
                                    @if(!empty($item['product_id']))
                                        {{ optional($availableProducts->firstWhere('id', $item['product_id']))->description }}
                                    @endif
                                </div>
                            </div>

                            <div class="w-full md:w-32">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input
                                    type="number"
                                    min="1"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    wire:model="items.{{ $index }}.quantity"
                                    wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                >
                            </div>

                            <div class="flex items-end">
                                @if(count($items) > 1)
                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-800"
                                        wire:click="removeItem({{ $index }})"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            wire:click="addEmptyItem"
                        >
                            <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add Another Product
                        </button>
                    </div>
                @endif
            </div>
        @elseif($step === 3)
            <!-- Step 3: Review & Submit -->
            <div>
                <h4 class="text-base font-medium text-gray-900 mb-4">Review Your Order</h4>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h5 class="font-medium text-gray-700 mb-2">Selected Seller</h5>
                    <div>
                        <span class="font-medium">{{ optional($sellers->firstWhere('id', $sellerId))->company_name }}</span>
                        <span class="text-gray-500 ml-2">{{ optional($sellers->firstWhere('id', $sellerId))->name }}</span>
                    </div>
                </div>

                <div class="border rounded-lg overflow-hidden mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $item)
                                @php
                                    $product = $availableProducts->firstWhere('id', $item['product_id']);
                                    if (!$product) continue;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">${{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">{{ $item['quantity'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">${{ number_format($product->price * $item['quantity'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Order Total:</th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($totalAmount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="space-y-6">
                    <div>
                        <label for="deliveryAddress" class="block text-sm font-medium text-gray-700">Delivery Address</label>
                        <textarea
                            id="deliveryAddress"
                            rows="3"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your delivery address"
                            wire:model="deliveryAddress"
                        ></textarea>
                    </div>

                    <div>
                        <label for="expectedDeliveryDate" class="block text-sm font-medium text-gray-700">Expected Delivery Date</label>
                        <input
                            type="date"
                            id="expectedDeliveryDate"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            wire:model="expectedDeliveryDate"
                        >
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Order Notes</label>
                        <textarea
                            id="notes"
                            rows="3"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Any special instructions or notes for this order"
                            wire:model="notes"
                        ></textarea>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
        @if($step > 1)
            <button
                type="button"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                wire:click="previousStep"
            >
                Back
            </button>
        @else
            <div></div>
        @endif

        @if($step < 3)
            <button
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                wire:click="nextStep"
            >
                Continue
            </button>
        @else
            <button
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                wire:click="submitOrder"
            >
                Submit Order
            </button>
        @endif
    </div>
</div>
