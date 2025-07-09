<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Order Fulfillment - #{{ $order->id }}
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Follow the steps to update order status
        </p>
    </div>

    <!-- Error Message -->
    @if($errorMessage)
    <div class="mx-6 my-4 bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    {{ $errorMessage }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Success Message -->
    @if($successMessage)
    <div class="mx-6 my-4 bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    {{ $successMessage }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Progress Bar -->
    <div class="px-6 pt-4">
        <div class="flex items-center">
            @for($i = 1; $i <= $maxSteps; $i++)
                <div class="flex-1 {{ $i < $maxSteps ? 'border-b-2 border-blue-600' : '' }} pb-1">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full {{ $i <= $step ? 'bg-blue-600' : 'bg-gray-200' }} flex items-center justify-center text-white">
                            @if($i < $step)
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                {{ $i }}
                            @endif
                        </div>
                        <span class="ml-2 text-sm {{ $i <= $step ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                            @if($i === 1)
                                Select Status
                            @elseif($i === 2)
                                Fulfillment Details
                            @else
                                Confirm & Complete
                            @endif
                        </span>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Step Content -->
    <div class="px-6 py-6">
        @if($step === 1)
            <!-- Step 1: Select Status -->
            <div class="space-y-6">
                <div>
                    <label for="currentStatus" class="block text-sm font-medium text-gray-700">Current Status</label>
                    <div class="mt-1">
                        <div class="py-2 px-3 border border-gray-300 bg-gray-50 rounded-md text-gray-700">
                            {{ $this->getStatusLabel($currentStatus) }}
                        </div>
                    </div>
                </div>

                <div>
                    <label for="nextStatus" class="block text-sm font-medium text-gray-700">Update Status To</label>
                    <div class="mt-1">
                        <select id="nextStatus"
                                wire:model="nextStatus"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Select a status...</option>
                            @foreach($validNextStatuses as $status => $label)
                                <option value="{{ $status }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @elseif($step === 2)
            <!-- Step 2: Fulfillment Details -->
            <div class="space-y-6">
                @switch($nextStatus)
                    @case('shipped')
                        <div>
                            <label for="trackingNumber" class="block text-sm font-medium text-gray-700">Tracking Number</label>
                            <div class="mt-1">
                                <input type="text"
                                       id="trackingNumber"
                                       wire:model="trackingNumber"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label for="shippingProvider" class="block text-sm font-medium text-gray-700">Shipping Provider</label>
                            <div class="mt-1">
                                <select id="shippingProvider"
                                        wire:model="shippingProvider"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select a provider...</option>
                                    @foreach($shippingProviders as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @break

                    @case('fulfillment_failed')
                        <div>
                            <label for="fulfillmentNote" class="block text-sm font-medium text-gray-700">Failure Reason</label>
                            <div class="mt-1">
                                <textarea id="fulfillmentNote"
                                          wire:model="fulfillmentNote"
                                          rows="4"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                        @break

                    @case('partially_fulfilled')
                        <div>
                            <label for="partialFulfillmentDetails" class="block text-sm font-medium text-gray-700">Partial Fulfillment Details</label>
                            <div class="mt-1">
                                <textarea id="partialFulfillmentDetails"
                                          wire:model="partialFulfillmentDetails"
                                          rows="4"
                                          placeholder="Specify which items are fulfilled and which are backordered or delayed"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                        @break

                    @case('returned')
                        <div>
                            <label for="returnReason" class="block text-sm font-medium text-gray-700">Return Reason</label>
                            <div class="mt-1">
                                <select id="returnReason"
                                        wire:model="returnReason"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select a reason...</option>
                                    @foreach($returnReasons as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @break

                    @default
                        <div class="py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                            <p class="text-sm text-gray-700">
                                No additional information required for this status update.
                            </p>
                        </div>
                @endswitch
            </div>
        @elseif($step === 3)
            <!-- Step 3: Confirm & Complete -->
            <div class="space-y-6">
                <div class="bg-gray-50 p-4 rounded-md">
                    <h4 class="text-base font-medium text-gray-900 mb-3">Order Summary</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Order ID</p>
                            <p class="text-sm font-medium text-gray-900">#{{ $order->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Customer</p>
                            <p class="text-sm font-medium text-gray-900">{{ optional($order->buyer)->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Current Status</p>
                            <p class="text-sm font-medium text-gray-900">{{ $this->getStatusLabel($currentStatus) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">New Status</p>
                            <p class="text-sm font-medium text-gray-900">{{ $this->getStatusLabel($nextStatus) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Show specific details based on status -->
                @switch($nextStatus)
                    @case('shipped')
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-base font-medium text-gray-900 mb-3">Shipping Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Tracking Number</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $trackingNumber }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Shipping Provider</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $shippingProviders[$shippingProvider] ?? $shippingProvider }}</p>
                                </div>
                            </div>
                        </div>
                        @break

                    @case('partially_fulfilled')
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-base font-medium text-gray-900 mb-3">Partial Fulfillment Details</h4>
                            <div>
                                <p class="text-sm text-gray-500">Details</p>
                                <p class="text-sm font-medium text-gray-900 whitespace-pre-line">{{ $partialFulfillmentDetails }}</p>
                            </div>
                        </div>
                        @break

                    @case('returned')
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-base font-medium text-gray-900 mb-3">Return Details</h4>
                            <div>
                                <p class="text-sm text-gray-500">Return Reason</p>
                                <p class="text-sm font-medium text-gray-900">{{ $returnReasons[$returnReason] ?? $returnReason }}</p>
                            </div>
                        </div>
                        @break

                    @case('fulfillment_failed')
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-base font-medium text-gray-900 mb-3">Fulfillment Failure Details</h4>
                            <div>
                                <p class="text-sm text-gray-500">Failure Reason</p>
                                <p class="text-sm font-medium text-gray-900 whitespace-pre-line">{{ $fulfillmentNote }}</p>
                            </div>
                        </div>
                        @break
                @endswitch

                <div class="bg-yellow-50 p-4 rounded-md border-l-4 border-yellow-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This action will update the order status and cannot be easily reversed.
                                Please confirm that all details are correct before proceeding.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Step Navigation -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
        <button type="button"
                wire:click="previousStep"
                @if($step === 1) disabled @endif
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ $step === 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
            Previous
        </button>
        <button type="button"
                wire:click="{{ $step < $maxSteps ? 'nextStep' : 'completeProcess' }}"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ $step < $maxSteps ? 'Continue' : 'Complete' }}
        </button>
    </div>
</div>
