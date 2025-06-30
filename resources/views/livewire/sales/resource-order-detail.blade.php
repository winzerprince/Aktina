<div class="p-6 bg-white rounded-lg shadow-md">
    @if ($resourceOrder)
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Resource Order #{{ $resourceOrder->id }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('resource-orders.index') }}">
                    <x-mary-button color="blue" outline>
                        <x-mary-icon name="arrow-left" class="mr-1" /> Back to Resource Orders
                    </x-mary-button>
                </a>

                @if ($resourceOrder->status === 'pending')
                    <x-mary-button wire:click="acceptResourceOrder" wire:loading.attr="disabled" color="blue">
                        Accept Order
                    </x-mary-button>
                @endif

                @if ($resourceOrder->status === 'accepted')
                    <x-mary-button wire:click="completeResourceOrder" wire:loading.attr="disabled" color="emerald">
                        Complete Order
                    </x-mary-button>
                @endif
            </div>
        </div>

        <!-- Order Status -->
        <div class="mb-6">
            @if ($resourceOrder->status === 'pending')
                <x-mary-badge color="amber" size="lg">Pending</x-mary-badge>
            @elseif ($resourceOrder->status === 'accepted')
                <x-mary-badge color="indigo" size="lg">In Progress</x-mary-badge>
            @elseif ($resourceOrder->status === 'complete')
                <x-mary-badge color="emerald" size="lg">Completed</x-mary-badge>
            @endif
        </div>

        <!-- Order Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <x-mary-card>
                <div>
                    <h3 class="font-semibold text-lg mb-2">Order Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Date:</span> {{ $resourceOrder->created_at->format('M d, Y h:i A') }}</p>
                        <p><span class="font-medium">Total:</span> ${{ number_format($resourceOrder->price, 2) }}</p>
                        <p><span class="font-medium">Status:</span> {{ ucfirst($resourceOrder->status) }}</p>
                        <p><span class="font-medium">Items Count:</span> {{ $resourceOrder->total_items }}</p>
                    </div>
                </div>
            </x-mary-card>

            <x-mary-card>
                <div>
                    <h3 class="font-semibold text-lg mb-2">Supplier Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Name:</span> {{ $resourceOrder->seller->name ?? 'N/A' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $resourceOrder->seller->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $resourceOrder->seller->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-mary-card>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-4">Order Items</h3>
            <div class="overflow-x-auto">
                <x-mary-table>
                    <x-slot:header>
                        <x-mary-table.heading>Resource</x-mary-table.heading>
                        <x-mary-table.heading>Quantity</x-mary-table.heading>
                        <x-mary-table.heading>Price</x-mary-table.heading>
                        <x-mary-table.heading>Total</x-mary-table.heading>
                    </x-slot:header>

                    @foreach($resourceOrder->getItemsAsArray() as $item)
                        <x-mary-table.row>
                            <x-mary-table.cell>
                                @php
                                    $resource = \App\Models\Resource::find($item['resource_id'] ?? 0);
                                @endphp
                                {{ $resource ? $resource->name : 'Unknown Resource' }}
                            </x-mary-table.cell>
                            <x-mary-table.cell>{{ $item['quantity'] }}</x-mary-table.cell>
                            <x-mary-table.cell>${{ number_format($item['price'] ?? 0, 2) }}</x-mary-table.cell>
                            <x-mary-table.cell>${{ number_format(($item['quantity'] * ($item['price'] ?? 0)), 2) }}</x-mary-table.cell>
                        </x-mary-table.row>
                    @endforeach

                    <!-- Total row -->
                    <x-mary-table.row>
                        <x-mary-table.cell colspan="3" class="text-right font-bold">Total:</x-mary-table.cell>
                        <x-mary-table.cell class="font-bold">${{ number_format($resourceOrder->price, 2) }}</x-mary-table.cell>
                    </x-mary-table.row>
                </x-mary-table>
            </div>
        </div>

        <!-- Resource Stock Information -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-4">Resource Stock Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($resourceOrder->getResourcesDetails() as $resourceDetail)
                    <x-mary-card>
                        <div>
                            <h4 class="font-semibold">{{ $resourceDetail['resource']->name }}</h4>
                            <p class="text-sm text-gray-500 mb-1">{{ $resourceDetail['resource']->description }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span>Ordered Quantity: {{ $resourceDetail['quantity'] }}</span>
                                @if($resourceDetail['low_stock'])
                                    <x-mary-badge color="red">Low Stock</x-mary-badge>
                                @else
                                    <x-mary-badge color="green">In Stock</x-mary-badge>
                                @endif
                            </div>
                        </div>
                    </x-mary-card>
                @endforeach
            </div>
        </div>

        <!-- Order Timeline -->
        <div>
            <h3 class="font-semibold text-lg mb-4">Order Timeline</h3>
            <ol class="relative border-l border-gray-200 ml-4">
                <li class="mb-10 ml-6">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -left-4 ring-4 ring-white">
                        <x-mary-icon name="cube" class="w-4 h-4 text-blue-800" />
                    </span>
                    <h4 class="font-semibold text-gray-900">Order Created</h4>
                    <time class="block text-sm font-normal leading-none text-gray-500">{{ $resourceOrder->created_at->format('M d, Y h:i A') }}</time>
                    <p class="text-base font-normal text-gray-500">Resource order was placed by Aktina</p>
                </li>

                @if ($resourceOrder->status !== 'pending')
                    <li class="mb-10 ml-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full -left-4 ring-4 ring-white">
                            <x-mary-icon name="cog" class="w-4 h-4 text-indigo-800" />
                        </span>
                        <h4 class="font-semibold text-gray-900">Order Accepted</h4>
                        <time class="block text-sm font-normal leading-none text-gray-500">{{ $resourceOrder->updated_at->format('M d, Y h:i A') }}</time>
                        <p class="text-base font-normal text-gray-500">Resource order was accepted by supplier</p>
                    </li>
                @endif

                @if ($resourceOrder->status === 'complete')
                    <li class="ml-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 bg-green-100 rounded-full -left-4 ring-4 ring-white">
                            <x-mary-icon name="check" class="w-4 h-4 text-green-800" />
                        </span>
                        <h4 class="font-semibold text-gray-900">Order Completed</h4>
                        <time class="block text-sm font-normal leading-none text-gray-500">{{ $resourceOrder->updated_at->format('M d, Y h:i A') }}</time>
                        <p class="text-base font-normal text-gray-500">Resource order has been successfully completed</p>
                    </li>
                @endif
            </ol>
        </div>
    @else
        <div class="text-center py-12">
            <x-mary-icon name="exclamation-circle" class="w-16 h-16 mx-auto text-red-500" />
            <h3 class="mt-2 text-lg font-medium text-gray-900">Resource order not found</h3>
            <p class="mt-1 text-gray-500">The requested resource order does not exist.</p>
            <div class="mt-6">
                <a href="{{ route('resource-orders.index') }}">
                    <x-mary-button color="blue">
                        Back to Resource Orders
                    </x-mary-button>
                </a>
            </div>
        </div>
    @endif
</div>
