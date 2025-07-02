<div class="space-y-6">
    <!-- Analytics Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Production Analytics</h2>
                <p class="text-gray-600">Detailed analysis of production performance and metrics</p>
            </div>
            <div class="flex flex-col md:flex-row gap-4 mt-4 md:mt-0">
                <div>
                    <select wire:model="selectedPeriod" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($periods as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model="selectedMetric" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($metrics as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="exportAnalytics" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overall Efficiency</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($kpiMetrics['overall_efficiency'] ?? 0, 1) }}%</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            @if(isset($comparisonData['current']['efficiency']) && isset($comparisonData['previous']['efficiency']))
                @php
                    $change = $comparisonData['current']['efficiency'] - $comparisonData['previous']['efficiency'];
                @endphp
                <div class="mt-4 flex items-center">
                    @if($change > 0)
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-green-600 ml-1">+{{ number_format($change, 1) }}%</span>
                    @elseif($change < 0)
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-red-600 ml-1">{{ number_format($change, 1) }}%</span>
                    @else
                        <span class="text-sm text-gray-500">No change</span>
                    @endif
                    <span class="text-sm text-gray-500 ml-2">from previous period</span>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Average Throughput</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($kpiMetrics['avg_throughput'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500">units/day</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Quality Score</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($kpiMetrics['quality_score'] ?? 0, 1) }}%</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Downtime</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($kpiMetrics['total_downtime'] ?? 0, 1) }}</p>
                    <p class="text-xs text-gray-500">hours</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Cost per Unit</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($kpiMetrics['cost_per_unit'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">On-Time Delivery</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($kpiMetrics['on_time_delivery'] ?? 0, 1) }}%</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">{{ $metrics[$selectedMetric] }} Analysis</h3>
        <div id="main-analytics-chart" style="height: 400px;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    renderAnalyticsChart();
});

Livewire.on('chartDataUpdated', () => {
    renderAnalyticsChart();
});

function renderAnalyticsChart() {
    const chartData = @js($chartData);
    
    if (!chartData.series || chartData.series.length === 0) {
        return;
    }

    let options = {
        chart: {
            height: 400,
            toolbar: { show: true },
            zoom: { enabled: true }
        },
        series: chartData.series,
        colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
        stroke: { 
            curve: 'smooth', 
            width: chartData.type === 'line' ? 3 : 1 
        },
        markers: { 
            size: chartData.type === 'line' ? 6 : 0 
        },
        legend: { 
            position: 'top',
            horizontalAlign: 'left'
        },
        tooltip: {
            shared: true,
            intersect: false
        }
    };

    // Configure chart type specific options
    if (chartData.type === 'donut') {
        options.chart.type = 'donut';
        options.labels = chartData.labels;
        options.plotOptions = {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        };
        delete options.xaxis;
        delete options.stroke;
        delete options.markers;
    } else {
        options.chart.type = chartData.type || 'line';
        options.xaxis = {
            categories: chartData.categories || [],
            labels: {
                rotate: -45
            }
        };
        
        if (chartData.type === 'column') {
            options.plotOptions = {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%'
                }
            };
        }
    }

    const chart = new ApexCharts(document.querySelector("#main-analytics-chart"), options);
    chart.render();
}
</script>
