<div class="space-y-6">
    <!-- Analytics Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Sales Analytics</h2>
                <p class="text-gray-600">Detailed analysis of sales performance and trends</p>
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
                <button wire:click="exportDetailedReport" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Sales Goal Progress -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Sales Goal Progress</h3>
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-600">Goal Amount:</label>
                <input type="number" wire:model="goalAmount" min="0" step="1000"
                       class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        
        @if(isset($salesData['goal_progress']))
            @php $progress = $salesData['goal_progress']; @endphp
            <div class="space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Current: ${{ number_format($progress['current_revenue']) }}</span>
                    <span class="text-gray-600">Goal: ${{ number_format($progress['goal_amount']) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" 
                         style="width: {{ min(100, $progress['progress_percentage']) }}%"></div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold {{ $progress['progress_percentage'] >= 100 ? 'text-green-600' : 'text-gray-900' }}">
                        {{ number_format($progress['progress_percentage'], 1) }}% Complete
                    </span>
                    @if($progress['remaining_amount'] > 0)
                        <span class="text-sm text-gray-600">
                            ${{ number_format($progress['remaining_amount']) }} remaining
                        </span>
                    @else
                        <span class="text-sm text-green-600 font-medium">Goal achieved! 🎉</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($salesData['total_revenue'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            @if(isset($salesData['growth_rate']))
                <div class="mt-4 flex items-center">
                    @if($salesData['growth_rate'] > 0)
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-green-600 ml-1">+{{ number_format($salesData['growth_rate'], 1) }}%</span>
                    @elseif($salesData['growth_rate'] < 0)
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-red-600 ml-1">{{ number_format($salesData['growth_rate'], 1) }}%</span>
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
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($salesData['order_count'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($salesData['average_order_value'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($salesData['conversion_rate'] ?? 0, 1) }}%</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Performance Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Revenue by Product</h3>
        <div id="product-revenue-chart" style="height: 400px;"></div>
    </div>

    <!-- Retailer Performance Metrics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Retailer Performance Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $performanceMetrics['total_retailers'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Active Retailers</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $performanceMetrics['new_retailers'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">New Retailers</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $performanceMetrics['repeat_customers'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Repeat Customers</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($performanceMetrics['retailer_satisfaction'] ?? 0, 1) }}%</div>
                <div class="text-sm text-gray-600">Satisfaction Score</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    renderProductRevenueChart();
});

Livewire.on('chartDataUpdated', () => {
    renderProductRevenueChart();
});

function renderProductRevenueChart() {
    const productData = @js($revenueByProduct);
    
    if (!productData || productData.length === 0) {
        document.querySelector("#product-revenue-chart").innerHTML = '<div class="flex items-center justify-center h-full text-gray-500">No product data available</div>';
        return;
    }
    
    const sortedData = productData.sort((a, b) => b.revenue - a.revenue).slice(0, 10);
    
    const options = {
        chart: {
            type: 'bar',
            height: 400,
            toolbar: { show: true }
        },
        series: [{
            name: 'Revenue',
            data: sortedData.map(item => item.revenue)
        }],
        xaxis: {
            categories: sortedData.map(item => item.name),
            labels: { 
                rotate: -45,
                style: { fontSize: '12px' }
            }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "$" + val.toLocaleString();
                }
            }
        },
        colors: ['#4F46E5'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '70%'
            }
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$" + val.toLocaleString();
                }
            }
        }
    };
    
    const chart = new ApexCharts(document.querySelector("#product-revenue-chart"), options);
    chart.render();
}
</script>
