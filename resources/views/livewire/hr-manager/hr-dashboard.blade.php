<div class="space-y-6" wire:poll.{{ $refreshInterval }}ms>
    <!-- Header with Time Frame Controls -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">HR Dashboard</h1>
        <div class="flex space-x-2">
            <button wire:click="updateTimeframe('7')" 
                    class="px-3 py-1 text-sm rounded-lg {{ $timeframe === '7' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                7 days
            </button>
            <button wire:click="updateTimeframe('30')" 
                    class="px-3 py-1 text-sm rounded-lg {{ $timeframe === '30' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                30 days
            </button>
            <button wire:click="updateTimeframe('90')" 
                    class="px-3 py-1 text-sm rounded-lg {{ $timeframe === '90' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                90 days
            </button>
            <button wire:click="refresh" 
                    class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Employee Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Employees</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($employeeStats['total_employees']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Active Employees</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($employeeStats['active_employees']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">New This Month</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($employeeStats['new_employees_this_month']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-{{ $employeeStats['employee_growth_rate'] >= 0 ? 'green' : 'red' }}-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Growth Rate</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $employeeStats['employee_growth_rate'] >= 0 ? '+' : '' }}{{ $employeeStats['employee_growth_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Workforce Analytics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Workforce Analytics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $workforceAnalytics['productivity_metrics']['orders_per_employee'] }}</div>
                <div class="text-sm text-gray-500">Orders per Employee</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $workforceAnalytics['productivity_metrics']['avg_order_processing_time'] }}</div>
                <div class="text-sm text-gray-500">Avg Processing Time (days)</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $workforceAnalytics['productivity_metrics']['employee_utilization_rate'] }}%</div>
                <div class="text-sm text-gray-500">Employee Utilization</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activity Trends Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Activity Trends</h3>
            <div id="activityTrendsChart"></div>
        </div>

        <!-- Department Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Department Distribution</h3>
            <div id="departmentChart"></div>
        </div>
    </div>

    <!-- Department Performance -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Department Performance</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders Handled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Performance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Efficiency Score</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departmentMetrics as $dept)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $dept['department'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($dept['employee_count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($dept['total_orders_handled']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $dept['avg_performance'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-{{ $dept['efficiency_score'] >= 80 ? 'green' : ($dept['efficiency_score'] >= 60 ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                                             style="width: {{ min($dept['efficiency_score'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $dept['efficiency_score'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Training Needs -->
    @if(count($trainingNeeds) > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Training Needs Assessment</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ count($trainingNeeds) }} employees need training
                </span>
            </div>
            
            <div class="space-y-3">
                @foreach($trainingNeeds as $need)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900">{{ $need['name'] }}</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($need['priority'] === 'High') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $need['priority'] }} Priority
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $need['role'])) }}</div>
                                <div class="text-sm text-gray-700 mt-2">{{ $need['recommended_training'] }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">Score: {{ $need['performance_score'] }}%</div>
                                <div class="w-16 bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-{{ $need['performance_score'] >= 70 ? 'green' : ($need['performance_score'] >= 50 ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                                         style="width: {{ $need['performance_score'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Trends Chart
    const activityData = @json($activityTrends);
    const activityTrendsOptions = {
        series: [{
            name: 'Orders',
            data: activityData.map(item => item.orders_created)
        }, {
            name: 'Resource Orders',
            data: activityData.map(item => item.resource_orders_created)
        }, {
            name: 'New Registrations',
            data: activityData.map(item => item.new_registrations)
        }],
        chart: {
            height: 300,
            type: 'line',
            toolbar: { show: false }
        },
        colors: ['#3B82F6', '#10B981', '#8B5CF6'],
        xaxis: {
            categories: activityData.map(item => new Date(item.date).toLocaleDateString())
        },
        stroke: { curve: 'smooth' },
        legend: { position: 'top' }
    };
    new ApexCharts(document.querySelector("#activityTrendsChart"), activityTrendsOptions).render();

    // Department Distribution Chart
    const deptData = @json($employeeStats['departments']);
    const departmentOptions = {
        series: Object.values(deptData),
        chart: {
            height: 300,
            type: 'donut'
        },
        labels: Object.keys(deptData).map(role => role.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
        colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'],
        legend: { position: 'bottom' }
    };
    new ApexCharts(document.querySelector("#departmentChart"), departmentOptions).render();
});
</script>
@endpush
