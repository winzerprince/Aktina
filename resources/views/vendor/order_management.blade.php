<x-layouts.app>
    <x-slot:title>{{ __('Order Management') }}</x-slot:title>

    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Order Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage and track your orders with retailers</p>
            </div>
            <x-ui.button variant="primary" icon="plus" class="cursor-pointer">
                New Order
            </x-ui.button>
        </div>

        <!-- Basic Stats Cards -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Active Orders"
                    value="24"
                    change="12%"
                    changeType="positive"
                    icon="shopping-bag"
                    iconBg="primary"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Pending Approval"
                    value="8"
                    change="5%"
                    changeType="positive"
                    icon="clock"
                    iconBg="warning"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Completed Today"
                    value="16"
                    change="23%"
                    changeType="positive"
                    icon="check-circle"
                    iconBg="success"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Revenue Today"
                    value="$12,450"
                    change="18%"
                    changeType="positive"
                    icon="currency-dollar"
                    iconBg="success"
                />
            </div>
        </div>

        <!-- Simple Orders Table -->
        <div class="w-full mb-6">
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Orders</h3>
                    <x-ui.data-table
                        :headers="['Order ID', 'Retailer', 'Items', 'Status', 'Total']"
                        :rows="[
                            ['#VND-001', 'Tech Store Pro', '15 items', ['type' => 'status', 'status' => 'pending'], '$2,150'],
                            ['#VND-002', 'Digital Hub', '8 items', ['type' => 'status', 'status' => 'completed'], '$890'],
                            ['#VND-003', 'Smart Electronics', '22 items', ['type' => 'status', 'status' => 'processing'], '$3,200'],
                            ['#VND-004', 'Tech World', '12 items', ['type' => 'status', 'status' => 'shipped'], '$1,680'],
                            ['#VND-005', 'Modern Store', '5 items', ['type' => 'status', 'status' => 'pending'], '$750']
                        ]"
                    />
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
