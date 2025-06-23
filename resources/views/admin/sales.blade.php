<x-layouts.app>
    <x-slot:title>{{ __('Sales Management') }}</x-slot:title>

    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Sales Management') }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ __('Track orders, revenue, and sales performance') }}</p>
        </div>

        <!-- Filters and Search -->
        <div class="mb-6">
            <x-ui.card class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button variant="primary" size="sm">{{ __('All Orders') }}</x-ui.button>
                        <x-ui.button variant="outline" size="sm">{{ __('Pending') }}</x-ui.button>
                        <x-ui.button variant="outline" size="sm">{{ __('Completed') }}</x-ui.button>
                        <x-ui.button variant="outline" size="sm">{{ __('Failed') }}</x-ui.button>
                    </div>
                    <div class="flex gap-2">
                        <x-ui.form-input type="search" placeholder="{{ __('Search orders...') }}" class="min-w-64" />
                        <x-ui.button variant="outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                            </svg>
                            {{ __('Filter') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-ui.metric-card
                title="{{ __('Total Revenue') }}"
                value="$125,430"
                change="+15.3%"
                change-type="positive"
                icon="currency-dollar"
                description="{{ __('This month') }}"
            />

            <x-ui.metric-card
                title="{{ __('Total Profit') }}"
                value="$32,180"
                change="+12.8%"
                change-type="positive"
                icon="chart-pie"
                description="{{ __('Net profit margin') }}"
            />

            <x-ui.metric-card
                title="{{ __('Orders') }}"
                value="847"
                change="+9.2%"
                change-type="positive"
                icon="shopping-bag"
                description="{{ __('This month') }}"
            />

            <x-ui.metric-card
                title="{{ __('Avg Order Value') }}"
                value="$148.12"
                change="+5.7%"
                change-type="positive"
                icon="chart-bar"
                description="{{ __('Per order') }}"
            />
        </div>

        <!-- Recent Orders Table -->
        <x-ui.card>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Orders') }}</h3>
                    <div class="flex gap-2">
                        <x-ui.button variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ __('Export') }}
                        </x-ui.button>
                        <x-ui.button variant="primary" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('New Order') }}
                        </x-ui.button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Order ID') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Customer') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Products') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Revenue') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Profit') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $orders = [
                                    ['id' => 1001, 'customer' => 'Acme Corp', 'products' => 'Widget A, Widget B', 'revenue' => 1248.50, 'profit' => 312.12, 'status' => 'completed', 'date' => '2024-01-15'],
                                    ['id' => 1002, 'customer' => 'Tech Solutions Ltd', 'products' => 'Service Package', 'revenue' => 850.00, 'profit' => 255.00, 'status' => 'pending', 'date' => '2024-01-14'],
                                    ['id' => 1003, 'customer' => 'Global Industries', 'products' => 'Product X, Product Y, Product Z', 'revenue' => 2340.75, 'profit' => 702.22, 'status' => 'completed', 'date' => '2024-01-14'],
                                    ['id' => 1004, 'customer' => 'StartUp Inc', 'products' => 'Consulting Service', 'revenue' => 450.00, 'profit' => 180.00, 'status' => 'failed', 'date' => '2024-01-13'],
                                    ['id' => 1005, 'customer' => 'Enterprise Co', 'products' => 'Enterprise Solution', 'revenue' => 3200.00, 'profit' => 960.00, 'status' => 'pending', 'date' => '2024-01-13'],
                                ];
                            @endphp

                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        #{{ $order['id'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white font-medium">{{ $order['customer'] }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $order['products'] }}">
                                            {{ $order['products'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        ${{ number_format($order['revenue'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400 font-medium">
                                        ${{ number_format($order['profit'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-ui.status-badge :status="$order['status']" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ date('M j, Y', strtotime($order['date'])) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ __('View') }}
                                            </button>
                                            <button class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                {{ __('Edit') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between px-6 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <x-ui.button variant="outline" size="sm">{{ __('Previous') }}</x-ui.button>
                        <x-ui.button variant="outline" size="sm">{{ __('Next') }}</x-ui.button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('Showing') }} <span class="font-medium">1</span> {{ __('to') }} <span class="font-medium">5</span> {{ __('of') }} <span class="font-medium">97</span> {{ __('results') }}
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <x-ui.button variant="outline" size="sm">{{ __('Previous') }}</x-ui.button>
                                <x-ui.button variant="primary" size="sm">1</x-ui.button>
                                <x-ui.button variant="outline" size="sm">2</x-ui.button>
                                <x-ui.button variant="outline" size="sm">3</x-ui.button>
                                <x-ui.button variant="outline" size="sm">{{ __('Next') }}</x-ui.button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>
