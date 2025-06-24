<x-layouts.app :title="__('Dashboard Components Demo')">
<div class="w-full px-6 py-6 mx-auto">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Components</h1>
            <p class="text-gray-600 dark:text-gray-400">Demonstration of custom UI components</p>
        </div>
        <x-ui.button variant="primary" icon="plus">
            Add New Item
        </x-ui.button>
    </div>

    <!-- Alert Examples -->
    <div class="mb-6">
        <x-ui.alert type="success" title="Success!" dismissible="true" class="mb-4">
            Your data has been successfully saved to the database.
        </x-ui.alert>

        <x-ui.alert type="warning" title="Warning" class="mb-4">
            Please review your settings before proceeding.
        </x-ui.alert>
    </div>

    <!-- Stats Cards Row -->
    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
            <x-ui.stats-card
                title="Total Revenue"
                value="$53,000"
                change="55%"
                changeType="positive"
                icon="money-coins"
                iconBg="success"
            />
        </div>

        <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
            <x-ui.stats-card
                title="Active Users"
                value="2,300"
                change="3%"
                changeType="positive"
                icon="world"
                iconBg="primary"
            />
        </div>

        <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
            <x-ui.stats-card
                title="New Orders"
                value="1,462"
                change="2%"
                changeType="negative"
                icon="paper-diploma"
                iconBg="warning"
            />
        </div>

        <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
            <x-ui.stats-card
                title="Total Sales"
                value="$103,430"
                change="5%"
                changeType="positive"
                icon="cart"
                iconBg="success"
            />
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="flex flex-wrap -mx-3 mb-6">
        <!-- Chart Card -->
        <div class="w-full max-w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
            <x-ui.chart-card
                title="Monthly Sales"
                chartId="salesChart"
                chartType="bar"
                description="(+23%) than last month"
                :stats="[
                    ['label' => 'Orders', 'value' => '36K', 'progress' => ['value' => 60, 'max' => 100]],
                    ['label' => 'Revenue', 'value' => '$2.4M', 'progress' => ['value' => 80, 'max' => 100]],
                    ['label' => 'Customers', 'value' => '1.2K', 'progress' => ['value' => 45, 'max' => 100]],
                    ['label' => 'Growth', 'value' => '12%', 'progress' => ['value' => 70, 'max' => 100]]
                ]"
            />
        </div>

        <!-- Form Card -->
        <div class="w-full max-w-full px-3 lg:w-1/2 lg:flex-none">
            <x-ui.card title="Quick Form" subtitle="Fill out the form below">
                <form>
                    <x-ui.form-input
                        label="Full Name"
                        name="name"
                        placeholder="Enter your full name"
                        icon="single-02"
                        required="true"
                    />

                    <x-ui.form-input
                        label="Email Address"
                        name="email"
                        type="email"
                        placeholder="Enter your email"
                        icon="email-83"
                        required="true"
                    />

                    <x-ui.form-input
                        label="Message"
                        name="message"
                        type="textarea"
                        placeholder="Enter your message"
                    />

                    <x-ui.slider
                        label="Priority Level"
                        name="priority"
                        :min="1"
                        :max="10"
                        :value="5"
                        color="primary"
                    />

                    <div class="flex space-x-4">
                        <x-ui.button variant="primary" type="submit" class="flex-1">
                            Submit Form
                        </x-ui.button>
                        <x-ui.button variant="outline" type="button" class="flex-1">
                            Cancel
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

    <!-- Progress and Status Examples -->
    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full max-w-full px-3 mb-6 lg:mb-0 lg:w-1/2 lg:flex-none">
            <x-ui.card title="Progress Tracking" subtitle="Current project status">
                <div class="space-y-4">
                    <div>
                        <x-ui.progress-bar
                            label="Project Alpha"
                            :value="75"
                            color="success"
                            showPercentage="true"
                        />
                    </div>

                    <div>
                        <x-ui.progress-bar
                            label="Project Beta"
                            :value="45"
                            color="warning"
                            showPercentage="true"
                        />
                    </div>

                    <div>
                        <x-ui.progress-bar
                            label="Project Gamma"
                            :value="90"
                            color="primary"
                            showPercentage="true"
                        />
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="w-full max-w-full px-3 lg:w-1/2 lg:flex-none">
            <x-ui.card title="Status Overview" subtitle="System status indicators">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 dark:text-gray-300">Database</span>
                        <x-ui.status-badge status="online" />
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 dark:text-gray-300">API Service</span>
                        <x-ui.status-badge status="active" />
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 dark:text-gray-300">Cache Server</span>
                        <x-ui.status-badge status="warning" />
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 dark:text-gray-300">Backup System</span>
                        <x-ui.status-badge status="offline" />
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <!-- Data Table -->
    <x-ui.data-table
        title="User Management"
        :headers="['Author', 'Function', 'Status', 'Employed']"
        :rows="[
            [
                [
                    'type' => 'user',
                    'name' => 'John Michael',
                    'email' => 'john@example.com'
                ],
                'Manager<br><small class=\"text-gray-500\">Organization</small>',
                ['type' => 'status', 'status' => 'online'],
                '23/04/18'
            ],
            [
                [
                    'type' => 'user',
                    'name' => 'Alexa Liras',
                    'email' => 'alexa@example.com'
                ],
                'Developer<br><small class=\"text-gray-500\">Engineering</small>',
                ['type' => 'status', 'status' => 'offline'],
                '11/01/19'
            ],
            [
                [
                    'type' => 'user',
                    'name' => 'Laurent Perrier',
                    'email' => 'laurent@example.com'
                ],
                'Executive<br><small class=\"text-gray-500\">Projects</small>',
                ['type' => 'status', 'status' => 'online'],
                '19/09/17'
            ]
        ]"
        searchable="true"
        sortable="true"
        pagination="true"
        :rowActions="['edit', 'delete', 'view']"
    />
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

</x-layouts.app>
