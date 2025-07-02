<div class="bg-white shadow rounded-lg p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Inventory Analytics</h3>
            <p class="mt-1 text-sm text-gray-500">Monitor stock levels and inventory performance</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-0">
            <!-- Warehouse Filter -->
            <select wire:model.live="selectedWarehouse" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="all">All Warehouses</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
            
            <!-- View Type Filter -->
            <select wire:model.live="chartView" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="stock_levels">Stock Levels</option>
                <option value="turnover">Inventory Turnover</option>
                <option value="alerts">Stock Alerts</option>
            </select>
            
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">Total Stock Value</p>
                    <p class="text-2xl font-bold">${{ number_format($totalStock, 2) }}</p>
                </div>
                <div class="text-indigo-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Low Stock Items</p>
                    <p class="text-2xl font-bold">{{ $lowStockItems }}</p>
                </div>
                <div class="text-red-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm">Avg Turnover</p>
                    <p class="text-2xl font-bold">{{ number_format($averageTurnover, 1) }}x</p>
                </div>
                <div class="text-emerald-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    @if($loading)
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading inventory data...</span>
        </div>
    @else
        <!-- Chart Container -->
        <div class="mb-6">
            @if($chartView === 'stock_levels')
                <div id="stock-levels-chart" class="h-80"></div>
            @elseif($chartView === 'turnover')
                <div id="turnover-chart" class="h-80"></div>
            @elseif($chartView === 'alerts')
                <div id="alerts-chart" class="h-80"></div>
            @endif
        </div>

        <!-- Chart Legend/Summary -->
        @if($chartView === 'stock_levels' && count($stockData) > 0)
            <div class="border-t pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Stock Level Summary</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach(array_slice($stockData, 0, 8) as $item)
                        <div class="text-center">
                            <p class="text-xs font-medium text-gray-900 truncate">{{ $item['resource_name'] ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">{{ number_format($item['current_stock'] ?? 0) }} units</p>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                @php
                                    $percentage = ($item['minimum_level'] ?? 0) > 0 
                                        ? min(100, (($item['current_stock'] ?? 0) / ($item['minimum_level'] ?? 1)) * 100) 
                                        : 100;
                                @endphp
                                <div class="h-1.5 rounded-full {{ $percentage > 100 ? 'bg-green-500' : ($percentage > 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                     style="width: {{ min(100, $percentage) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- ApexCharts Scripts -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            let stockChart, turnoverChart, alertsChart;
            
            function initStockLevelsChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const stockData = @json($stockData);
                    
                    const options = {
                        chart: {
                            type: 'bar',
                            height: 320,
                            toolbar: { show: true }
                        },
                        series: [{
                            name: 'Current Stock',
                            data: stockData.map(item => item.current_stock)
                        }, {
                            name: 'Minimum Level',
                            data: stockData.map(item => item.minimum_level)
                        }],
                        xaxis: {
                            categories: stockData.map(item => item.resource_name),
                            labels: {
                                rotate: -45,
                                maxHeight: 60
                            }
                        },
                        colors: ['#3B82F6', '#EF4444'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '70%',
                                grouping: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'top'
                        },
                        grid: {
                            borderColor: '#F3F4F6'
                        }
                    };

                    stockChart = new ApexCharts(document.querySelector("#stock-levels-chart"), options);
                    stockChart.render();
                }
            }

            function initTurnoverChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const turnoverData = @json($turnoverData);
                    
                    const options = {
                        chart: {
                            type: 'line',
                            height: 320,
                            toolbar: { show: true }
                        },
                        series: [{
                            name: 'Turnover Rate',
                            data: turnoverData.map(item => ({
                                x: item.period,
                                y: item.turnover_rate
                            }))
                        }],
                        xaxis: {
                            type: 'datetime'
                        },
                        colors: ['#10B981'],
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

                    turnoverChart = new ApexCharts(document.querySelector("#turnover-chart"), options);
                    turnoverChart.render();
                }
            }

            function initAlertsChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const alertsData = @json($alertsData);
                    
                    const options = {
                        chart: {
                            type: 'donut',
                            height: 320
                        },
                        series: alertsData.map(item => item.count),
                        labels: alertsData.map(item => item.alert_type),
                        colors: ['#EF4444', '#F59E0B', '#8B5CF6', '#06B6D4'],
                        legend: {
                            position: 'bottom'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%'
                                }
                            }
                        }
                    };

                    alertsChart = new ApexCharts(document.querySelector("#alerts-chart"), options);
                    alertsChart.render();
                }
            }

            // Initialize appropriate chart based on view
            if (@this.chartView === 'stock_levels') {
                initStockLevelsChart();
            } else if (@this.chartView === 'turnover') {
                initTurnoverChart();
            } else if (@this.chartView === 'alerts') {
                initAlertsChart();
            }

            // Listen for data refresh
            window.addEventListener('dataRefreshed', function() {
                // Destroy existing charts
                if (stockChart) stockChart.destroy();
                if (turnoverChart) turnoverChart.destroy();
                if (alertsChart) alertsChart.destroy();
                
                // Reinitialize based on current view
                setTimeout(() => {
                    if (@this.chartView === 'stock_levels') {
                        initStockLevelsChart();
                    } else if (@this.chartView === 'turnover') {
                        initTurnoverChart();
                    } else if (@this.chartView === 'alerts') {
                        initAlertsChart();
                    }
                }, 100);
            });
        });
    </script>
</div>
