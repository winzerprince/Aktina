<x-layouts.app>
    <x-slot:title>{{ __('Business Insights') }}</x-slot:title>

    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Business Insights') }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ __('AI-powered predictions and customer analytics') }}</p>
        </div>

        <!-- Insights Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-ui.metric-card
                title="{{ __('Prediction Accuracy') }}"
                value="94.2%"
                change="+2.1%"
                change-type="positive"
                icon="chart-bar"
                description="{{ __('AI model performance') }}"
            />

            <x-ui.metric-card
                title="{{ __('Customer Segments') }}"
                value="7"
                change="+1"
                change-type="positive"
                icon="user-group"
                description="{{ __('Active segments') }}"
            />

            <x-ui.metric-card
                title="{{ __('Predicted Revenue') }}"
                value="$156,420"
                change="+8.5%"
                change-type="positive"
                icon="currency-dollar"
                description="{{ __('Next 30 days') }}"
            />

            <x-ui.metric-card
                title="{{ __('Risk Score') }}"
                value="2.1"
                change="-0.3"
                change-type="positive"
                icon="shield-check"
                description="{{ __('Business risk level') }}"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- AI Predictions -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('AI Predictions') }}</h3>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Live') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Sales Prediction -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ __('Sales Forecast') }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ __('Expected 18% increase in sales next week. Peak expected on Wednesday.') }}
                                    </p>
                                    <div class="mt-2">
                                        <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">{{ __('Confidence: 92%') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Prediction -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15.586 13H14a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ __('Inventory Alert') }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ __('Low stock predicted for 3 products by end of month. Restock recommended.') }}
                                    </p>
                                    <div class="mt-2">
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">{{ __('Confidence: 87%') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Behavior -->
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ __('Customer Behavior') }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ __('Premium customers showing 25% increase in purchase frequency.') }}
                                    </p>
                                    <div class="mt-2">
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">{{ __('Confidence: 95%') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-ui.button variant="outline" class="w-full">
                            {{ __('View Detailed Predictions') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            <!-- Predicted Sales Chart -->
            <x-ui.chart-card
                title="{{ __('Sales Prediction') }}"
                description="{{ __('30-day sales forecast vs actual') }}"
            >
                <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">{{ __('Predictive Sales Chart') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Time series prediction model') }}</p>
                    </div>
                </div>
            </x-ui.chart-card>
        </div>

        <!-- Customer Segmentation -->
        <div class="mb-8">
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Customer Segmentation') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('ML-based customer clustering analysis') }}</p>
                        </div>
                        <x-ui.button variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('Export Report') }}
                        </x-ui.button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Cluster Visualization -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-4">{{ __('Cluster Distribution') }}</h4>
                            <div class="h-64 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-gray-400 mb-2">
                                        <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300">{{ __('Customer Cluster Graph') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('K-means clustering visualization') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Segment Details -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-4">{{ __('Segment Insights') }}</h4>
                            <div class="space-y-3">
                                @php
                                    $segments = [
                                        ['name' => 'Premium Buyers', 'size' => 156, 'value' => '$45,320', 'color' => 'bg-blue-500'],
                                        ['name' => 'Regular Customers', 'size' => 342, 'value' => '$28,150', 'color' => 'bg-green-500'],
                                        ['name' => 'Occasional Buyers', 'size' => 198, 'value' => '$12,840', 'color' => 'bg-yellow-500'],
                                        ['name' => 'New Customers', 'size' => 89, 'value' => '$6,240', 'color' => 'bg-purple-500'],
                                        ['name' => 'Price Sensitive', 'size' => 267, 'value' => '$15,690', 'color' => 'bg-orange-500'],
                                    ];
                                @endphp

                                @foreach ($segments as $segment)
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-4 h-4 {{ $segment['color'] }} rounded-full"></div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $segment['name'] }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $segment['size'] }} {{ __('customers') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $segment['value'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('avg. value') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- AI Model Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Model Performance') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-300">{{ __('Sales Prediction') }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">94.2%</span>
                            </div>
                            <x-ui.progress-bar :value="94.2" color="blue" />
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-300">{{ __('Customer Segmentation') }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">89.7%</span>
                            </div>
                            <x-ui.progress-bar :value="89.7" color="green" />
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-300">{{ __('Demand Forecasting') }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">91.5%</span>
                            </div>
                            <x-ui.progress-bar :value="91.5" color="purple" />
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Data Quality') }}</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">98.5%</div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Data Completeness') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">1.2M</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Records') }}</p>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">15min</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Last Update') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Quick Actions') }}</h3>
                    <div class="space-y-3">
                        <x-ui.button variant="outline" class="w-full justify-start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('Retrain Models') }}
                        </x-ui.button>
                        <x-ui.button variant="outline" class="w-full justify-start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            {{ __('View Analytics') }}
                        </x-ui.button>
                        <x-ui.button variant="outline" class="w-full justify-start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('Settings') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
