<div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sales Forecast</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">AI-powered prediction for future revenue</p>
            </div>
            <div class="flex items-center space-x-2">
                <select wire:model.live="horizon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="30">30 Days</option>
                    <option value="60">60 Days</option>
                    <option value="90">90 Days</option>
                    <option value="180">180 Days</option>
                </select>
                <button wire:click="loadForecastData" class="p-2 text-blue-500 hover:text-blue-700 rounded-full focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="space-y-6">
            @if($isLoading)
                <div class="flex justify-center items-center h-80">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            @elseif($serviceError)
                <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">ML Service Unavailable</h3>
                            <p class="mt-2 text-sm text-red-700 dark:text-red-300">
                                Unable to connect to the ML microservice. Please try again later or contact support.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif(empty($forecastData['dates']))
                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No Forecast Data Available</h3>
                            <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                There isn't enough sales history to generate a forecast. Add more sales data.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div>
                    <div id="sales-forecast-chart" class="h-80"></div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Forecast Accuracy</h4>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">Medium</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                Based on historical data reliability
                            </p>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Trend</h4>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                @if(count($forecastData['values']) > 1)
                                    @php
                                        $firstValue = $forecastData['values'][0];
                                        $lastValue = $forecastData['values'][count($forecastData['values']) - 1];
                                        $percentChange = $firstValue != 0 ? round((($lastValue - $firstValue) / $firstValue) * 100, 1) : 0;
                                    @endphp
                                    {{ $percentChange > 0 ? '+' : '' }}{{ $percentChange }}%
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                Forecasted change over period
                            </p>
                        </div>

                        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-purple-800 dark:text-purple-200">Peak Revenue Day</h4>
                            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                @if(!empty($forecastData['values']))
                                    @php
                                        $maxIndex = array_search(max($forecastData['values']), $forecastData['values']);
                                        $peakDate = $maxIndex !== false && isset($forecastData['dates'][$maxIndex])
                                            ? \Carbon\Carbon::parse($forecastData['dates'][$maxIndex])->format('M j')
                                            : 'N/A';
                                    @endphp
                                    {{ $peakDate }}
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">
                                Highest projected revenue
                            </p>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('livewire:load', function() {
                        renderSalesForecastChart(@json($forecastData));
                    });

                    document.addEventListener('livewire:update', function() {
                        renderSalesForecastChart(@json($forecastData));
                    });

                    function renderSalesForecastChart(data) {
                        if (!data || !data.dates || !data.values || data.dates.length === 0) {
                            return;
                        }

                        const options = {
                            series: [
                                {
                                    name: 'Forecast',
                                    data: data.values,
                                    type: 'line',
                                },
                                {
                                    name: 'Lower Bound',
                                    data: data.lower,
                                    type: 'area',
                                    fill: 'gradient',
                                },
                                {
                                    name: 'Upper Bound',
                                    data: data.upper,
                                    type: 'area',
                                    fill: 'gradient',
                                }
                            ],
                            chart: {
                                height: 320,
                                type: 'line',
                                fontFamily: 'Inter, sans-serif',
                                toolbar: {
                                    show: true
                                },
                            },
                            colors: ['#3B82F6', '#93C5FD', '#DBEAFE'],
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: [3, 1, 1]
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    opacityFrom: 0.3,
                                    opacityTo: 0.1,
                                }
                            },
                            legend: {
                                position: 'top',
                            },
                            xaxis: {
                                categories: data.dates.map(d => {
                                    return new Date(d).toLocaleDateString('en-US', {
                                        month: 'short',
                                        day: 'numeric'
                                    });
                                }),
                                labels: {
                                    rotate: -45,
                                    rotateAlways: false,
                                },
                                tickAmount: 10
                            },
                            yaxis: {
                                title: {
                                    text: 'Revenue ($)'
                                },
                                labels: {
                                    formatter: function (value) {
                                        return '$' + Number(value).toLocaleString();
                                    }
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function(value) {
                                        return '$' + Number(value).toLocaleString();
                                    }
                                }
                            }
                        };

                        const chart = new ApexCharts(document.querySelector("#sales-forecast-chart"), options);
                        chart.render();
                    }
                </script>
            @endif
        </div>
    </div>
</div>
