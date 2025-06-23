<x-layouts.app>
    <x-slot:title>{{ __('User Management') }}</x-slot:title>

    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('User Management') }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ __('Manage user signups, vendor validation, and user access') }}</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8">
            <x-ui.card class="p-4">
                <div class="flex flex-wrap gap-2">
                    <x-ui.button variant="primary" size="sm" id="pending-signups-tab">{{ __('Pending Signups') }}</x-ui.button>
                    <x-ui.button variant="outline" size="sm" id="vendor-validation-tab">{{ __('Vendor Validation') }}</x-ui.button>
                    <x-ui.button variant="outline" size="sm" id="user-search-tab">{{ __('User Search & Block') }}</x-ui.button>
                </div>
            </x-ui.card>
        </div>

        <!-- Pending Signups Section -->
        <div id="pending-signups-section">
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Pending User Signups') }}</h3>
                        <div class="flex gap-2">
                            <x-ui.form-input type="search" placeholder="{{ __('Search users...') }}" class="min-w-64" />
                            <x-ui.button variant="outline" size="sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                                </svg>
                                {{ __('Filter') }}
                            </x-ui.button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            $pendingUsers = [
                                ['name' => 'John Smith', 'email' => 'john.smith@example.com', 'role' => 'Retailer', 'date' => '2024-01-15', 'phone' => '+1-234-567-8901'],
                                ['name' => 'Sarah Johnson', 'email' => 'sarah.j@company.com', 'role' => 'Vendor', 'date' => '2024-01-14', 'phone' => '+1-234-567-8902'],
                                ['name' => 'Mike Chen', 'email' => 'mike.chen@tech.com', 'role' => 'Supplier', 'date' => '2024-01-14', 'phone' => '+1-234-567-8903'],
                                ['name' => 'Lisa Brown', 'email' => 'lisa.brown@store.com', 'role' => 'Retailer', 'date' => '2024-01-13', 'phone' => '+1-234-567-8904'],
                                ['name' => 'David Wilson', 'email' => 'david.w@logistics.com', 'role' => 'Vendor', 'date' => '2024-01-13', 'phone' => '+1-234-567-8905'],
                                ['name' => 'Emma Davis', 'email' => 'emma.davis@retail.com', 'role' => 'Retailer', 'date' => '2024-01-12', 'phone' => '+1-234-567-8906'],
                            ];
                        @endphp

                        @foreach ($pendingUsers as $user)
                            <x-ui.user-card
                                :name="$user['name']"
                                :email="$user['email']"
                                :role="$user['role']"
                                :date="$user['date']"
                                :phone="$user['phone']"
                                type="pending"
                            />
                        @endforeach
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Vendor Validation Section -->
        <div id="vendor-validation-section" class="hidden">
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Vendor Validation') }}</h3>
                        <div class="flex gap-2">
                            <x-ui.form-input type="search" placeholder="{{ __('Search vendors...') }}" class="min-w-64" />
                            <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                <option>{{ __('All Scores') }}</option>
                                <option>{{ __('High Score (8-10)') }}</option>
                                <option>{{ __('Medium Score (5-7)') }}</option>
                                <option>{{ __('Low Score (1-4)') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            $vendors = [
                                ['name' => 'TechSupply Co.', 'email' => 'contact@techsupply.com', 'score' => 8.5, 'date' => '2024-01-15', 'phone' => '+1-555-0101', 'business' => 'Electronics Wholesale'],
                                ['name' => 'Global Parts Ltd.', 'email' => 'info@globalparts.com', 'score' => 7.2, 'date' => '2024-01-14', 'phone' => '+1-555-0102', 'business' => 'Automotive Parts'],
                                ['name' => 'Fresh Foods Inc.', 'email' => 'orders@freshfoods.com', 'score' => 9.1, 'date' => '2024-01-14', 'phone' => '+1-555-0103', 'business' => 'Food Distribution'],
                                ['name' => 'BuildMart Supplies', 'email' => 'sales@buildmart.com', 'score' => 6.8, 'date' => '2024-01-13', 'phone' => '+1-555-0104', 'business' => 'Construction Materials'],
                                ['name' => 'Fashion Forward', 'email' => 'wholesale@fashionfw.com', 'score' => 5.5, 'date' => '2024-01-13', 'phone' => '+1-555-0105', 'business' => 'Clothing & Apparel'],
                                ['name' => 'Office Solutions Pro', 'email' => 'contact@officesol.com', 'score' => 8.9, 'date' => '2024-01-12', 'phone' => '+1-555-0106', 'business' => 'Office Supplies'],
                            ];
                        @endphp

                        @foreach ($vendors as $vendor)
                            <x-ui.user-card
                                :name="$vendor['name']"
                                :email="$vendor['email']"
                                role="Vendor"
                                :date="$vendor['date']"
                                :phone="$vendor['phone']"
                                :score="$vendor['score']"
                                :business="$vendor['business']"
                                type="vendor"
                            />
                        @endforeach
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- User Search & Block Section -->
        <div id="user-search-section" class="hidden">
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Search & Block Users') }}</h3>
                        <div class="flex gap-2">
                            <x-ui.form-input type="search" placeholder="{{ __('Search by name, email, or ID...') }}" class="min-w-80" />
                            <x-ui.button variant="primary">{{ __('Search') }}</x-ui.button>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('User') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Role') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Last Active') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $users = [
                                        ['name' => 'Alice Cooper', 'email' => 'alice@example.com', 'role' => 'Retailer', 'status' => 'active', 'last_active' => '2024-01-15'],
                                        ['name' => 'Bob Miller', 'email' => 'bob@company.com', 'role' => 'Vendor', 'status' => 'active', 'last_active' => '2024-01-14'],
                                        ['name' => 'Carol White', 'email' => 'carol@store.com', 'role' => 'Supplier', 'status' => 'blocked', 'last_active' => '2024-01-10'],
                                        ['name' => 'Dan Thompson', 'email' => 'dan@business.com', 'role' => 'Retailer', 'status' => 'active', 'last_active' => '2024-01-14'],
                                        ['name' => 'Eva Garcia', 'email' => 'eva@logistics.com', 'role' => 'Vendor', 'status' => 'inactive', 'last_active' => '2024-01-08'],
                                    ];
                                @endphp

                                @foreach ($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ substr($user['name'], 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user['name'] }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user['email'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $user['role'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-ui.status-badge :status="$user['status']" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ date('M j, Y', strtotime($user['last_active'])) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    {{ __('View') }}
                                                </button>
                                                @if ($user['status'] === 'blocked')
                                                    <button class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                        {{ __('Unblock') }}
                                                    </button>
                                                @else
                                                    <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        {{ __('Block') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = {
                'pending-signups-tab': 'pending-signups-section',
                'vendor-validation-tab': 'vendor-validation-section',
                'user-search-tab': 'user-search-section'
            };

            // Tab switching functionality
            Object.keys(tabs).forEach(tabId => {
                document.getElementById(tabId).addEventListener('click', function() {
                    // Hide all sections
                    Object.values(tabs).forEach(sectionId => {
                        document.getElementById(sectionId).classList.add('hidden');
                    });

                    // Remove active state from all tabs
                    Object.keys(tabs).forEach(id => {
                        const tab = document.getElementById(id);
                        tab.classList.remove('bg-blue-600', 'text-white');
                        tab.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    });

                    // Show selected section
                    document.getElementById(tabs[tabId]).classList.remove('hidden');

                    // Add active state to clicked tab
                    this.classList.add('bg-blue-600', 'text-white');
                    this.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                });
            });
        });
    </script>
</x-layouts.app>
