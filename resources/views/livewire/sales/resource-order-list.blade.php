<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Resource Orders Management</h2>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-mary-stat title="Total Resource Orders" value="{{ $statistics['total_orders'] }}" color="blue">
            <x-slot:icon>
                <x-mary-icon name="cube" />
            </x-slot:icon>
        </x-mary-stat>

        <x-mary-stat title="Pending Orders" value="{{ $statistics['pending_orders'] }}" color="amber">
            <x-slot:icon>
                <x-mary-icon name="clock" />
            </x-slot:icon>
        </x-mary-stat>

        <x-mary-stat title="In Progress" value="{{ $statistics['accepted_orders'] }}" color="indigo">
            <x-slot:icon>
                <x-mary-icon name="cog" />
            </x-slot:icon>
        </x-mary-stat>

        <x-mary-stat title="Completed Orders" value="{{ $statistics['completed_orders'] }}" color="emerald">
            <x-slot:icon>
                <x-mary-icon name="check-circle" />
            </x-slot:icon>
        </x-mary-stat>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col lg:flex-row gap-4">
        <div class="flex-1">
            <x-mary-input placeholder="Search by ID, buyer, or seller..." wire:model.live.debounce.500ms="searchTerm">
                <x-slot:prepend>
                    <x-mary-icon name="magnifying-glass" />
                </x-slot:prepend>
            </x-mary-input>
        </div>

        <div class="flex flex-wrap gap-2">
            <x-mary-select wire:model.live="statusFilter">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="complete">Complete</option>
            </x-mary-select>

            <x-mary-select wire:model.live="dateFilter">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="custom">Custom Range</option>
            </x-mary-select>

            <x-mary-button color="blue" wire:click="resetFilters" outline>
                Reset Filters
            </x-mary-button>
        </div>
    </div>

    <!-- Custom Date Range -->
    @if ($dateFilter === 'custom')
        <div class="mb-6 flex flex-wrap gap-4">
            <div>
                <x-mary-label>Start Date</x-mary-label>
                <x-mary-input type="date" wire:model.live="startDate" />
            </div>

            <div>
                <x-mary-label>End Date</x-mary-label>
                <x-mary-input type="date" wire:model.live="endDate" />
            </div>
        </div>
    @endif

    <!-- Resource Orders Table -->
    <div class="overflow-x-auto">
        <x-mary-table striped>
            <x-slot:header>
                <x-mary-table.heading>Order ID</x-mary-table.heading>
                <x-mary-table.heading>Supplier</x-mary-table.heading>
                <x-mary-table.heading>Items</x-mary-table.heading>
                <x-mary-table.heading>Status</x-mary-table.heading>
                <x-mary-table.heading>Total</x-mary-table.heading>
                <x-mary-table.heading>Date</x-mary-table.heading>
                <x-mary-table.heading>Actions</x-mary-table.heading>
            </x-slot:header>

            @forelse ($resourceOrders as $resourceOrder)
                <x-mary-table.row>
                    <x-mary-table.cell>
                        <a href="{{ route('resource-orders.show', $resourceOrder->id) }}" class="font-medium text-blue-600 hover:underline">
                            #{{ $resourceOrder->id }}
                        </a>
                    </x-mary-table.cell>
                    <x-mary-table.cell>
                        {{ $resourceOrder->seller->name ?? 'Unknown' }}
                    </x-mary-table.cell>
                    <x-mary-table.cell>
                        <x-mary-badge color="blue">
                            {{ $resourceOrder->items_count }} items
                        </x-mary-badge>
                    </x-mary-table.cell>
                    <x-mary-table.cell>
                        @if ($resourceOrder->status === 'pending')
                            <x-mary-badge color="amber">Pending</x-mary-badge>
                        @elseif ($resourceOrder->status === 'accepted')
                            <x-mary-badge color="indigo">In Progress</x-mary-badge>
                        @elseif ($resourceOrder->status === 'complete')
                            <x-mary-badge color="emerald">Completed</x-mary-badge>
                        @endif
                    </x-mary-table.cell>
                    <x-mary-table.cell>
                        <span class="font-semibold">
                            ${{ number_format($resourceOrder->price, 2) }}
                        </span>
                    </x-mary-table.cell>
                    <x-mary-table.cell>
                        {{ $resourceOrder->created_at->format('M d, Y') }}
                    </x-mary-table.cell>
                    <x-mary-table.cell>
                        <div class="flex gap-2">
                            @if ($resourceOrder->status === 'pending')
                                <x-mary-button wire:click="accept({{ $resourceOrder->id }})" wire:loading.attr="disabled" size="sm" color="blue">
                                    Accept
                                </x-mary-button>
                            @endif

                            @if ($resourceOrder->status === 'accepted')
                                <x-mary-button wire:click="complete({{ $resourceOrder->id }})" wire:loading.attr="disabled" size="sm" color="emerald">
                                    Complete
                                </x-mary-button>
                            @endif

                            <a href="{{ route('resource-orders.show', $resourceOrder->id) }}">
                                <x-mary-button size="sm" outline>
                                    View
                                </x-mary-button>
                            </a>
                        </div>
                    </x-mary-table.cell>
                </x-mary-table.row>
            @empty
                <x-mary-table.row>
                    <x-mary-table.cell colspan="7" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <x-mary-icon name="cube" class="w-12 h-12 text-gray-400" />
                            <p class="text-gray-500 text-lg">No resource orders found</p>
                            <p class="text-gray-400 text-sm">Try adjusting your search criteria</p>
                        </div>
                    </x-mary-table.cell>
                </x-mary-table.row>
            @endforelse
        </x-mary-table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $resourceOrders->links() }}
    </div>
</div>
