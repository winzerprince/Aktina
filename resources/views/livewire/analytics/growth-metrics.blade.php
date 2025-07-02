<div class="bg-white shadow rounded-lg p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Growth Metrics</h3>
            <p class="mt-1 text-sm text-gray-500">Track business growth and performance trends</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-0">
            <!-- Time Range Filter -->
            <select wire:model.live="timeRange" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="3m">Last 3 Months</option>
                <option value="6m">Last 6 Months</option>
                <option value="12m">Last 12 Months</option>
                <option value="24m">Last 24 Months</option>
            </select>
            
            <!-- Metric Type Filter -->
            <select wire:model.live="metricType" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="users">User Growth</option>
                <option value="orders">Order Growth</option>
                <option value="revenue">Revenue Growth</option>
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

    <!-- Main Growth Metric Card -->
    <div class="bg-gradient-to-r from-{{ $trendDirection === 'up' ? 'green' : 'red' }}-500 to-{{ $trendDirection === 'up' ? 'green' : 'red' }}-600 rounded-lg p-6 text-white mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-{{ $trendDirection === 'up' ? 'green' : 'red' }}-100 text-sm uppercase tracking-wide">
                    {{ ucfirst($metricType) }} Growth
                </p>
                <p class="text-3xl font-bold mt-2">
                    {{ $growthPercentage >= 0 ? '+' : '' }}{{ number_format($growthPercentage, 1) }}%
                </p>
                <p class="text-{{ $trendDirection === 'up' ? 'green' : 'red' }}-100 text-sm mt-1">
                    {{ ucfirst($timeRange) === '3m' ? 'Quarter over quarter' : 'Period over period' }}
                </p>
            </div>
            <div class="text-{{ $trendDirection === 'up' ? 'green' : 'red' }}-200">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($trendDirection === 'up')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    @endif
                </svg>
            </div>
        </div>
        
        <!-- Period Comparison -->
        <div class="mt-4 pt-4 border-t border-{{ $trendDirection === 'up' ? 'green' : 'red' }}-400">
            <div class="flex justify-between text-sm">
                <div>
                    <p class="text-{{ $trendDirection === 'up' ? 'green' : 'red' }}-100">Current Period</p>
                    <p class="font-medium">{{ number_format($currentPeriodTotal) }}</p>
                </div>
                <div>
                    <p class="text-{{ $trendDirection === 'up' ? 'green' : 'red' }}-100">Previous Period</p>
                    <p class="font-medium">{{ number_format($previousPeriodTotal) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- New Users -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">New Users</p>
                    <p class="text-2xl font-bold">{{ number_format($newUsersCount) }}</p>
                </div>
                <div class="text-blue-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- User Retention -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">User Retention</p>
                    <p class="text-2xl font-bold">{{ number_format($userRetentionRate, 1) }}%</p>
                </div>
                <div class="text-purple-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Order Growth -->
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm">Order Growth</p>
                    <p class="text-2xl font-bold">
                        {{ $orderGrowthRate >= 0 ? '+' : '' }}{{ number_format($orderGrowthRate, 1) }}%
                    </p>
                </div>
                <div class="text-amber-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    @if($loading)
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading growth data...</span>
        </div>
    @else
        <!-- Growth Chart -->
        <div class="mb-6">
            <div id="growth-chart" class="h-80"></div>
        </div>

        <!-- Growth Analysis -->
        @if(count($growthData) > 0)
            <div class="border-t pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Growth Analysis</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Growth Insights -->
                    <div class="space-y-3">
                        <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Key Insights</h5>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-{{ $trendDirection === 'up' ? 'green' : 'red' }}-400 rounded-full mr-2"></div>
                                <span class="text-gray-600">
                                    {{ $trendDirection === 'up' ? 'Positive' : 'Negative' }} growth trend over {{ $timeRange }}
                                </span>
                            </div>
                            @if($metricType === 'users')
                                <div class="flex items-center text-sm">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full mr-2"></div>
                                    <span class="text-gray-600">{{ number_format($activeUsersCount) }} active users in current period</span>
                                </div>
                            @endif
                            @if($userRetentionRate > 80)
                                <div class="flex items-center text-sm">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Excellent user retention rate</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Performance Indicators -->
                    <div class="space-y-3">
                        <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Performance</h5>
                        <div class="space-y-2">
                            <!-- User Growth Indicator -->
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">User Growth</span>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" 
                                             style="width: {{ min(100, max(0, $userRetentionRate)) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium">{{ number_format($userRetentionRate, 1) }}%</span>
                                </div>
                            </div>

                            <!-- Order Growth Indicator -->
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Order Growth</span>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-amber-500 h-2 rounded-full" 
                                             style="width: {{ min(100, max(0, abs($orderGrowthRate))) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium">{{ number_format(abs($orderGrowthRate), 1) }}%</span>
                                </div>
                            </div>

                            <!-- Revenue Growth Indicator -->
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Revenue Growth</span>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-green-500 h-2 rounded-full" 
                                             style="width: {{ min(100, max(0, abs($revenueGrowthRate))) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium">{{ number_format(abs($revenueGrowthRate), 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- ApexCharts Script -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            let growthChart;
            
            function initGrowthChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const growthData = @json($growthData);
                    
                    const options = {
                        chart: {
                            type: 'area',
                            height: 320,
                            toolbar: { show: true },
                            zoom: { enabled: true }
                        },
                        series: [{
                            name: @this.metricType.charAt(0).toUpperCase() + @this.metricType.slice(1),
                            data: growthData.map(item => ({
                                x: item.period,
                                y: item.value
                            }))
                        }],
                        xaxis: {
                            type: 'datetime',
                            labels: {
                                format: @this.timeRange.includes('m') ? 'MMM yyyy' : 'MMM dd'
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value).toLocaleString();
                                }
                            }
                        },
                        colors: [@this.trendDirection === 'up' ? '#10B981' : '#EF4444'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.6,
                                opacityTo: 0.1,
                                stops: [0, 90, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        markers: {
                            size: 5,
                            colors: [@this.trendDirection === 'up' ? '#10B981' : '#EF4444'],
                            strokeColors: '#fff',
                            strokeWidth: 2
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
                                    return Math.round(value).toLocaleString();
                                }
                            }
                        }
                    };

                    growthChart = new ApexCharts(document.querySelector("#growth-chart"), options);
                    growthChart.render();
                }
            }

            // Initialize chart
            initGrowthChart();

            // Listen for data refresh
            window.addEventListener('growthDataRefreshed', function() {
                if (growthChart) {
                    growthChart.destroy();
                }
                setTimeout(() => {
                    initGrowthChart();
                }, 100);
            });
        });
    </script>
</div>
