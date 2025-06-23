<x-layouts.app>
    <x-slot:title>{{ __('Admin Dashboard') }}</x-slot:title>

    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Admin Dashboard') }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ __('Overview of your business performance and key metrics') }}</p>
        </div>

        <!-- Time Filter -->
        <div class="mb-6">
            <x-ui.card class="p-4">
                <div class="flex flex-wrap gap-2">
                    <x-ui.button variant="primary" size="sm" id="today-btn">{{ __('Today') }}</x-ui.button>
                    <x-ui.button variant="outline" size="sm" id="week-btn">{{ __('This Week') }}</x-ui.button>
                    <x-ui.button variant="outline" size="sm" id="month-btn">{{ __('This Month') }}</x-ui.button>
                </div>
            </x-ui.card>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <x-ui.metric-card
                title="{{ __('Total Sales') }}"
                value="$45,320"
                change="+12.5%"
                change-type="positive"
                icon="chart-bar"
                description="{{ __('Revenue from all sales') }}"
            />

            <x-ui.metric-card
                title="{{ __('Revenue') }}"
                value="$38,240"
                change="+8.2%"
                change-type="positive"
                icon="currency-dollar"
                description="{{ __('Net revenue after costs') }}"
            />

            <x-ui.metric-card
                title="{{ __('Workers') }}"
                value="156"
                change="+3"
                change-type="positive"
                icon="users"
                description="{{ __('Active workforce') }}"
            />

            <x-ui.metric-card
                title="{{ __('Retailers') }}"
                value="89"
                change="+5"
                change-type="positive"
                icon="building-storefront"
                description="{{ __('Registered retailers') }}"
            />

            <x-ui.metric-card
                title="{{ __('Vendors') }}"
                value="34"
                change="+2"
                change-type="positive"
                icon="truck"
                description="{{ __('Active vendors') }}"
            />

            <x-ui.metric-card
                title="{{ __('Orders') }}"
                value="1,234"
                change="+18.7%"
                change-type="positive"
                icon="shopping-bag"
                description="{{ __('Total orders processed') }}"
            />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Sales Chart -->
            <x-ui.chart-card
                title="{{ __('Sales Overview') }}"
                description="{{ __('Sales performance over time') }}"
                class="lg:col-span-2"
            >
                <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">{{ __('Chart will be rendered here') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Integration with Chart.js or similar library') }}</p>
                    </div>
                </div>
            </x-ui.chart-card>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Orders') }}</h3>
                        <a href="{{ route('admin.sales') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            {{ __('View all') }}
                        </a>
                    </div>

                    <div class="space-y-3">
                        @for ($i = 1; $i <= 5; $i++)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Order #{{ 1000 + $i }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Retailer') }} {{ $i }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 dark:text-white">${{ number_format(rand(100, 1000), 2) }}</p>
                                    <x-ui.status-badge status="completed" />
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </x-ui.card>

            <!-- Pending Actions -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Pending Actions') }}</h3>
                        <a href="{{ route('admin.user-management') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            {{ __('View all') }}
                        </a>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ __('5 Vendor Applications') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Waiting for approval') }}</p>
                                </div>
                            </div>
                            <x-ui.button variant="outline" size="sm">{{ __('Review') }}</x-ui.button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ __('12 User Signups') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Pending verification') }}</p>
                                </div>
                            </div>
                            <x-ui.button variant="outline" size="sm">{{ __('Review') }}</x-ui.button>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <script>
        // Simple time filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('[id$="-btn"]');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active state from all buttons
                    buttons.forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    });

                    // Add active state to clicked button
                    this.classList.add('bg-blue-600', 'text-white');
                    this.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');

                    // Here you would typically make an AJAX call to update the data
                    console.log('Filter changed to:', this.textContent);
                });
            });
        });
    </script>
</x-layouts.app>
