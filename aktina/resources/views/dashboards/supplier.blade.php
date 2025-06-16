<x-layouts.dashboard>
    <div class="space-y-6">
        <!-- Welcome Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">Welcome back, {{ auth()->user()->name }}!</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    Here's what's happening with your supplier account today.
                </flux:text>
            </div>
            <flux:badge color="green" variant="solid">
                {{ auth()->user()->getRoleDisplayName() }}
            </flux:badge>
        </div>

        <!-- Tab Navigation -->
        <flux:tab.group>
            <flux:tabs wire:model="tab">
                <flux:tab name="home" icon="home">Tab 0: Home</flux:tab>
                <flux:tab name="orders" icon="shopping-bag">Tab 1: Orders</flux:tab>
                <flux:tab name="profile" icon="user-circle">Tab 2: Profile Settings</flux:tab>
            </flux:tabs>

            <flux:tab.panel name="home" class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Order Statistics -->
                    <flux:card>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Orders</flux:text>
                                <flux:heading size="xl" class="mt-1">127</flux:heading>
                            </div>
                            <flux:icon name="shopping-cart" class="h-8 w-8 text-blue-500" />
                        </div>
                    </flux:card>

                    <!-- Pending Orders -->
                    <flux:card>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pending Orders</flux:text>
                                <flux:heading size="xl" class="mt-1">8</flux:heading>
                            </div>
                            <flux:icon name="clock" class="h-8 w-8 text-orange-500" />
                        </div>
                    </flux:card>

                    <!-- Delivery Performance -->
                    <flux:card>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">On-Time Delivery</flux:text>
                                <flux:heading size="xl" class="mt-1">94%</flux:heading>
                            </div>
                            <flux:icon name="truck" class="h-8 w-8 text-green-500" />
                        </div>
                    </flux:card>
                </div>

                <!-- Recent Orders -->
                <flux:card class="mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg">Recent Orders</flux:heading>
                        <flux:button variant="ghost" size="sm" icon="arrow-right">View All</flux:button>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                            <div>
                                <flux:text class="font-medium">Order #AKT-2025-001</flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Raw materials for production line A</flux:text>
                            </div>
                            <flux:badge color="yellow">Pending</flux:badge>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                            <div>
                                <flux:text class="font-medium">Order #AKT-2025-002</flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Component supplies for line B</flux:text>
                            </div>
                            <flux:badge color="green">Delivered</flux:badge>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                            <div>
                                <flux:text class="font-medium">Order #AKT-2025-003</flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Packaging materials</flux:text>
                            </div>
                            <flux:badge color="blue">In Transit</flux:badge>
                        </div>
                    </div>
                </flux:card>
            </flux:tab.panel>

            <flux:tab.panel name="orders" class="mt-6">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Orders Management</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                        Complete order management interface will be implemented here. This will include:
                    </flux:text>
                    <ul class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <li>• Active orders requiring attention</li>
                        <li>• Pending order requests from Aktina</li>
                        <li>• Complete order history with search and filtering</li>
                        <li>• Order status update capabilities</li>
                    </ul>
                </flux:card>
            </flux:tab.panel>

            <flux:tab.panel name="profile" class="mt-6">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Profile Settings</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                        Profile management interface will be implemented here. This will include:
                    </flux:text>
                    <ul class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <li>• Company profile information</li>
                        <li>• Contact details management</li>
                        <li>• Password reset functionality</li>
                        <li>• Notification preferences</li>
                    </ul>
                </flux:card>
            </flux:tab.panel>
        </flux:tab.group>
    </div>
</x-layouts.dashboard>
