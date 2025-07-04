<x-layouts.app>
    <x-slot:title>{{ __('AI Assistant') }}</x-slot:title>

    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">AI Assistant</h1>
                <p class="text-gray-600 dark:text-gray-400">Get insights and recommendations for your business</p>
            </div>
        </div>

        <!-- AI Insights Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Business Insights -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.504 1.132a1 1 0 01.992 0l1.75 1a1 1 0 11-.992 1.736L10 3.152l-1.254.716a1 1 0 11-.992-1.736l1.75-1zM5.618 4.504a1 1 0 01-.372 1.364L5.016 6l.23.132a1 1 0 11-.992 1.736L3 7.723V8a1 1 0 01-2 0V6a1 1 0 01.504-.868l3-1.732a1 1 0 011.114.104zM14.382 4.504a1 1 0 011.114-.104l3 1.732A1 1 0 0119 6v2a1 1 0 11-2 0v-.277l-1.254.145a1 1 0 11-.992-1.736L15.984 6l-.23-.132a1 1 0 01-.372-1.364z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sales Forecast</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        Based on current trends, your sales are projected to increase by 15% next month.
                    </p>
                    <div class="text-xs text-blue-600 dark:text-blue-400">
                        Confidence: 87%
                    </div>
                </div>
            </x-ui.card>

            <!-- Inventory Recommendations -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Inventory Alert</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        Consider restocking 3 products that are running low and trending upward.
                    </p>
                    <div class="text-xs text-amber-600 dark:text-amber-400">
                        Priority: High
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Quick Actions -->
        <div class="w-full">
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-ui.button variant="outline" class="flex items-center justify-center p-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Generate Sales Report
                        </x-ui.button>
                        
                        <x-ui.button variant="outline" class="flex items-center justify-center p-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Market Analysis
                        </x-ui.button>
                        
                        <x-ui.button variant="outline" class="flex items-center justify-center p-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                            </svg>
                            Optimize Pricing
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
