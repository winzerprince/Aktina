<x-layouts.app :title="_('Orders')">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Order Management</h1>
            <a href="{{ route('orders.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create New Order
            </a>
        </div>

        <livewire:sales.order-list />

        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-3">Resource Orders</h2>
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-600">Manage orders for raw materials and resources</p>
                <a href="{{ route('resource-orders.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Resource Order
                </a>
            </div>

            <livewire:sales.resource-order-list />
        </div>
    </div>
</x-layouts.app>
