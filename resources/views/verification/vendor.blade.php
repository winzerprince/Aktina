<x-layouts.app :title="__('Vendor Application')">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome to Aktina SCM</h1>
                <p class="text-lg text-gray-600">Complete your vendor application to access the platform</p>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-8">
                <nav class="flex space-x-8 border-b border-gray-200">
                    <button
                        id="tab-application"
                        class="tab-button py-2 px-1 border-b-2 font-medium text-sm active"
                        onclick="switchTab('application')"
                    >
                        Application
                    </button>
                    <button
                        id="tab-welcome"
                        class="tab-button py-2 px-1 border-b-2 font-medium text-sm"
                        onclick="switchTab('welcome')"
                    >
                        Welcome & Instructions
                    </button>
                </nav>
            </div>

            <!-- Application Tab Content -->
            <div id="content-application" class="tab-content">
                <livewire:verification.vendor-application />
            </div>

            <!-- Welcome Tab Content -->
            <div id="content-welcome" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-900">Welcome to Aktina Vendor Program</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-gray-800">Application Process</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                                <li>Submit your vendor application with required documents</li>
                                <li>Our system will process and score your application</li>
                                <li>Admin team will review and schedule a meeting</li>
                                <li>After successful meeting, you'll receive approval</li>
                                <li>Once approved, you can access all vendor features</li>
                            </ol>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-gray-800">Required Documents</h3>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>Business Registration Certificate</li>
                                <li>Tax Identification Documents</li>
                                <li>Bank Statements (minimum $1M balance preferred)</li>
                                <li>Company Profile and Capabilities</li>
                                <li>Previous Partnership References</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-gray-800">What You'll Gain Access To</h3>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>Vendor Dashboard with Analytics</li>
                                <li>Order Management System</li>
                                <li>Retailer Network Access</li>
                                <li>Inventory Management Tools</li>
                                <li>Sales Reporting and Insights</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .tab-button {
            @apply text-gray-500 border-transparent;
        }
        .tab-button.active {
            @apply text-blue-600 border-blue-600;
        }
        .tab-button:hover {
            @apply text-gray-700 border-gray-300;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab
            document.getElementById('tab-' + tabName).classList.add('active');
        }
    </script>
    @endpush
</x-layouts.app>
