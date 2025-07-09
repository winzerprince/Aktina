<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Production Order Management</h1>
                <p class="text-gray-600">Track, assign, and fulfill production orders</p>
            </div>
            <div class="flex flex-col md:flex-row gap-3 mt-4 md:mt-0">
                <button type="button" wire:click="exportOrders" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Export Orders
                </button>
            </div>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Total Production Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Processing Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-sky-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Processing</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $stats['processing_orders'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Fulfilled Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-cyan-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Fulfilled</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $stats['fulfilled_orders'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Efficiency Rate -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Efficiency Rate</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $stats['efficiency_rate'] }}%</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input wire:model.debounce.300ms="search" type="text" placeholder="Search orders..." class="pl-10 py-2 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 rounded-md border-gray-300">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model="statusFilter" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">All Statuses</option>
                        <option value="accepted">Accepted</option>
                        <option value="processing">Processing</option>
                        <option value="partially_fulfilled">Partially Fulfilled</option>
                        <option value="fulfilled">Fulfilled</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <select wire:model="dateRange" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                @if ($dateRange === 'custom')
                <div class="flex gap-2">
                    <input wire:model="dateFrom" type="date" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <input wire:model="dateTo" type="date" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                @endif

                <!-- Priority Filter -->
                <div>
                    <select wire:model="priorityFilter" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="flex items-center gap-3">
                <div>
                    <select id="bulkAction" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Bulk Actions</option>
                        @foreach ($validBulkStatuses as $status => $label)
                            <option value="{{ $status }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" onclick="confirmBulkAction()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow">
        <!-- Success/Error Messages -->
        @if (!empty($errorMessage))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (!empty($successMessage))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ $successMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3 px-4">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                        </th>
                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('id')">
                            <div class="flex items-center">
                                Order ID
                                @if ($sortBy === 'id')
                                    @if ($sortDirection === 'asc')
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @else
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                            <div class="flex items-center">
                                Date
                                @if ($sortBy === 'created_at')
                                    @if ($sortDirection === 'asc')
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @else
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Buyer
                        </th>
                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Items
                        </th>
                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Assigned
                        </th>
                        <th scope="col" class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="selectedOrders" value="{{ $order->id }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    #{{ $order->id }}
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->buyer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->buyer->email }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->orderItems->count() }} items
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-900">
                                @if (!empty($order->metadata['assigned_employees']))
                                    <div class="flex -space-x-2">
                                        @foreach(array_slice($order->metadata['assigned_employees'], 0, 3) as $employeeId)
                                            <div class="h-7 w-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium uppercase text-gray-700 border border-white">
                                                {{ substr($employeeId, 0, 2) }}
                                            </div>
                                        @endforeach
                                        @if (count($order->metadata['assigned_employees']) > 3)
                                            <div class="h-7 w-7 rounded-full bg-gray-300 flex items-center justify-center text-xs font-medium text-gray-700 border border-white">
                                                +{{ count($order->metadata['assigned_employees']) - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewOrderDetails({{ $order->id }})" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>

                                    <button wire:click="checkResources({{ $order->id }})" class="text-green-600 hover:text-green-900" title="Check Resources">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </button>

                                    <button wire:click="openAssignEmployees({{ $order->id }})" class="text-blue-600 hover:text-blue-900" title="Assign Employees">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </button>

                                    <button wire:click="openScheduleProduction({{ $order->id }})" class="text-yellow-600 hover:text-yellow-900" title="Schedule Production">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>

                                    <button wire:click="openFulfillmentWizard({{ $order->id }})" class="text-cyan-600 hover:text-cyan-900" title="Process Fulfillment">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 px-4 text-center text-gray-500">
                                No orders found. Try adjusting your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Order Details Modal -->
    @if ($showOrderDetails && $selectedOrder)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 my-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Order #{{ $selectedOrder->id }} Details</h3>
                        <button wire:click="closeOrderDetails" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 max-h-96 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Order Info -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-2">Order Information</h4>
                            <dl class="grid grid-cols-1 gap-2">
                                <div class="py-2 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Order ID:</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">#{{ $selectedOrder->id }}</dd>
                                </div>
                                <div class="py-2 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Created Date:</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $selectedOrder->created_at->format('M d, Y h:i A') }}</dd>
                                </div>
                                <div class="py-2 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                    <dd class="text-sm col-span-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $selectedOrder->getStatusColor() }}-100 text-{{ $selectedOrder->getStatusColor() }}-800">
                                            {{ ucfirst(str_replace('_', ' ', $selectedOrder->status)) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-2 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Buyer:</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $selectedOrder->buyer->name }}</dd>
                                </div>
                                <div class="py-2 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Total Items:</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $selectedOrder->orderItems->count() }}</dd>
                                </div>
                                @if (!empty($selectedOrder->metadata['production_schedule']))
                                <div class="py-2 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Production Date:</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ $selectedOrder->metadata['production_schedule']['scheduled_date'] }}</dd>
                                </div>
                                <div class="py-2 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Priority:</dt>
                                    <dd class="text-sm text-gray-900 col-span-2">{{ ucfirst($selectedOrder->metadata['production_schedule']['priority']) }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Items -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-2">Order Items</h4>
                            <div class="overflow-y-auto max-h-60">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                            <th scope="col" class="py-2 px-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th scope="col" class="py-2 px-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($selectedOrder->orderItems as $item)
                                            <tr>
                                                <td class="py-2 px-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $item->product->name }}
                                                </td>
                                                <td class="py-2 px-3 whitespace-nowrap text-right text-sm text-gray-900">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="py-2 px-3 whitespace-nowrap text-right text-sm text-gray-900">
                                                    ${{ number_format($item->price, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button wire:click="closeOrderDetails" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Resource Check Modal -->
    @if ($showResourceCheck && $selectedOrder)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 my-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Resource Availability for Order #{{ $selectedOrder->id }}</h3>
                        <button wire:click="closeOrderDetails" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($resources as $resource)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $resource['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ $resource['required'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ $resource['available'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if ($resource['sufficient'])
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Sufficient
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Insufficient
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No resources found for this order.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button wire:click="closeOrderDetails" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Assign Employees Modal -->
    @if ($showAssignEmployees && $selectedOrder)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 my-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Assign Employees to Order #{{ $selectedOrder->id }}</h3>
                        <button wire:click="closeOrderDetails" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    @if (!empty($errorMessage))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($availableEmployees as $employee)
                            <div class="flex items-center">
                                <input type="checkbox" id="employee-{{ $employee['id'] }}" value="{{ $employee['id'] }}" wire:model="selectedEmployees" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="employee-{{ $employee['id'] }}" class="ml-3 block text-sm font-medium text-gray-700">
                                    {{ $employee['name'] }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closeOrderDetails" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button wire:click="assignEmployees" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Assign Employees
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Schedule Production Modal -->
    @if ($showScheduleProduction && $selectedOrder)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 my-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Schedule Production for Order #{{ $selectedOrder->id }}</h3>
                        <button wire:click="closeOrderDetails" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    @if (!empty($errorMessage))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Production Date</label>
                            <input type="date" id="scheduled_date" wire:model="scheduledDate" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div>
                            <label for="production_priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select id="production_priority" wire:model="productionPriority" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Production Items</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Production Days</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($productionDetails as $index => $detail)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $detail['product_name'] }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $detail['quantity'] }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <input type="number" wire:model="productionDetails.{{ $index }}.production_days" min="1" class="w-20 mx-auto block focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closeOrderDetails" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button wire:click="scheduleProduction" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Schedule Production
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Fulfillment Wizard Modal -->
    @if ($showFulfillmentWizard && $selectedOrder)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 my-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Process Order #{{ $selectedOrder->id }}
                            <span class="text-sm text-gray-500 ml-2">Step {{ $wizardStep }} of 3</span>
                        </h3>
                        <button wire:click="closeOrderDetails" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    @if (!empty($errorMessage))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <!-- Progress bar -->
                        <div class="relative pt-1">
                            <div class="overflow-hidden h-2 mb-2 text-xs flex rounded bg-gray-200">
                                <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500" style="width: {{ $wizardStep * 100 / 3 }}%"></div>
                            </div>
                            <div class="flex justify-between">
                                <div class="text-xs {{ $wizardStep >= 1 ? 'text-indigo-600 font-semibold' : 'text-gray-500' }}">Status</div>
                                <div class="text-xs {{ $wizardStep >= 2 ? 'text-indigo-600 font-semibold' : 'text-gray-500' }}">Items</div>
                                <div class="text-xs {{ $wizardStep >= 3 ? 'text-indigo-600 font-semibold' : 'text-gray-500' }}">Notes</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Select Status -->
                    @if ($wizardStep === 1)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-4">Select Next Status</h4>
                            <div class="grid grid-cols-1 gap-4">
                                @forelse ($validNextStatuses as $status => $label)
                                    <div class="flex items-center">
                                        <input type="radio" id="status-{{ $status }}" value="{{ $status }}" wire:model="nextStatus" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="status-{{ $status }}" class="ml-3 block text-sm font-medium text-gray-700">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-gray-500">No valid status transitions available for this order.</div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Step 2: Fulfill Items -->
                    @if ($wizardStep === 2)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-4">
                                @if($nextStatus === 'partially_fulfilled')
                                    Specify Partial Fulfillment Quantities
                                @else
                                    Confirm Full Fulfillment
                                @endif
                            </h4>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ordered</th>
                                            @if($nextStatus === 'partially_fulfilled')
                                                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Fulfill Qty</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason (optional)</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($selectedOrder->orderItems as $item)
                                            <tr>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $item->product->name }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900">
                                                    {{ $item->quantity }}
                                                </td>
                                                @if($nextStatus === 'partially_fulfilled')
                                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                                        <input type="number" wire:model="partialFulfilledItems.{{ $item->id }}.fulfilled_quantity" min="0" max="{{ $item->quantity }}" class="w-20 mx-auto focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </td>
                                                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                                                        <input type="text" wire:model="partialFulfilledItems.{{ $item->id }}.reason" placeholder="Reason for partial" class="w-full focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Step 3: Add Notes -->
                    @if ($wizardStep === 3)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-4">Add Production Notes (Optional)</h4>
                            <div>
                                <label for="notes" class="sr-only">Notes</label>
                                <textarea id="notes" wire:model="productionNotes.general" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Add any production notes or comments"></textarea>
                            </div>

                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-700">Order Summary</h5>
                                <div class="mt-2 p-4 bg-gray-50 rounded-md">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-500">New Status:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $nextStatus)) }}</span>
                                    </div>

                                    @if($nextStatus === 'partially_fulfilled')
                                        <div class="mb-2">
                                            <span class="text-sm font-medium text-gray-500">Partially Fulfilled Items:</span>
                                            <ul class="mt-1 list-disc list-inside text-sm text-gray-700">
                                                @foreach ($partialFulfilledItems as $itemId => $data)
                                                    @if ($data['fulfilled_quantity'] > 0)
                                                        <li>{{ $data['product_name'] }}: {{ $data['fulfilled_quantity'] }} of {{ $data['max_quantity'] }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <div>
                        @if ($wizardStep > 1)
                            <button wire:click="previousStep" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Previous
                            </button>
                        @else
                            <button wire:click="closeOrderDetails" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                        @endif
                    </div>

                    <div>
                        @if ($wizardStep < 3)
                            <button wire:click="nextStep" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Next
                            </button>
                        @else
                            <button wire:click="completeFulfillment" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Complete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- JavaScript for Bulk Actions -->
    <script>
        function confirmBulkAction() {
            const select = document.getElementById('bulkAction');
            const selectedAction = select.value;

            if (!selectedAction) {
                return;
            }

            if (confirm('Are you sure you want to update the selected orders?')) {
                @this.bulkUpdateStatus(selectedAction);
            }
        }
    </script>
</div>
