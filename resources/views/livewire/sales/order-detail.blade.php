<div class="p-6 bg-white rounded-lg shadow-md">
    @if ($order)
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Order #{{ $order->id }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('orders.index') }}">
                    <x-mary-button color="blue" outline>
                        <x-mary-icon name="arrow-left" class="mr-1" /> Back to Orders
                    </x-mary-button>
                </a>

                @if ($order->status === 'pending')
                    <x-mary-button wire:click="acceptOrder" wire:loading.attr="disabled" color="blue">
                        Accept Order
                    </x-mary-button>
                @endif

                @if ($order->status === 'accepted')
                    <x-mary-button wire:click="completeOrder" wire:loading.attr="disabled" color="emerald">
                        Complete Order
                    </x-mary-button>
                @endif
            </div>
        </div>

        <!-- Order Status -->
        <div class="mb-6">
            @if ($order->status === 'pending')
                <x-mary-badge color="amber" size="lg">Pending</x-mary-badge>
            @elseif ($order->status === 'accepted')
                <x-mary-badge color="indigo" size="lg">In Progress</x-mary-badge>
            @elseif ($order->status === 'complete')
                <x-mary-badge color="emerald" size="lg">Completed</x-mary-badge>
            @endif
        </div>

        <!-- Order Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <x-mary-card>
                <div>
                    <h3 class="font-semibold text-lg mb-2">Order Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Date:</span> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                        <p><span class="font-medium">Total:</span> ${{ number_format($order->price, 2) }}</p>
                        <p><span class="font-medium">Status:</span> {{ ucfirst($order->status) }}</p>
                        <p><span class="font-medium">Items Count:</span> {{ $order->total_items }}</p>
                    </div>
                </div>
            </x-mary-card>

            <x-mary-card>
                <div>
                    <h3 class="font-semibold text-lg mb-2">Customer Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Name:</span> {{ $order->buyer->name ?? 'N/A' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->buyer->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->buyer->phone ?? 'N/A' }}</p>
                        <p><span class="font-medium">Type:</span> {{ $order->buyer->role ?? 'N/A' }}</p>
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
                        <x-mary-table.heading>Product</x-mary-table.heading>
                        <x-mary-table.heading>Quantity</x-mary-table.heading>
                        <x-mary-table.heading>Price</x-mary-table.heading>
                        <x-mary-table.heading>Total</x-mary-table.heading>
                    </x-slot:header>

                    @foreach($order->getItemsAsArray() as $item)
                        <x-mary-table.row>
                            <x-mary-table.cell>
                                @php
                                    $product = \App\Models\Product::find($item['product_id'] ?? 0);
                                @endphp
                                {{ $product ? $product->name : 'Unknown Product' }}
                            </x-mary-table.cell>
                            <x-mary-table.cell>{{ $item['quantity'] }}</x-mary-table.cell>
                            <x-mary-table.cell>${{ number_format($item['price'] ?? 0, 2) }}</x-mary-table.cell>
                            <x-mary-table.cell>${{ number_format(($item['quantity'] * ($item['price'] ?? 0)), 2) }}</x-mary-table.cell>
                        </x-mary-table.row>
                    @endforeach

                    <!-- Total row -->
                    <x-mary-table.row>
                        <x-mary-table.cell colspan="3" class="text-right font-bold">Total:</x-mary-table.cell>
                        <x-mary-table.cell class="font-bold">${{ number_format($order->price, 2) }}</x-mary-table.cell>
                    </x-mary-table.row>
                </x-mary-table>
            </div>
        </div>

        <!-- Employees Section -->
        @if ($order->status === 'accepted' || $order->status === 'complete')
            <div class="mb-6">
                <h3 class="font-semibold text-lg mb-4">Assigned Employees</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($assignedEmployees as $employee)
                        <x-mary-card>
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <x-mary-icon name="user" class="w-6 h-6 text-blue-500" />
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium">{{ $employee['name'] }}</h4>
                                    <p class="text-sm text-gray-500">{{ $employee['position'] }}</p>
                                </div>
                            </div>
                        </x-mary-card>
                    @empty
                        <div class="col-span-full">
                            <x-mary-empty-state
                                title="No Employees Assigned"
                                icon="user-group"
                                description="There are currently no employees assigned to this order." />
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Employee Assignment -->
        @if ($order->status === 'pending')
            <div class="mb-6">
                <h3 class="font-semibold text-lg mb-4">Assign Employees</h3>

                @if (count($availableEmployees) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        @foreach ($availableEmployees as $index => $employee)
                            <div>
                                <x-mary-checkbox
                                    id="employee-{{ $employee['id'] }}"
                                    wire:model.live="selectedEmployees"
                                    value="{{ $employee['id'] }}"
                                    label="{{ $employee['name'] }} - {{ $employee['position'] }}" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-mary-alert title="No Employees Available" color="amber">
                        There are no available employees to assign to this order.
                        <p class="text-sm">All employees are currently occupied with other tasks.</p>
                    </x-mary-alert>
                @endif
            </div>
        @endif

        <!-- Order Timeline -->
        <div>
            <h3 class="font-semibold text-lg mb-4">Order Timeline</h3>
            <ol class="relative border-l border-gray-200 ml-4">
                <li class="mb-10 ml-6">
                    <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -left-4 ring-4 ring-white">
                        <x-mary-icon name="shopping-cart" class="w-4 h-4 text-blue-800" />
                    </span>
                    <h4 class="font-semibold text-gray-900">Order Created</h4>
                    <time class="block text-sm font-normal leading-none text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</time>
                    <p class="text-base font-normal text-gray-500">Order was placed by {{ $order->buyer->name ?? 'Unknown' }}</p>
                </li>

                @if ($order->status !== 'pending')
                    <li class="mb-10 ml-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full -left-4 ring-4 ring-white">
                            <x-mary-icon name="cog" class="w-4 h-4 text-indigo-800" />
                        </span>
                        <h4 class="font-semibold text-gray-900">Order Accepted</h4>
                        <time class="block text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</time>
                        <p class="text-base font-normal text-gray-500">Order was accepted and processing started</p>
                    </li>
                @endif

                @if ($order->status === 'complete')
                    <li class="ml-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 bg-green-100 rounded-full -left-4 ring-4 ring-white">
                            <x-mary-icon name="check" class="w-4 h-4 text-green-800" />
                        </span>
                        <h4 class="font-semibold text-gray-900">Order Completed</h4>
                        <time class="block text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</time>
                        <p class="text-base font-normal text-gray-500">Order has been successfully completed</p>
                    </li>
                @endif
            </ol>
        </div>
    @else
        <div class="text-center py-12">
            <x-mary-icon name="exclamation-circle" class="w-16 h-16 mx-auto text-red-500" />
            <h3 class="mt-2 text-lg font-medium text-gray-900">Order not found</h3>
            <p class="mt-1 text-gray-500">The requested order does not exist.</p>
            <div class="mt-6">
                <a href="{{ route('orders.index') }}">
                    <x-mary-button color="blue">
                        Back to Orders
                    </x-mary-button>
                </a>
            </div>
        </div>
    @endif
</div>
