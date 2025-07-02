<div class="space-y-6" wire:poll.{{ $refreshInterval }}ms>
    <!-- Header with Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Real-time System Monitoring</h2>
                <p class="text-gray-600">Live system metrics and performance monitoring</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Refresh Interval Selector -->
                <select wire:model.live="refreshInterval" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1000">1 second</option>
                    <option value="3000">3 seconds</option>
                    <option value="5000">5 seconds</option>
                    <option value="10000">10 seconds</option>
                </select>
                
                <!-- Alerts Toggle -->
                <button 
                    wire:click="toggleAlerts"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium {{ $alertsEnabled ? 'text-white bg-red-600 hover:bg-red-700' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM6 2v20l5-5H6V2z"></path>
                    </svg>
                    Alerts {{ $alertsEnabled ? 'ON' : 'OFF' }}
                </button>
                
                <!-- Export Button -->
                <button wire:click="exportMetrics('json')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Metrics
                </button>
            </div>
        </div>
        
        <!-- Metric Toggles -->
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach(['system', 'database', 'cache', 'queue'] as $metric)
                <button 
                    wire:click="toggleMetric('{{ $metric }}')"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ in_array($metric, $activeMetrics) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}"
                >
                    {{ ucfirst($metric) }}
                    @if(in_array($metric, $activeMetrics))
                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- System Alerts -->
    @if($alertsEnabled && $this->systemAlerts)
        <div class="space-y-2">
            @foreach($this->systemAlerts as $alert)
                <div class="bg-white rounded-lg shadow-sm border-l-4 {{ $alert['type'] === 'critical' ? 'border-red-500' : 'border-yellow-500' }} p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($alert['type'] === 'critical')
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</h3>
                                <p class="text-sm text-gray-600">{{ $alert['message'] }}</p>
                            </div>
                        </div>
                        <button 
                            wire:click="acknowledgeAlert('{{ $alert['id'] }}')"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            Acknowledge
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- System Metrics Overview -->
    @if(in_array('system', $activeMetrics) && $this->systemMetrics)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- CPU Usage -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">CPU Usage</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->systemMetrics['cpu']['usage'] }}%</p>
                        <p class="text-xs text-gray-500">Load: {{ number_format($this->systemMetrics['cpu']['load_1min'], 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16">
                            <div id="cpu-gauge" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Memory Usage</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->systemMetrics['memory']['percentage'] }}%</p>
                        <p class="text-xs text-gray-500">{{ $this->formatBytes($this->systemMetrics['memory']['used']) }} / {{ $this->formatBytes($this->systemMetrics['memory']['total']) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16">
                            <div id="memory-gauge" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disk Usage -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Disk Usage</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->systemMetrics['disk']['percentage'] }}%</p>
                        <p class="text-xs text-gray-500">{{ $this->formatBytes($this->systemMetrics['disk']['free']) }} free</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16">
                            <div id="disk-gauge" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Database and Cache Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(in_array('database', $activeMetrics) && $this->databaseMetrics)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Database Performance</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Active Connections</span>
                        <span class="text-sm font-medium">{{ $this->databaseMetrics['connections']['active'] }} / {{ $this->databaseMetrics['connections']['max'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $this->databaseMetrics['connections']['percentage'] }}%"></div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Avg Query Time</span>
                        <span class="text-sm font-medium">{{ $this->databaseMetrics['performance']['avg_query_time'] }}ms</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Slow Queries</span>
                        <span class="text-sm font-medium">{{ $this->databaseMetrics['performance']['slow_queries'] }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(in_array('cache', $activeMetrics) && $this->cacheMetrics)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cache Performance</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Hit Rate</span>
                        <span class="text-sm font-medium">{{ $this->cacheMetrics['hit_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $this->cacheMetrics['hit_rate'] }}%"></div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Memory Usage</span>
                        <span class="text-sm font-medium">{{ $this->cacheMetrics['memory_usage']['percentage'] }}%</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Key Count</span>
                        <span class="text-sm font-medium">{{ number_format($this->cacheMetrics['key_count']) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Performance Timeline Chart -->
    @if($this->performanceTimeline)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Timeline (Last 30 minutes)</h3>
            <div id="performance-timeline-chart" class="h-80"></div>
        </div>
    @endif

    <!-- Queue Metrics and Error Rates -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(in_array('queue', $activeMetrics) && $this->queueMetrics)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Queue Status</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending Jobs</span>
                        <span class="text-sm font-medium">{{ number_format($this->queueMetrics['pending']) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Failed Jobs</span>
                        <span class="text-sm font-medium text-red-600">{{ number_format($this->queueMetrics['failed']) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Processed Today</span>
                        <span class="text-sm font-medium text-green-600">{{ number_format($this->queueMetrics['processed_today']) }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if($this->errorRates)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Error Rates (24h)</h3>
                <div id="error-rates-chart" class="h-48"></div>
            </div>
        @endif
    </div>

    <!-- System Logs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">System Logs</h3>
            <div class="flex gap-2">
                <button 
                    wire:click="toggleLogs"
                    class="text-sm text-blue-600 hover:text-blue-800"
                >
                    {{ $showLogs ? 'Hide' : 'Show' }} Logs
                </button>
                @if($showLogs)
                    <button 
                        wire:click="clearLogs"
                        class="text-sm text-red-600 hover:text-red-800"
                    >
                        Clear Logs
                    </button>
                @endif
            </div>
        </div>
        
        @if($showLogs && $this->recentLogs)
            <div class="max-h-96 overflow-y-auto space-y-2">
                @foreach($this->recentLogs as $log)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            @if($log['level'] === 'error' || $log['level'] === 'critical')
                                <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                            @elseif($log['level'] === 'warning')
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                            @else
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $log['message'] }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log['timestamp'])->diffForHumans() }}</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ ucfirst($log['level']) }} • {{ $log['context'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('livewire:navigated', function () {
    initializeMonitoringCharts();
});

document.addEventListener('DOMContentLoaded', function () {
    initializeMonitoringCharts();
});

function initializeMonitoringCharts() {
    // CPU Gauge
    @if(in_array('system', $activeMetrics) && $this->systemMetrics)
        const cpuGaugeOptions = {
            series: [{{ $this->systemMetrics['cpu']['usage'] }}],
            chart: {
                type: 'radialBar',
                height: 64,
                sparkline: { enabled: true }
            },
            plotOptions: {
                radialBar: {
                    hollow: { size: '50%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            show: true,
                            fontSize: '10px',
                            fontWeight: 'bold'
                        }
                    }
                }
            },
            colors: ['{{ $this->systemMetrics['cpu']['usage'] > 80 ? "#EF4444" : ($this->systemMetrics['cpu']['usage'] > 60 ? "#F59E0B" : "#10B981") }}']
        };
        
        if (window.cpuGauge) window.cpuGauge.destroy();
        window.cpuGauge = new ApexCharts(document.querySelector("#cpu-gauge"), cpuGaugeOptions);
        window.cpuGauge.render();

        // Memory Gauge
        const memoryGaugeOptions = {
            series: [{{ $this->systemMetrics['memory']['percentage'] }}],
            chart: {
                type: 'radialBar',
                height: 64,
                sparkline: { enabled: true }
            },
            plotOptions: {
                radialBar: {
                    hollow: { size: '50%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            show: true,
                            fontSize: '10px',
                            fontWeight: 'bold'
                        }
                    }
                }
            },
            colors: ['{{ $this->systemMetrics['memory']['percentage'] > 80 ? "#EF4444" : ($this->systemMetrics['memory']['percentage'] > 60 ? "#F59E0B" : "#10B981") }}']
        };
        
        if (window.memoryGauge) window.memoryGauge.destroy();
        window.memoryGauge = new ApexCharts(document.querySelector("#memory-gauge"), memoryGaugeOptions);
        window.memoryGauge.render();

        // Disk Gauge
        const diskGaugeOptions = {
            series: [{{ $this->systemMetrics['disk']['percentage'] }}],
            chart: {
                type: 'radialBar',
                height: 64,
                sparkline: { enabled: true }
            },
            plotOptions: {
                radialBar: {
                    hollow: { size: '50%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            show: true,
                            fontSize: '10px',
                            fontWeight: 'bold'
                        }
                    }
                }
            },
            colors: ['{{ $this->systemMetrics['disk']['percentage'] > 80 ? "#EF4444" : ($this->systemMetrics['disk']['percentage'] > 60 ? "#F59E0B" : "#10B981") }}']
        };
        
        if (window.diskGauge) window.diskGauge.destroy();
        window.diskGauge = new ApexCharts(document.querySelector("#disk-gauge"), diskGaugeOptions);
        window.diskGauge.render();
    @endif

    // Performance Timeline Chart
    @if($this->performanceTimeline)
        const timelineOptions = {
            series: @json($this->performanceTimeline['series']),
            chart: {
                type: 'line',
                height: 320,
                animations: { enabled: true }
            },
            xaxis: {
                categories: @json($this->performanceTimeline['categories'])
            },
            colors: ['#3B82F6', '#EF4444', '#F59E0B', '#10B981'],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            legend: {
                position: 'top'
            }
        };
        
        if (window.timelineChart) window.timelineChart.destroy();
        window.timelineChart = new ApexCharts(document.querySelector("#performance-timeline-chart"), timelineOptions);
        window.timelineChart.render();
    @endif

    // Error Rates Chart
    @if($this->errorRates)
        const errorRatesOptions = {
            series: [{
                name: 'Error Rate',
                data: @json(array_column($this->errorRates, 'error_rate'))
            }],
            chart: {
                type: 'area',
                height: 192,
                sparkline: { enabled: true }
            },
            colors: ['#EF4444'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1
                }
            }
        };
        
        if (window.errorRatesChart) window.errorRatesChart.destroy();
        window.errorRatesChart = new ApexCharts(document.querySelector("#error-rates-chart"), errorRatesOptions);
        window.errorRatesChart.render();
    @endif
}

// Livewire listeners
document.addEventListener('livewire:init', () => {
    Livewire.on('metrics-toggled', () => {
        setTimeout(() => initializeMonitoringCharts(), 100);
    });
    
    Livewire.on('refresh-interval-changed', () => {
        setTimeout(() => initializeMonitoringCharts(), 100);
    });
    
    Livewire.on('download-metrics', (event) => {
        const data = event.data;
        const format = event.format;
        const filename = event.filename;
        
        const content = JSON.stringify(data, null, 2);
        downloadFile(content, filename + '.' + format, 'application/json');
    });
});

function downloadFile(content, filename, contentType) {
    const blob = new Blob([content], { type: contentType });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
</script>
@endpush

@php
    // Helper method for formatting bytes
    if (!function_exists('formatBytes')) {
        function formatBytes($size) {
            $units = ['B', 'KB', 'MB', 'GB'];
            for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
                $size /= 1024;
            }
            return round($size, 2) . ' ' . $units[$i];
        }
    }
@endphp
