<x-layouts.app>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Order Management</h1>
            <a href="{{ route('orders.create') }}">
                <x-mary-button color="blue">
                    <x-mary-icon name="plus" class="mr-1" /> Create New Order
                </x-mary-button>
            </a>
        </div>

        <livewire:sales.order-list />

        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-3">Resource Orders</h2>
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-600">Manage orders for raw materials and resources</p>
                <a href="{{ route('resource-orders.create') }}">
                    <x-mary-button color="indigo">
                        <x-mary-icon name="plus" class="mr-1" /> Create Resource Order
                    </x-mary-button>
                </a>
            </div>

            <livewire:sales.resource-order-list />
        </div>
    </div>
</x-layouts.app>
