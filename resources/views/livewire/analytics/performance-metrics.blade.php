<div class="bg-white shadow rounded-lg p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Performance Metrics</h3>
            <p class="mt-1 text-sm text-gray-500">Track role-specific KPIs and performance indicators</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-0">
            <!-- Role Filter -->
            <select wire:model.live="selectedRole" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="current">My Performance</option>
                <option value="admin">Admin Overview</option>
                <option value="vendor">Vendor Performance</option>
                <option value="retailer">Retailer Performance</option>
                <option value="production_manager">Production Performance</option>
                <option value="supplier">Supplier Performance</option>
            </select>
            
            <!-- Time Range Filter -->
            <select wire:model.live="timeRange" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
                <option value="1y">Last Year</option>
            </select>
            
            <!-- Export Button -->
            <button wire:click="exportPerformanceReport" 
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

    <!-- Overall Performance Score -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-indigo-100 text-sm uppercase tracking-wide">Overall Performance Score</p>
                <p class="text-4xl font-bold mt-2">{{ $overallScore }}/100</p>
                <p class="text-indigo-100 text-sm mt-1">
                    @if($overallScore >= 90)
                        Excellent Performance
                    @elseif($overallScore >= 80)
                        Good Performance
                    @elseif($overallScore >= 70)
                        Average Performance
                    @else
                        Needs Improvement
                    @endif
                </p>
            </div>
            <div class="text-indigo-200">
                <!-- Performance Ring -->
                <div class="relative w-24 h-24">
                    <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                        <path d="M18 2.0845a15.9155 15.9155 0 0 1 0 31.831a15.9155 15.9155 0 0 1 0-31.831"
                              fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                        <path d="M18 2.0845a15.9155 15.9155 0 0 1 0 31.831a15.9155 15.9155 0 0 1 0-31.831"
                              fill="none" stroke="white" stroke-width="2"
                              stroke-dasharray="{{ $overallScore }}, 100"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-white text-sm font-bold">{{ $overallScore }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    @if($loading)
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading performance data...</span>
        </div>
    @else
        <!-- KPI Cards -->
        @if(count($kpiData) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($kpiData as $kpi)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-900">{{ $kpi['name'] ?? 'KPI' }}</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if(($kpi['score'] ?? 0) >= ($kpi['target'] ?? 100))
                                    bg-green-100 text-green-800
                                @elseif(($kpi['score'] ?? 0) >= ($kpi['target'] ?? 100) * 0.8)
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-red-100 text-red-800
                                @endif
                            ">
                                @if(($kpi['score'] ?? 0) >= ($kpi['target'] ?? 100))
                                    On Target
                                @elseif(($kpi['score'] ?? 0) >= ($kpi['target'] ?? 100) * 0.8)
                                    Close
                                @else
                                    Below Target
                                @endif
                            </span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Current</span>
                                <span class="font-medium text-gray-900">{{ number_format($kpi['current'] ?? 0, 1) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Target</span>
                                <span class="font-medium text-gray-900">{{ number_format($kpi['target'] ?? 0, 1) }}</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $progress = ($kpi['target'] ?? 0) > 0 ? min(100, (($kpi['current'] ?? 0) / ($kpi['target'] ?? 1)) * 100) : 0;
                                @endphp
                                <div class="h-2 rounded-full {{ $progress >= 100 ? 'bg-green-500' : ($progress >= 80 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                     style="width: {{ $progress }}%"></div>
                            </div>
                            
                            <div class="text-xs text-gray-500 text-center">
                                {{ number_format($progress, 1) }}% of target
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Performance Chart -->
        <div class="mb-6">
            <div id="performance-chart" class="h-80"></div>
        </div>

        <!-- Insights Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Achievements -->
            @if(count($achievements) > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Achievements
                    </h4>
                    <div class="space-y-3">
                        @foreach($achievements as $achievement)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $achievement['name'] }}</p>
                                    <p class="text-xs text-gray-500">Target exceeded</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-green-600">{{ number_format($achievement['current'], 1) }}</p>
                                    <p class="text-xs text-gray-500">vs {{ number_format($achievement['target'], 1) }} target</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Improvement Areas -->
            @if(count($improvementAreas) > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        Improvement Areas
                    </h4>
                    <div class="space-y-3">
                        @foreach($improvementAreas as $area)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $area['name'] }}</p>
                                    <p class="text-xs text-gray-500">Below target</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-red-600">{{ number_format($area['current'], 1) }}</p>
                                    <p class="text-xs text-gray-500">vs {{ number_format($area['target'], 1) }} target</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- ApexCharts Script -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            let performanceChart;
            
            function initPerformanceChart() {
                if (typeof ApexCharts !== 'undefined') {
                    const kpiData = @json($kpiData);
                    
                    const options = {
                        chart: {
                            type: 'radar',
                            height: 320,
                            toolbar: { show: true }
                        },
                        series: [{
                            name: 'Current Performance',
                            data: kpiData.map(item => item.score || 0)
                        }, {
                            name: 'Target',
                            data: kpiData.map(item => item.target || 100)
                        }],
                        labels: kpiData.map(item => item.name || 'KPI'),
                        colors: ['#3B82F6', '#10B981'],
                        plotOptions: {
                            radar: {
                                size: 140,
                                polygons: {
                                    strokeColor: '#e9e9e9',
                                    fill: {
                                        colors: ['#f8f8f8', '#fff']
                                    }
                                }
                            }
                        },
                        stroke: {
                            width: 2
                        },
                        fill: {
                            opacity: 0.1
                        },
                        markers: {
                            size: 4
                        },
                        legend: {
                            position: 'bottom'
                        },
                        yaxis: {
                            min: 0,
                            max: 100
                        }
                    };

                    performanceChart = new ApexCharts(document.querySelector("#performance-chart"), options);
                    performanceChart.render();
                }
            }

            // Initialize chart
            initPerformanceChart();

            // Listen for data refresh
            window.addEventListener('performanceDataRefreshed', function() {
                if (performanceChart) {
                    performanceChart.destroy();
                }
                setTimeout(() => {
                    initPerformanceChart();
                }, 100);
            });
        });
    </script>
</div>
