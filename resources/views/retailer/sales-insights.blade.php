<x-layouts.app :title="__('Sales Insights')">
    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Sales Insights</h1>
                <p class="text-gray-600 dark:text-gray-400">Track your sales performance and customer analytics</p>
            </div>
            <div class="flex space-x-3">
                <x-ui.form-input
                    type="select"
                    name="period"
                    class="!mb-0"
                >
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="quarter">This Quarter</option>
                </x-ui.form-input>
                <x-ui.button variant="primary" icon="chart-bar-32">
                    Generate Report
                </x-ui.button>
            </div>
        </div>

        <!-- Sales Metrics -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Today's Sales"
                    value="$12,450"
                    change="18%"
                    changeType="positive"
                    icon="cart"
                    iconBg="success"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Orders"
                    value="156"
                    change="12%"
                    changeType="positive"
                    icon="paper-diploma"
                    iconBg="primary"
                />
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <x-ui.stats-card
                    title="Avg. Order Value"
                    value="$89.50"
                    change="5%"
                    changeType="positive"
                    icon="money-coins"
                    iconBg="warning"
                />
            </div>

            <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
                <x-ui.stats-card
                    title="Conversion Rate"
                    value="3.2%"
                    change="0.8%"
                    changeType="positive"
                    icon="chart-pie-35"
                    iconBg="success"
                />
            </div>
        </div>

        <!-- Charts Section -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <!-- Sales Chart -->
            <div class="w-full max-w-full px-3 mb-6 lg:mb-0 lg:w-2/3 lg:flex-none">
                <x-ui.chart-card
                    title="Sales Performance"
                    chartId="salesChart"
                    chartType="bar"
                    description="Daily sales for the current month"
                    :stats="[
                        ['label' => 'Sales', 'value' => '$12.4K', 'progress' => ['value' => 78, 'max' => 100]],
                        ['label' => 'Orders', 'value' => '156', 'progress' => ['value' => 65, 'max' => 100]],
                        ['label' => 'Customers', 'value' => '89', 'progress' => ['value' => 45, 'max' => 100]],
                        ['label' => 'Returns', 'value' => '3', 'progress' => ['value' => 12, 'max' => 100]]
                    ]"
                />
            </div>

            <!-- Top Products -->
            <div class="w-full max-w-full px-3 lg:w-1/3 lg:flex-none">
                <x-ui.card title="Top Selling Products" subtitle="Best performers this month">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h6 class="font-semibold text-gray-800 dark:text-white">Premium Widget</h6>
                                <p class="text-sm text-gray-600 dark:text-gray-400">45 units sold</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-[#008800]">$2,250</span>
                                <br>
                                <x-ui.status-badge status="success" size="xs">Hot</x-ui.status-badge>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h6 class="font-semibold text-gray-800 dark:text-white">Standard Kit</h6>
                                <p class="text-sm text-gray-600 dark:text-gray-400">32 units sold</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-[#008800]">$1,920</span>
                                <br>
                                <x-ui.status-badge status="success" size="xs">Trending</x-ui.status-badge>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h6 class="font-semibold text-gray-800 dark:text-white">Basic Package</h6>
                                <p class="text-sm text-gray-600 dark:text-gray-400">28 units sold</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-[#008800]">$840</span>
                                <br>
                                <x-ui.status-badge status="warning" size="xs">Stable</x-ui.status-badge>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>

        <!-- Recent Orders & Customer Insights -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full max-w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
                <x-ui.data-table
                    title="Recent Orders"
                    :headers="['Order ID', 'Customer', 'Amount', 'Status']"
                    :rows="[
                        [
                            '#ORD-1234',
                            [
                                'type' => 'user',
                                'name' => 'John Smith',
                                'email' => 'john@example.com'
                            ],
                            '$125.50',
                            ['type' => 'status', 'status' => 'success']
                        ],
                        [
                            '#ORD-1235',
                            [
                                'type' => 'user',
                                'name' => 'Sarah Johnson',
                                'email' => 'sarah@example.com'
                            ],
                            '$89.00',
                            ['type' => 'status', 'status' => 'pending']
                        ],
                        [
                            '#ORD-1236',
                            [
                                'type' => 'user',
                                'name' => 'Mike Brown',
                                'email' => 'mike@example.com'
                            ],
                            '$67.25',
                            ['type' => 'status', 'status' => 'success']
                        ]
                    ]"
                    :actions="true"
                    :rowActions="['view', 'edit']"
                />
            </div>

            <div class="w-full max-w-full px-3 lg:w-1/2 lg:flex-none">
                <x-ui.card title="Customer Analytics" subtitle="Customer behavior insights">
                    <div class="space-y-4">
                        <div>
                            <x-ui.progress-bar
                                label="New Customers (23)"
                                :value="68"
                                color="success"
                                showPercentage="true"
                            />
                        </div>

                        <div>
                            <x-ui.progress-bar
                                label="Returning Customers (89)"
                                :value="85"
                                color="primary"
                                showPercentage="true"
                            />
                        </div>

                        <div>
                            <x-ui.progress-bar
                                label="Customer Satisfaction"
                                :value="92"
                                color="success"
                                showPercentage="true"
                            />
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                            <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Purchase Frequency</h6>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="text-lg font-bold text-[#008800]">45%</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Weekly</div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="text-lg font-bold text-[#008800]">35%</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Monthly</div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="text-lg font-bold text-[#008800]">20%</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Occasional</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>

        <!-- Performance Goals -->
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3">
                <x-ui.card title="Monthly Goals Progress" subtitle="Track your sales targets">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-ui.slider
                                label="Sales Target: $15,000"
                                name="sales_goal"
                                :min="0"
                                :max="15000"
                                :value="12450"
                                color="success"
                                showValue="false"
                            />
                            <div class="flex justify-between text-sm mt-2">
                                <span class="text-gray-600 dark:text-gray-400">Current: $12,450</span>
                                <span class="font-semibold text-[#008800]">83%</span>
                            </div>
                        </div>

                        <div>
                            <x-ui.slider
                                label="Orders Target: 200"
                                name="orders_goal"
                                :min="0"
                                :max="200"
                                :value="156"
                                color="primary"
                                showValue="false"
                            />
                            <div class="flex justify-between text-sm mt-2">
                                <span class="text-gray-600 dark:text-gray-400">Current: 156</span>
                                <span class="font-semibold text-[#008800]">78%</span>
                            </div>
                        </div>

                        <div>
                            <x-ui.slider
                                label="Customer Target: 100"
                                name="customers_goal"
                                :min="0"
                                :max="100"
                                :value="89"
                                color="warning"
                                showValue="false"
                            />
                            <div class="flex justify-between text-sm mt-2">
                                <span class="text-gray-600 dark:text-gray-400">Current: 89</span>
                                <span class="font-semibold text-[#008800]">89%</span>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            </div>
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
