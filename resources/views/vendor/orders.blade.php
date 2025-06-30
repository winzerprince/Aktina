<x-layouts.app>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">My Orders</h1>
            <a href="{{ route('orders.create') }}">
                <x-button color="blue">
                    <x-icon name="o-plus" class="mr-1" /> Place New Order
                </x-button>
            </a>
        </div>

        <livewire:sales.order-list />
    </div>
</x-layouts.app>
