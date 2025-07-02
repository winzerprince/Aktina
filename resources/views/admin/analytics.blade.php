<x-layouts.app>
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Advanced Analytics</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive business intelligence and performance insights</p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->format('F j, Y') }}
                </div>
            </div>
        </div>

        <!-- Advanced Analytics Component -->
        <livewire:admin.advanced-analytics />
    </div>
</x-layouts.app>
