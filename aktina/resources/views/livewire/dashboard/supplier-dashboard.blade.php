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
    <div class="space-y-6">
        <div class="border-b border-zinc-200 dark:border-zinc-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button wire:click="$set('tab', 'home')"
                        class="flex items-center space-x-2 border-b-2 py-2 px-1 text-sm font-medium
                               {{ $tab === 'home' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                    <flux:icon name="home" class="h-4 w-4" />
                    <span>Tab 0: Home</span>
                </button>
                <button wire:click="$set('tab', 'orders')"
                        class="flex items-center space-x-2 border-b-2 py-2 px-1 text-sm font-medium
                               {{ $tab === 'orders' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                    <flux:icon name="shopping-bag" class="h-4 w-4" />
                    <span>Tab 1: Orders</span>
                </button>
                <button wire:click="$set('tab', 'profile')"
                        class="flex items-center space-x-2 border-b-2 py-2 px-1 text-sm font-medium
                               {{ $tab === 'profile' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                    <flux:icon name="user-circle" class="h-4 w-4" />
                    <span>Tab 2: Profile Settings</span>
                </button>
            </nav>
        </div>        @if($tab === 'home')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Orders -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Orders</flux:text>
                            <flux:heading size="xl" class="mt-1">{{ $this->orderStats['total'] }}</flux:heading>
                        </div>
                        <flux:icon name="shopping-cart" class="h-8 w-8 text-blue-500" />
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pending Orders</flux:text>
                            <flux:heading size="xl" class="mt-1">{{ $this->orderStats['pending'] }}</flux:heading>
                        </div>
                        <flux:icon name="clock" class="h-8 w-8 text-orange-500" />
                    </div>
                </div>

                <!-- On-Time Delivery -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">On-Time Delivery</flux:text>
                            <flux:heading size="xl" class="mt-1">{{ $this->orderStats['on_time_percentage'] }}%</flux:heading>
                        </div>
                        <flux:icon name="truck" class="h-8 w-8 text-green-500" />
                    </div>
                </div>
            </div>

            <!-- Order Status Overview -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 text-center">
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Confirmed</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ $this->orderStats['confirmed'] }}</flux:heading>
                </div>
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 text-center">
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">In Production</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ $this->orderStats['in_production'] }}</flux:heading>
                </div>
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 text-center">
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Shipped</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ $this->orderStats['shipped'] }}</flux:heading>
                </div>
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 text-center">
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Delivered</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ $this->orderStats['delivered'] }}</flux:heading>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">Recent Orders</flux:heading>
                    <flux:button wire:click="$set('tab', 'orders')" variant="ghost" size="sm" icon="arrow-right">View All</flux:button>
                </div>

                @if($this->recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($this->recentOrders as $order)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <flux:text class="font-medium">{{ $order->order_number }}</flux:text>
                                        <flux:badge color="{{ $order->priority_color }}" size="sm">{{ ucfirst($order->priority) }}</flux:badge>
                                    </div>
                                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->title }}</flux:text>
                                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                                        Required by: {{ $order->required_by->format('M d, Y') }}
                                    </flux:text>
                                </div>
                                <div class="text-right">
                                    <flux:badge color="{{ $order->status_color }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</flux:badge>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mt-1">
                                        ${{ number_format($order->total_amount, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <flux:icon name="inbox" class="h-12 w-12 mx-auto text-zinc-400 mb-3" />
                        <flux:text class="text-zinc-600 dark:text-zinc-400">No orders found</flux:text>
                    </div>
                @endif
            </div>
        @endif

        @if($tab === 'orders')
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg">Orders Management</flux:heading>
                    <flux:button icon="plus" variant="primary">New Order Request</flux:button>
                </div>

                <!-- Filters -->
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <flux:input
                            wire:model.live="orderSearch"
                            placeholder="Search orders..."
                            icon="magnifying-glass"
                        />
                    </div>
                    <div>
                        <flux:select wire:model.live="orderStatus">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="in_production">In Production</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </flux:select>
                    </div>
                    <flux:button wire:click="clearOrderFilters" variant="ghost" icon="x-mark">Clear</flux:button>
                </div>

                <!-- Orders Table -->
                @if($this->orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-zinc-200 dark:border-zinc-700">
                                <tr>
                                    <th class="text-left py-3 px-4">
                                        <button wire:click="updateOrderSort('order_number')" class="flex items-center space-x-1 font-medium text-zinc-900 dark:text-zinc-100">
                                            <span>Order #</span>
                                            <flux:icon name="chevron-up-down" class="h-4 w-4" />
                                        </button>
                                    </th>
                                    <th class="text-left py-3 px-4">
                                        <button wire:click="updateOrderSort('title')" class="flex items-center space-x-1 font-medium text-zinc-900 dark:text-zinc-100">
                                            <span>Title</span>
                                            <flux:icon name="chevron-up-down" class="h-4 w-4" />
                                        </button>
                                    </th>
                                    <th class="text-left py-3 px-4">Status</th>
                                    <th class="text-left py-3 px-4">Priority</th>
                                    <th class="text-left py-3 px-4">
                                        <button wire:click="updateOrderSort('total_amount')" class="flex items-center space-x-1 font-medium text-zinc-900 dark:text-zinc-100">
                                            <span>Amount</span>
                                            <flux:icon name="chevron-up-down" class="h-4 w-4" />
                                        </button>
                                    </th>
                                    <th class="text-left py-3 px-4">
                                        <button wire:click="updateOrderSort('required_by')" class="flex items-center space-x-1 font-medium text-zinc-900 dark:text-zinc-100">
                                            <span>Required By</span>
                                            <flux:icon name="chevron-up-down" class="h-4 w-4" />
                                        </button>
                                    </th>
                                    <th class="text-left py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($this->orders as $order)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <td class="py-3 px-4">
                                            <flux:text class="font-medium">{{ $order->order_number }}</flux:text>
                                        </td>
                                        <td class="py-3 px-4">
                                            <flux:text class="font-medium">{{ $order->title }}</flux:text>
                                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400 block">
                                                {{ Str::limit($order->description, 50) }}
                                            </flux:text>
                                        </td>
                                        <td class="py-3 px-4">
                                            <flux:badge color="{{ $order->status_color }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </flux:badge>
                                        </td>
                                        <td class="py-3 px-4">
                                            <flux:badge color="{{ $order->priority_color }}" size="sm">
                                                {{ ucfirst($order->priority) }}
                                            </flux:badge>
                                        </td>
                                        <td class="py-3 px-4">
                                            <flux:text class="font-medium">${{ number_format($order->total_amount, 2) }}</flux:text>
                                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400 block">
                                                {{ $order->quantity }} {{ $order->unit }}
                                            </flux:text>
                                        </td>
                                        <td class="py-3 px-4">
                                            <flux:text class="text-sm">{{ $order->required_by->format('M d, Y') }}</flux:text>
                                            @if($order->delivery_date)
                                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-500 block">
                                                    Delivered: {{ $order->delivery_date->format('M d, Y') }}
                                                </flux:text>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center space-x-2">
                                                <flux:button size="sm" variant="ghost" icon="eye">View</flux:button>
                                                @if($order->status === 'pending')
                                                    <flux:button size="sm" variant="ghost" icon="pencil">Edit</flux:button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $this->orders->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <flux:icon name="inbox" class="h-16 w-16 mx-auto text-zinc-400 mb-4" />
                        <flux:heading size="lg" class="mb-2">No orders found</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400 mb-4">
                            @if($orderSearch || $orderStatus)
                                No orders match your current filters.
                            @else
                                You haven't placed any orders yet.
                            @endif
                        </flux:text>
                        @if($orderSearch || $orderStatus)
                            <flux:button wire:click="clearOrderFilters" variant="ghost">Clear Filters</flux:button>
                        @else
                            <flux:button icon="plus" variant="primary">Create Your First Order</flux:button>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if($tab === 'profile')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Company Profile -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                    <flux:heading size="lg" class="mb-6">Company Profile</flux:heading>

                    <form wire:submit="updateProfile" class="space-y-4">
                        <div>
                            <flux:label for="companyName">Company Name</flux:label>
                            <flux:input wire:model="companyName" id="companyName" required />
                            @error('companyName') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                        </div>

                        <div>
                            <flux:label for="contactPerson">Contact Person</flux:label>
                            <flux:input wire:model="contactPerson" id="contactPerson" />
                            @error('contactPerson') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                        </div>

                        <div>
                            <flux:label for="email">Email Address</flux:label>
                            <flux:input wire:model="email" type="email" id="email" required />
                            @error('email') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                        </div>

                        <div>
                            <flux:label for="phone">Phone Number</flux:label>
                            <flux:input wire:model="phone" id="phone" />
                            @error('phone') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                        </div>

                        <div>
                            <flux:label for="address">Business Address</flux:label>
                            <flux:textarea wire:model="address" id="address" rows="3" />
                            @error('address') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                        </div>

                        <div class="flex justify-end">
                            <flux:button type="submit" variant="primary">Update Profile</flux:button>
                        </div>
                    </form>

                    @if (session()->has('message'))
                        <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <flux:text class="text-green-800 dark:text-green-200">
                                {{ session('message') }}
                            </flux:text>
                        </div>
                    @endif
                </div>

                <!-- Notification Preferences -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                    <flux:heading size="lg" class="mb-6">Notification Preferences</flux:heading>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="font-medium">Email Notifications</flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Receive order updates via email
                                </flux:text>
                            </div>
                            <flux:switch wire:model="emailNotifications" />
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="font-medium">SMS Notifications</flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Receive urgent updates via SMS
                                </flux:text>
                            </div>
                            <flux:switch wire:model="smsNotifications" />
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:heading size="md" class="mb-4">Account Statistics</flux:heading>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Member Since</flux:text>
                                <flux:text class="font-medium">{{ auth()->user()->created_at->format('M Y') }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Total Orders</flux:text>
                                <flux:text class="font-medium">{{ $this->orderStats['total'] }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Completed Orders</flux:text>
                                <flux:text class="font-medium">{{ $this->orderStats['delivered'] }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">On-Time Rate</flux:text>
                                <flux:text class="font-medium">{{ $this->orderStats['on_time_percentage'] }}%</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 lg:col-span-2">
                    <flux:heading size="lg" class="mb-6">Quick Actions</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:button variant="outline" class="w-full justify-start" icon="document-text">
                            Download Order History
                        </flux:button>
                        <flux:button variant="outline" class="w-full justify-start" icon="envelope">
                            Contact Support
                        </flux:button>
                        <flux:button variant="outline" class="w-full justify-start" icon="cog">
                            Account Settings
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
