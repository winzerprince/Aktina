<div class="bg-white shadow rounded-lg p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Resource Usage Analytics</h3>
            <p class="mt-1 text-sm text-gray-500">Monitor warehouse and production resource utilization</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-0">
            <!-- View Type Filter -->
            <select wire:model.live="viewType" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="warehouse">Warehouse Usage</option>
                <option value="production">Production Usage</option>
                <option value="capacity">Capacity Analysis</option>
            </select>
            
            <!-- Warehouse Filter -->
            <select wire:model.live="selectedWarehouse" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="all">All Warehouses</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
            
            <!-- Time Range Filter -->
            <select wire:model.live="timeRange" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
            </select>
            
            <!-- Export Button -->
            <button wire:click="exportResourceReport" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
            
            <!-- Refresh Button -->
            <button wire:click="refreshData" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Capacity -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Capacity</p>
                    <p class="text-2xl font-bold">{{ number_format($totalCapacity) }}</p>
                </div>
                <div class="text-blue-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Used Capacity -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Used Capacity</p>
                    <p class="text-2xl font-bold">{{ number_format($usedCapacity) }}</p>
                </div>
                <div class="text-orange-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Utilization -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Utilization</p>
                    <p class="text-2xl font-bold">{{ number_format($utilizationPercentage, 1) }}%</p>
                </div>
                <div class="text-green-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Efficiency Score -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Efficiency Score</p>
                    <p class="text-2xl font-bold">{{ number_format($efficiencyScore, 1) }}/10</p>
                </div>
                <div class="text-purple-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    @if($loading)
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading resource data...</span>
        </div>
    @else
        <!-- Chart Container -->
        <div class="mb-6">
            @if($viewType === 'warehouse')
                <div id="warehouse-usage-chart" class="h-80"></div>
            @elseif($viewType === 'production')
                <div id="production-usage-chart" class="h-80"></div>
            @elseif($viewType === 'capacity')
                <div id="capacity-usage-chart" class="h-80"></div>
            @endif
        </div>

        <!-- Performance Analysis -->
        @if(count($topPerformers) > 0 || count($bottomPerformers) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Top Performers -->
                @if(count($topPerformers) > 0)
                    <div class="border-t pt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            Top Performers
                        </h4>
                        <div class="space-y-3">
                            @foreach($topPerformers as $performer)
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $performer['name'] ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $performer['type'] ?? 'Resource' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-green-600">{{ number_format($performer['efficiency'] ?? 0, 1) }}%</p>
                                        <p class="text-xs text-gray-500">Efficiency</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Bottom Performers -->
                @if(count($bottomPerformers) > 0)
                    <div class="border-t pt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Needs Attention
                        </h4>
                        <div class="space-y-3">
                            @foreach($bottomPerformers as $performer)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $performer['name'] ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $performer['type'] ?? 'Resource' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-red-600">{{ number_format($performer['efficiency'] ?? 0, 1) }}%</p>
                                        <p class="text-xs text-gray-500">Efficiency</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- ApexCharts Scripts -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            let warehouseChart, productionChart, capacityChart;
            
            function initWarehouseChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const chartData = @json($chartData);
                    
                    const options = {
                        chart: {
                            type: 'area',
                            height: 320,
                            toolbar: { show: true },
                            stacked: true
                        },
                        series: [{
                            name: 'Used Space',
                            data: chartData.map(item => ({
                                x: item.date,
                                y: item.used_space
                            }))
                        }, {
                            name: 'Available Space',
                            data: chartData.map(item => ({
                                x: item.date,
                                y: item.available_space
                            }))
                        }],
                        xaxis: {
                            type: 'datetime'
                        },
                        colors: ['#F59E0B', '#10B981'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                opacityFrom: 0.6,
                                opacityTo: 0.1
                            }
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        grid: {
                            borderColor: '#F3F4F6'
                        }
                    };

                    warehouseChart = new ApexCharts(document.querySelector("#warehouse-usage-chart"), options);
                    warehouseChart.render();
                }
            }

            function initProductionChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const chartData = @json($chartData);
                    
                    const options = {
                        chart: {
                            type: 'line',
                            height: 320,
                            toolbar: { show: true }
                        },
                        series: [{
                            name: 'Production Volume',
                            data: chartData.map(item => ({
                                x: item.date,
                                y: item.production_volume
                            }))
                        }, {
                            name: 'Capacity Utilization',
                            data: chartData.map(item => ({
                                x: item.date,
                                y: item.capacity_utilization
                            }))
                        }],
                        xaxis: {
                            type: 'datetime'
                        },
                        colors: ['#3B82F6', '#8B5CF6'],
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        markers: {
                            size: 5
                        },
                        grid: {
                            borderColor: '#F3F4F6'
                        }
                    };

                    productionChart = new ApexCharts(document.querySelector("#production-usage-chart"), options);
                    productionChart.render();
                }
            }

            function initCapacityChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const chartData = @json($chartData);
                    
                    const options = {
                        chart: {
                            type: 'bar',
                            height: 320,
                            toolbar: { show: true }
                        },
                        series: [{
                            name: 'Utilization %',
                            data: chartData.map(item => ({
                                x: item.resource_name,
                                y: item.utilization_percentage
                            }))
                        }],
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                columnWidth: '60%'
                            }
                        },
                        colors: ['#06B6D4'],
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return val + '%';
                            }
                        },
                        grid: {
                            borderColor: '#F3F4F6'
                        }
                    };

                    capacityChart = new ApexCharts(document.querySelector("#capacity-usage-chart"), options);
                    capacityChart.render();
                }
            }

            // Initialize appropriate chart based on view type
            if (@this.viewType === 'warehouse') {
                initWarehouseChart();
            } else if (@this.viewType === 'production') {
                initProductionChart();
            } else if (@this.viewType === 'capacity') {
                initCapacityChart();
            }

            // Listen for data refresh
            window.addEventListener('resourceDataRefreshed', function() {
                // Destroy existing charts
                if (warehouseChart) warehouseChart.destroy();
                if (productionChart) productionChart.destroy();
                if (capacityChart) capacityChart.destroy();
                
                // Reinitialize based on current view
                setTimeout(() => {
                    if (@this.viewType === 'warehouse') {
                        initWarehouseChart();
                    } else if (@this.viewType === 'production') {
                        initProductionChart();
                    } else if (@this.viewType === 'capacity') {
                        initCapacityChart();
                    }
                }, 100);
            });
        });
    </script>
</div>
