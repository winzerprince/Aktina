<x-layouts.app :title="_('Inventory')">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Inventory Management</h1>
                    <p class="text-gray-600">Monitor and manage products and resources across warehouses</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Report
                    </button>
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Item
                    </button>
                </div>
            </div>
        </div>

        <!-- Inventory Tabs -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button id="products-tab" onclick="showTab('products')" class="border-transparent text-indigo-600 border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium" aria-current="page">
                        Products
                    </button>
                    <button id="resources-tab" onclick="showTab('resources')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 py-4 px-1 text-sm font-medium">
                        Resources
                    </button>
                </nav>
            </div>
            
            <!-- Products Tab Content -->
            <div id="products-content" class="p-6">
                <livewire:production-manager.inventory.products-table />
            </div>
            
            <!-- Resources Tab Content -->
            <div id="resources-content" class="p-6 hidden">
                <livewire:production-manager.inventory.resources-table />
            </div>
        </div>
    </div>

    <script>
    function showTab(tabName) {
        // Hide all tab contents
        document.getElementById('products-content').classList.add('hidden');
        document.getElementById('resources-content').classList.add('hidden');
        
        // Remove active classes from all tabs
        document.getElementById('products-tab').classList.remove('text-indigo-600', 'border-indigo-500');
        document.getElementById('products-tab').classList.add('text-gray-500', 'border-transparent');
        document.getElementById('resources-tab').classList.remove('text-indigo-600', 'border-indigo-500');
        document.getElementById('resources-tab').classList.add('text-gray-500', 'border-transparent');
        
        // Show selected tab content and add active classes
        document.getElementById(tabName + '-content').classList.remove('hidden');
        document.getElementById(tabName + '-tab').classList.remove('text-gray-500', 'border-transparent');
        document.getElementById(tabName + '-tab').classList.add('text-indigo-600', 'border-indigo-500');
    }
    </script>
</x-layouts.app>
