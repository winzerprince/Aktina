<x-layouts.app :title="__('Order management')">
    <div class="w-full px-6 py-6 mx-auto">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Order management</h1>
            <p class="text-gray-600 dark:text-gray-400">Statistics for both pending orders and completed orders</p>
        </div>
        <x-ui.button variant="primary" icon="plus" class="cursor-pointer">
            Add Item
        </x-ui.button>
    </div>
    <div>
        <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
            <x-ui.stats-card
                title="New Orders"
                value="1,462"
                change=""
                changeType="negative"
                icon="paper-diploma"
                iconBg="warning"
            />
        </div>
         <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
            <x-ui.stats-card
                title="Total Sales"
                value="UGX 1,103,430"
                change=""
                changeType="positive"
                icon="cart"
                iconBg="success"
            />
        </div>
</div>
    <!--<h1 class="text-2xl font-bold mb-4">Order Management</h1>
    <p>This is the Order Management page for Production Managers.</p>-->
</x-layouts.app>
