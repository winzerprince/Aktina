<x-layouts.app>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Order Management</h1>
            <div class="flex space-x-2">
                <a href="{{ route('orders.create') }}">
                    <x-mary-button color="blue">
                        <x-mary-icon name="plus" class="mr-1" /> Create New Order
                    </x-mary-button>
                </a>
                <a href="{{ route('resource-orders.create') }}">
                    <x-mary-button color="indigo">
                        <x-mary-icon name="plus" class="mr-1" /> Create Resource Order
                    </x-mary-button>
                </a>
            </div>
        </div>

        <!-- Product Orders Section -->
        <div>
            <h2 class="text-xl font-semibold mb-3">Product Orders</h2>
            <livewire:sales.order-list />
        </div>

        <!-- Resource Orders Section -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-3">Resource Orders</h2>
            <livewire:sales.resource-order-list />
        </div>
    </div>
</x-layouts.app>
