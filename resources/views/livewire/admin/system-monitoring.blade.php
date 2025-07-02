<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900">System Monitoring</h3>
            <p class="mt-1 text-sm text-gray-500">Real-time system performance and health monitoring</p>
        </div>
        <div class="flex space-x-3 mt-4 sm:mt-0">
            <button wire:click="refreshData" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
            <button wire:click="exportMetrics" 
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- System Health Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- CPU Usage -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">CPU Usage</p>
                    <p class="text-2xl font-bold text-{{ $cpuUsage > 80 ? 'red' : ($cpuUsage > 60 ? 'yellow' : 'green') }}-600">
                        {{ number_format($cpuUsage, 1) }}%
                    </p>
                </div>
                <div class="p-3 rounded-full bg-{{ $cpuUsage > 80 ? 'red' : ($cpuUsage > 60 ? 'yellow' : 'green') }}-100">
                    <svg class="w-6 h-6 text-{{ $cpuUsage > 80 ? 'red' : ($cpuUsage > 60 ? 'yellow' : 'green') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $cpuUsage > 80 ? 'red' : ($cpuUsage > 60 ? 'yellow' : 'green') }}-500 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $cpuUsage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Memory Usage -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Memory Usage</p>
                    <p class="text-2xl font-bold text-{{ $memoryUsage > 85 ? 'red' : ($memoryUsage > 70 ? 'yellow' : 'green') }}-600">
                        {{ number_format($memoryUsage, 1) }}%
                    </p>
                </div>
                <div class="p-3 rounded-full bg-{{ $memoryUsage > 85 ? 'red' : ($memoryUsage > 70 ? 'yellow' : 'green') }}-100">
                    <svg class="w-6 h-6 text-{{ $memoryUsage > 85 ? 'red' : ($memoryUsage > 70 ? 'yellow' : 'green') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $memoryUsage > 85 ? 'red' : ($memoryUsage > 70 ? 'yellow' : 'green') }}-500 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $memoryUsage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Disk Usage -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Disk Usage</p>
                    <p class="text-2xl font-bold text-{{ $diskUsage > 90 ? 'red' : ($diskUsage > 75 ? 'yellow' : 'green') }}-600">
                        {{ number_format($diskUsage, 1) }}%
                    </p>
                </div>
                <div class="p-3 rounded-full bg-{{ $diskUsage > 90 ? 'red' : ($diskUsage > 75 ? 'yellow' : 'green') }}-100">
                    <svg class="w-6 h-6 text-{{ $diskUsage > 90 ? 'red' : ($diskUsage > 75 ? 'yellow' : 'green') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $diskUsage > 90 ? 'red' : ($diskUsage > 75 ? 'yellow' : 'green') }}-500 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $diskUsage }}%"></div>
                </div>
            </div>
        </div>

        <!-- System Load -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">System Load</p>
                    <p class="text-2xl font-bold text-{{ $systemLoad > 3 ? 'red' : ($systemLoad > 2 ? 'yellow' : 'green') }}-600">
                        {{ number_format($systemLoad, 2) }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-{{ $systemLoad > 3 ? 'red' : ($systemLoad > 2 ? 'yellow' : 'green') }}-100">
                    <svg class="w-6 h-6 text-{{ $systemLoad > 3 ? 'red' : ($systemLoad > 2 ? 'yellow' : 'green') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-500">Load Average (1min)</p>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- CPU & Memory Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">CPU & Memory Trends</h4>
            <div id="performance-chart" class="h-64"></div>
        </div>

        <!-- Network Traffic Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Network Traffic</h4>
            <div id="network-chart" class="h-64"></div>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($alerts) > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">System Alerts</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($alerts as $alert)
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($alert['severity'] === 'critical')
                            <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                        @elseif($alert['severity'] === 'warning')
                            <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                        @else
                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</h4>
                        <p class="text-sm text-gray-500">{{ $alert['message'] }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500">{{ $alert['time'] }}</span>
                    <button wire:click="dismissAlert('{{ $alert['id'] }}')" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- System Logs -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Recent System Logs</h3>
            <select wire:model.live="logLevel" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="all">All Levels</option>
                <option value="error">Errors</option>
                <option value="warning">Warnings</option>
                <option value="info">Info</option>
            </select>
        </div>
        <div class="max-h-64 overflow-y-auto">
            @if(count($logs) > 0)
                @foreach($logs as $log)
                <div class="px-6 py-3 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $log['level'] === 'error' ? 'bg-red-100 text-red-800' : 
                                   ($log['level'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($log['level']) }}
                            </span>
                            <div>
                                <p class="text-sm text-gray-900">{{ $log['message'] }}</p>
                                @if(isset($log['context']))
                                <p class="text-xs text-gray-500 mt-1">{{ $log['context'] }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $log['timestamp'] }}</span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-2 text-sm">No logs available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ApexCharts Scripts -->
    <script>
        document.addEventListener('livewire:navigated', function () {
            let performanceChart, networkChart;
            
            function initCharts() {
                if (typeof ApexCharts !== 'undefined') {
                    // Performance Chart
                    const performanceOptions = {
                        chart: {
                            type: 'line',
                            height: 256,
                            toolbar: { show: false },
                            animations: { enabled: true, easing: 'linear', speed: 800 }
                        },
                        series: [
                            {
                                name: 'CPU %',
                                data: @json($performanceData['cpu'] ?? [])
                            },
                            {
                                name: 'Memory %',
                                data: @json($performanceData['memory'] ?? [])
                            }
                        ],
                        xaxis: {
                            categories: @json($performanceData['timestamps'] ?? []),
                            labels: { style: { fontSize: '12px' } }
                        },
                        yaxis: {
                            min: 0,
                            max: 100,
                            labels: { style: { fontSize: '12px' } }
                        },
                        colors: ['#3B82F6', '#8B5CF6'],
                        stroke: { width: 2, curve: 'smooth' },
                        grid: { strokeDashArray: 3 },
                        legend: { position: 'top', horizontalAlign: 'right' }
                    };

                    // Network Chart
                    const networkOptions = {
                        chart: {
                            type: 'area',
                            height: 256,
                            toolbar: { show: false },
                            animations: { enabled: true, easing: 'linear', speed: 800 }
                        },
                        series: [
                            {
                                name: 'Download (MB/s)',
                                data: @json($networkData['download'] ?? [])
                            },
                            {
                                name: 'Upload (MB/s)',
                                data: @json($networkData['upload'] ?? [])
                            }
                        ],
                        xaxis: {
                            categories: @json($networkData['timestamps'] ?? []),
                            labels: { style: { fontSize: '12px' } }
                        },
                        yaxis: {
                            labels: { style: { fontSize: '12px' } }
                        },
                        colors: ['#10B981', '#F59E0B'],
                        fill: { opacity: 0.3 },
                        stroke: { width: 2 },
                        grid: { strokeDashArray: 3 },
                        legend: { position: 'top', horizontalAlign: 'right' }
                    };

                    if (document.getElementById('performance-chart')) {
                        performanceChart = new ApexCharts(document.getElementById('performance-chart'), performanceOptions);
                        performanceChart.render();
                    }

                    if (document.getElementById('network-chart')) {
                        networkChart = new ApexCharts(document.getElementById('network-chart'), networkOptions);
                        networkChart.render();
                    }
                }
            }

            // Initialize charts
            initCharts();

            // Update charts on refresh
            Livewire.on('dataRefreshed', () => {
                if (performanceChart) performanceChart.destroy();
                if (networkChart) networkChart.destroy();
                setTimeout(initCharts, 100);
            });
        });

        // Auto-refresh every 30 seconds
        setInterval(() => {
            @this.call('refreshData');
        }, 30000);
    </script>
</div>
