<x-layouts.app>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">My Orders</h1>
            <a href="{{ route('orders.create') }}">
                <x-mary-button color="blue">
                    <x-mary-icon name="plus" class="mr-1" /> Place New Order
                </x-mary-button>
            </a>
        </div>

        <livewire:sales.order-list />
    </div>
</x-layouts.app>
