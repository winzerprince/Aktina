@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Workforce Analytics</h1>
        <p class="text-gray-600 mt-2">Comprehensive workforce metrics and analytics</p>
    </div>

    <!-- Key Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.12l-.654.654a3 3 0 00-.849 2.12V20zm-5-12a4 4 0 110 8 4 4 0 010-8z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Total Employees</h3>
                    <p class="text-2xl font-bold text-blue-600">147</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Active Today</h3>
                    <p class="text-2xl font-bold text-green-600">134</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Avg. Hours/Week</h3>
                    <p class="text-2xl font-bold text-yellow-600">42.5</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Productivity</h3>
                    <p class="text-2xl font-bold text-purple-600">87%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Workforce Distribution Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Workforce Distribution</h2>
        <div id="workforce-chart" class="h-64"></div>
    </div>

    <!-- Department Performance Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Department Performance</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Production</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">92%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">88%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Excellent</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Logistics</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">28</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">89%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">85%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Good</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Quality Control</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">18</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">94%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">91%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Excellent</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Administration</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">21</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">87%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">83%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Average</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Workforce Distribution Chart
    var workforceOptions = {
        series: [{
            name: 'Employees',
            data: [45, 28, 18, 21, 15, 20]
        }],
        chart: {
            type: 'bar',
            height: 250
        },
        xaxis: {
            categories: ['Production', 'Logistics', 'QC', 'Admin', 'Sales', 'HR']
        },
        colors: ['#3B82F6']
    };

    var workforceChart = new ApexCharts(document.querySelector("#workforce-chart"), workforceOptions);
    workforceChart.render();
</script>
@endpush
@endsection
