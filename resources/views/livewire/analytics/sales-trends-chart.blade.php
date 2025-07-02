<div class="bg-white shadow rounded-lg p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Sales Trends</h3>
            <p class="mt-1 text-sm text-gray-500">Track sales performance over time</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-0">
            <!-- Time Range Filter -->
            <select wire:model.live="timeRange" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
                <option value="1y">Last Year</option>
            </select>
            
            <!-- Chart Type Filter -->
            <select wire:model.live="chartType" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="line">Line Chart</option>
                <option value="bar">Bar Chart</option>
                <option value="area">Area Chart</option>
            </select>
            
            <!-- Export Button -->
            <button wire:click="exportChart" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Sales</p>
                    <p class="text-2xl font-bold">${{ number_format($totalSales, 2) }}</p>
                </div>
                <div class="text-blue-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Growth Rate</p>
                    <p class="text-2xl font-bold">
                        {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}%
                    </p>
                </div>
                <div class="text-green-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($salesGrowth >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        @endif
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Top Products</p>
                    <p class="text-2xl font-bold">{{ count($topProducts) }}</p>
                </div>
                <div class="text-purple-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    @if($loading)
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading sales data...</span>
        </div>
    @else
        <!-- Chart Container -->
        <div class="mb-6">
            <div id="sales-trends-chart" class="h-80"></div>
        </div>

        <!-- Top Products -->
        @if(count($topProducts) > 0)
            <div class="border-t pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Top Selling Products</h4>
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $product['name'] ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $product['category'] ?? 'Uncategorized' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($product['total_sales'] ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($product['quantity_sold'] ?? 0) }} units</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- ApexCharts Script -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            let chart;
            
            function initChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const salesData = @json($salesData);
                    
                    const options = {
                        chart: {
                            type: @this.chartType,
                            height: 320,
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    selection: true,
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                }
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        series: [{
                            name: 'Sales',
                            data: salesData.map(item => ({
                                x: item.date,
                                y: item.total_sales
                            }))
                        }],
                        xaxis: {
                            type: 'datetime',
                            labels: {
                                format: 'MMM dd'
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        colors: ['#3B82F6'],
                        fill: {
                            type: @this.chartType === 'area' ? 'gradient' : 'solid',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.9,
                                stops: [0, 90, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        grid: {
                            borderColor: '#F3F4F6'
                        },
                        tooltip: {
                            x: {
                                format: 'MMM dd, yyyy'
                            },
                            y: {
                                formatter: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    };

                    chart = new ApexCharts(document.querySelector("#sales-trends-chart"), options);
                    chart.render();
                }
            }

            // Initialize chart
            initChart();

            // Listen for chart updates
            window.addEventListener('updateChart', function(event) {
                if (chart) {
                    chart.updateOptions({
                        chart: {
                            type: event.detail.type
                        }
                    });
                }
            });

            // Reinitialize chart when data changes
            Livewire.on('salesDataUpdated', () => {
                if (chart) {
                    chart.destroy();
                }
                setTimeout(() => {
                    initChart();
                }, 100);
            });
        });
    </script>
</div>
