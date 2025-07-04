<x-app-layout>
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    System Performance Monitoring
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    View detailed system performance metrics and alerts
                </p>
            </div>
            <div>
                <a href="{{ route('admin.alert-thresholds') }}" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Manage Alert Thresholds
                </a>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <!-- System CPU Usage Card -->
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                    CPU Usage
                </h4>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-200">
                        <div id="cpu-usage-bar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm">
                        <span id="cpu-usage-value" class="text-gray-800 dark:text-gray-300">0%</span>
                        <span class="text-gray-600 dark:text-gray-400">Threshold: <span id="cpu-threshold">80%</span></span>
                    </div>
                </div>
            </div>

            <!-- System Memory Usage Card -->
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                    Memory Usage
                </h4>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-green-200">
                        <div id="memory-usage-bar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-600 transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm">
                        <span id="memory-usage-value" class="text-gray-800 dark:text-gray-300">0%</span>
                        <span class="text-gray-600 dark:text-gray-400">Threshold: <span id="memory-threshold">85%</span></span>
                    </div>
                </div>
            </div>

            <!-- System Disk Usage Card -->
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                    Disk Usage
                </h4>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-purple-200">
                        <div id="disk-usage-bar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-purple-600 transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm">
                        <span id="disk-usage-value" class="text-gray-800 dark:text-gray-300">0%</span>
                        <span class="text-gray-600 dark:text-gray-400">Threshold: <span id="disk-threshold">90%</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance History Chart -->
        <div class="mt-6">
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                    System Performance History
                </h4>
                <div id="system-performance-chart" class="w-full h-64"></div>
            </div>
        </div>

        <!-- Recent System Performance Alerts -->
        <div class="mt-6">
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                    Recent System Alerts
                </h4>
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Timestamp
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Message
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Value
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Threshold
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" id="alerts-table-body">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No recent alerts
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ApexCharts Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Demo data (replace with actual data from server)
            function updateSystemData() {
                // Simulate CPU usage
                const cpuUsage = Math.floor(Math.random() * 100);
                const cpuThreshold = 80;
                document.getElementById('cpu-usage-bar').style.width = `${cpuUsage}%`;
                document.getElementById('cpu-usage-value').textContent = `${cpuUsage}%`;
                document.getElementById('cpu-threshold').textContent = `${cpuThreshold}%`;
                
                // Simulate Memory usage
                const memoryUsage = Math.floor(Math.random() * 100);
                const memoryThreshold = 85;
                document.getElementById('memory-usage-bar').style.width = `${memoryUsage}%`;
                document.getElementById('memory-usage-value').textContent = `${memoryUsage}%`;
                document.getElementById('memory-threshold').textContent = `${memoryThreshold}%`;
                
                // Simulate Disk usage
                const diskUsage = Math.floor(Math.random() * 100);
                const diskThreshold = 90;
                document.getElementById('disk-usage-bar').style.width = `${diskUsage}%`;
                document.getElementById('disk-usage-value').textContent = `${diskUsage}%`;
                document.getElementById('disk-threshold').textContent = `${diskThreshold}%`;
                
                // Update bar colors based on thresholds
                if (cpuUsage > cpuThreshold) {
                    document.getElementById('cpu-usage-bar').classList.replace('bg-blue-600', 'bg-red-600');
                } else {
                    document.getElementById('cpu-usage-bar').classList.replace('bg-red-600', 'bg-blue-600');
                }
                
                if (memoryUsage > memoryThreshold) {
                    document.getElementById('memory-usage-bar').classList.replace('bg-green-600', 'bg-red-600');
                } else {
                    document.getElementById('memory-usage-bar').classList.replace('bg-red-600', 'bg-green-600');
                }
                
                if (diskUsage > diskThreshold) {
                    document.getElementById('disk-usage-bar').classList.replace('bg-purple-600', 'bg-red-600');
                } else {
                    document.getElementById('disk-usage-bar').classList.replace('bg-red-600', 'bg-purple-600');
                }
            }
            
            // Initialize the system performance chart
            const options = {
                chart: {
                    type: 'line',
                    height: 350,
                    foreColor: '#9ca3af',
                    animations: {
                        enabled: true
                    },
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#3b82f6', '#10b981', '#8b5cf6'],
                series: [{
                    name: 'CPU',
                    data: Array(24).fill(0).map(() => Math.floor(Math.random() * 100))
                }, {
                    name: 'Memory',
                    data: Array(24).fill(0).map(() => Math.floor(Math.random() * 100))
                }, {
                    name: 'Disk',
                    data: Array(24).fill(0).map(() => Math.floor(Math.random() * 100))
                }],
                xaxis: {
                    categories: Array.from({length: 24}, (_, i) => {
                        const hour = new Date().getHours() - 23 + i;
                        return `${(hour < 0 ? 24 + hour : hour).toString().padStart(2, '0')}:00`;
                    })
                },
                yaxis: {
                    title: {
                        text: 'Usage %'
                    },
                    min: 0,
                    max: 100
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                },
                legend: {
                    position: 'top'
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                }
            };
            
            const chart = new ApexCharts(document.querySelector("#system-performance-chart"), options);
            chart.render();
            
            // Update data on page load and then every 5 seconds
            updateSystemData();
            setInterval(updateSystemData, 5000);
        });
    </script>
</x-app-layout>
