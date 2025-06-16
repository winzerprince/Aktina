<x-layouts.dashboard>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Wholesaler Dashboard</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    Welcome, {{ auth()->user()->name }}! Manage distribution operations here.
                </flux:text>
            </div>
            <flux:badge color="cyan" variant="solid">
                {{ auth()->user()->getRoleDisplayName() }}
            </flux:badge>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
            <flux:heading size="lg" class="mb-4">Distribution Dashboard Tabs (Coming Soon)</flux:heading>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-center">
                    <flux:icon name="home" class="h-8 w-8 mx-auto mb-2 text-blue-500" />
                    <flux:text class="font-medium">Tab 0: Home</flux:text>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-center">
                    <flux:icon name="shopping-bag" class="h-8 w-8 mx-auto mb-2 text-green-500" />
                    <flux:text class="font-medium">Tab 1: Orders</flux:text>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-center">
                    <flux:icon name="building-storefront" class="h-8 w-8 mx-auto mb-2 text-purple-500" />
                    <flux:text class="font-medium">Tab 2: Retailers</flux:text>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-center">
                    <flux:icon name="chart-bar" class="h-8 w-8 mx-auto mb-2 text-orange-500" />
                    <flux:text class="font-medium">Tab 3: Analytics</flux:text>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-center">
                    <flux:icon name="envelope" class="h-8 w-8 mx-auto mb-2 text-indigo-500" />
                    <flux:text class="font-medium">Tab 4: Communications</flux:text>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
