<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Production Manager Sales Trends</h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Sales performance analysis for production manager orders</p>
        </div>

        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
            <select wire:model.live="timeRange"
                    class="block w-full sm:w-auto rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm">
                @foreach($this->timeRangeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="date"
                       wire:model.live="startDate"
                       class="block w-full sm:w-auto rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                       placeholder="Start Date">
                <input type="date"
                       wire:model.live="endDate"
                       class="block w-full sm:w-auto rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm"
                       placeholder="End Date">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Total Sales</p>
                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">${{ number_format($this->salesData['total_sales'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">Total Orders</p>
                    <p class="text-lg font-semibold text-green-900 dark:text-green-100">{{ number_format($this->salesData['total_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-800 dark:text-purple-300">Average Order Value</p>
                    <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">${{ number_format($this->salesData['average_order_value'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="h-96 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4" wire:ignore>
        <div id="sales-chart" class="w-full h-full"></div>
    </div>

    <!-- Debug Information (remove after testing) -->
    <div class="mt-4 p-4 bg-gray-100 dark:bg-zinc-700 rounded-lg">
        <h4 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-2">Debug Info:</h4>
        <div class="text-xs text-gray-600 dark:text-zinc-400 space-y-1">
            <div>Data Count: {{ count($this->salesData['data']) }} items</div>
            <div>Total Sales: ${{ number_format($this->salesData['total_sales'], 2) }}</div>
            <div>Time Range: {{ $timeRange }}</div>
            <div>Date Range: {{ $startDate }} to {{ $endDate }}</div>
        </div>
    </div>    @push('scripts')
    <script>
        document.addEventListener('livewire:navigated', function () {
            initializeSalesChart();
        });

        // Also run on initial DOMContentLoaded for direct page loads
        document.addEventListener('DOMContentLoaded', function () {
            initializeSalesChart();
        });

        function initializeSalesChart() {
            console.log('SalesGraph: Initializing chart');

            // Check if ApexCharts is available
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded. Make sure to run "npm run build" or "npm run dev"');
                const container = document.querySelector("#sales-chart");
                if (container) {
                    container.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">ApexCharts not loaded. Run npm run dev.</div>';
                }
                return;
            }

            console.log('ApexCharts is available');
            let chart;

            function renderChart(salesData) {
                console.log('Rendering chart with data:', salesData);

                // Check if container exists
                const container = document.querySelector("#sales-chart");
                if (!container) {
                    console.error('Chart container not found');
                    return;
                }

                // Check if data exists
                if (!salesData || !salesData.data || salesData.data.length === 0) {
                    console.log('No data available for chart');
                    container.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-zinc-400">No sales data available for the selected period.</div>';
                    return;
                }

                // Detect dark mode
                const isDarkMode = document.documentElement.classList.contains('dark');
                console.log('Dark mode:', isDarkMode);

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
                        height: '100%',
                        width: '100%',
                        background: 'transparent',
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
                    theme: {
                        mode: isDarkMode ? 'dark' : 'light'
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
                                fontSize: '12px',
                                colors: isDarkMode ? '#a1a1aa' : '#374151'
                            }
                        },
                        axisBorder: {
                            color: isDarkMode ? '#52525b' : '#e5e7eb'
                        },
                        axisTicks: {
                            color: isDarkMode ? '#52525b' : '#e5e7eb'
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return '$' + new Intl.NumberFormat().format(val);
                            },
                            style: {
                                colors: isDarkMode ? '#a1a1aa' : '#374151'
                            }
                        }
                    },
                    tooltip: {
                        theme: isDarkMode ? 'dark' : 'light',
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
                            colorStops: isDarkMode ? [
                                {
                                    offset: 0,
                                    color: '#60a5fa',
                                    opacity: 0.8
                                },
                                {
                                    offset: 100,
                                    color: '#3b82f6',
                                    opacity: 0.6
                                }
                            ] : [
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
                    colors: [isDarkMode ? '#60a5fa' : '#3B82F6'],
                    grid: {
                        borderColor: isDarkMode ? '#52525b' : '#E5E7EB',
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

                try {
                    if (chart) {
                        console.log('Destroying existing chart');
                        chart.destroy();
                    }

                    console.log('Creating new chart');
                    chart = new ApexCharts(container, options);
                    chart.render().then(() => {
                        console.log('Chart rendered successfully');
                    }).catch((error) => {
                        console.error('Error rendering chart:', error);
                    });
                } catch (error) {
                    console.error('Error creating chart:', error);
                    container.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Error creating chart: ' + error.message + '</div>';
                }
            }

            // Initial render
            const initialData = @json($this->salesData);
            console.log('Initial sales data:', initialData);
            renderChart(initialData);

            // Listen for Livewire updates
            Livewire.on('refreshChart', (salesData) => {
                console.log('Refresh chart event received:', salesData);
                renderChart(salesData);
            });

            // Re-render when time range changes
            @this.on('timeRangeUpdated', (salesData) => {
                console.log('Time range updated event received:', salesData);
                renderChart(salesData);
            });

            // Listen for theme changes (if you have theme switching functionality)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        console.log('Theme changed, re-rendering chart');
                        // Re-render chart when theme changes
                        renderChart(@json($this->salesData));
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        } // End of initializeSalesChart function
    </script>
    @endpush
</div>
