<div class="bg-white rounded-lg shadow-sm border p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Production Manager Sales Trends</h3>
            <p class="text-sm text-gray-600 mt-1">Sales performance analysis for production manager orders</p>
        </div>

        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
            <select wire:model.live="timeRange"
                    class="block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($this->timeRangeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="date"
                       wire:model.live="startDate"
                       class="block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       placeholder="Start Date">
                <input type="date"
                       wire:model.live="endDate"
                       class="block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       placeholder="End Date">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">Total Sales</p>
                    <p class="text-lg font-semibold text-blue-900">${{ number_format($this->salesData['total_sales'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Total Orders</p>
                    <p class="text-lg font-semibold text-green-900">{{ number_format($this->salesData['total_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-800">Average Order Value</p>
                    <p class="text-lg font-semibold text-purple-900">${{ number_format($this->salesData['average_order_value'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="h-96" wire:ignore>
        <div id="sales-chart"></div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let chart;

            function renderChart(salesData) {
                const options = {
                    series: [{
                        name: 'Sales',
                        data: salesData.data.map(item => ({
                            x: item.x,
                            y: item.y
                        }))
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: false,
                                reset: true
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            animateGradually: {
                                enabled: true,
                                delay: 150
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        type: 'category',
                        categories: salesData.categories,
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return '$' + new Intl.NumberFormat().format(val);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return '$' + new Intl.NumberFormat().format(val);
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.9,
                            stops: [0, 90, 100],
                            colorStops: [
                                {
                                    offset: 0,
                                    color: '#3B82F6',
                                    opacity: 0.8
                                },
                                {
                                    offset: 100,
                                    color: '#1E40AF',
                                    opacity: 0.6
                                }
                            ]
                        }
                    },
                    colors: ['#3B82F6'],
                    grid: {
                        borderColor: '#E5E7EB',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 300
                            }
                        }
                    }]
                };

                if (chart) {
                    chart.destroy();
                }

                chart = new ApexCharts(document.querySelector("#sales-chart"), options);
                chart.render();
            }

            // Initial render
            renderChart(@json($this->salesData));

            // Listen for Livewire updates
            Livewire.on('refreshChart', (salesData) => {
                renderChart(salesData);
            });

            // Re-render when time range changes
            @this.on('timeRangeUpdated', (salesData) => {
                renderChart(salesData);
            });
        });
    </script>
    @endpush
</div>
