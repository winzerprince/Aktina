<x-app-layout>
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    Alert Threshold Management
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Configure and manage system alert thresholds for inventory, system performance, and more
                </p>
            </div>
        </div>

        <livewire:admin.alert-threshold-manager />
    </div>
</x-app-layout>
