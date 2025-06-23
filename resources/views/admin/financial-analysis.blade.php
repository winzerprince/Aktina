<x-layouts.app :title="__('Financial Analysis')">
    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Financial Analysis</h1>
                <p class="text-gray-600 dark:text-gray-400">Comprehensive financial overview and insights</p>
            </div>
            <div class="flex space-x-3">
                <x-ui.button variant="outline" icon="download">
                    Export Report
                </x-ui.button>
                <x-ui.button variant="primary" icon="refresh">
                    Refresh Data
                </x-ui.button>
            </div>
        </div>

        <!-- Key Metrics Row -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Total Revenue"
                    value="$892,450"
                    change="12.5%"
                    changeType="positive"
                    icon="money-coins"
                    iconBg="success"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Net Profit"
                    value="$234,120"
                    change="8.2%"
                    changeType="positive"
                    icon="chart-pie-35"
                    iconBg="primary"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Operating Costs"
                    value="$156,890"
                    change="2.1%"
                    changeType="negative"
                    icon="shop"
                    iconBg="warning"
                />
            </div>

            <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
                <x-ui.stats-card
                    title="ROI"
                    value="24.8%"
                    change="3.4%"
                    changeType="positive"
                    icon="chart-bar-32"
                    iconBg="success"
                />
            </div>
        </div>

        <!-- Charts and Analysis -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- Revenue Trend Chart -->
            <div class="w-full max-w-full px-3 mb-6 lg:mb-0 lg:w-2/3 lg:flex-none">
                <x-ui.chart-card
                    title="Revenue Trend Analysis"
                    chartId="revenueChart"
                    chartType="line"
                    description="12-month revenue performance with trend indicators"
                    :stats="[
                        ['label' => 'Revenue', 'value' => '$892K', 'progress' => ['value' => 85, 'max' => 100]],
                        ['label' => 'Growth', 'value' => '12.5%', 'progress' => ['value' => 78, 'max' => 100]],
                        ['label' => 'Target', 'value' => '$1M', 'progress' => ['value' => 89, 'max' => 100]],
                        ['label' => 'Forecast', 'value' => '$1.2M', 'progress' => ['value' => 92, 'max' => 100]]
                    ]"
                />
            </div>

            <!-- Financial Breakdown -->
            <div class="w-full max-w-full px-3 lg:w-1/3 lg:flex-none">
                <x-ui.card title="Cost Breakdown" subtitle="Operating expenses distribution">
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Personnel</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">$78,450</span>
                            </div>
                            <x-ui.progress-bar :value="65" color="primary" />
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Operations</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">$45,230</span>
                            </div>
                            <x-ui.progress-bar :value="35" color="success" />
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Marketing</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">$23,120</span>
                            </div>
                            <x-ui.progress-bar :value="25" color="warning" />
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Technology</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">$10,090</span>
                            </div>
                            <x-ui.progress-bar :value="15" color="danger" />
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>

        <!-- Financial Performance Table -->
        <x-ui.data-table
            title="Quarterly Financial Performance"
            :headers="['Quarter', 'Revenue', 'Expenses', 'Net Profit', 'Growth %', 'Status']"
            :rows="[
                [
                    'Q4 2023',
                    '$245,650',
                    '$156,890',
                    '$88,760',
                    '+12.5%',
                    ['type' => 'status', 'status' => 'success']
                ],
                [
                    'Q3 2023',
                    '$218,420',
                    '$142,350',
                    '$76,070',
                    '+8.2%',
                    ['type' => 'status', 'status' => 'success']
                ],
                [
                    'Q2 2023',
                    '$201,890',
                    '$138,450',
                    '$63,440',
                    '+5.8%',
                    ['type' => 'status', 'status' => 'warning']
                ],
                [
                    'Q1 2023',
                    '$226,490',
                    '$145,200',
                    '$81,290',
                    '+15.2%',
                    ['type' => 'status', 'status' => 'success']
                ]
            ]"
            :actions="false"
            searchable="true"
            sortable="true"
        />

        <!-- Financial Alerts -->
        <div class="mt-6">
            <x-ui.alert type="success" title="Revenue Target Achieved" dismissible="true" class="mb-4">
                Congratulations! Q4 revenue has exceeded the target by 12.5%. Current performance is tracking above projections.
            </x-ui.alert>

            <x-ui.alert type="warning" title="Cost Analysis Alert" class="mb-4">
                Operating costs have increased by 2.1% this quarter. Review recommended to identify optimization opportunities.
            </x-ui.alert>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-layouts.app>
