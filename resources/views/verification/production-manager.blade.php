<x-layouts.auth title="Production Manager Verification">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/aktina-logo.svg') }}" alt="Aktina" class="h-12 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-gray-900">Welcome, Production Manager!</h1>
                <p class="mt-2 text-gray-600">Your account is being reviewed by our admin team</p>
            </div>

            <!-- Welcome Content -->
            <div class="bg-white rounded-lg shadow border p-8">
                <div class="text-center mb-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center justify-center mb-4">
                            <svg class="h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-blue-800 mb-2">Account Under Review</h3>
                        <p class="text-blue-700">Your production manager account is currently being reviewed by our admin team. You'll receive access once approved.</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">About Aktina Production Management</h2>
                        <p class="text-gray-600 mb-4">
                            As a production manager in the Aktina platform, you'll oversee manufacturing processes, manage production schedules, and coordinate with suppliers and vendors to ensure efficient operations.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">What You'll Have Access To</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Production Planning</h4>
                                <p class="text-sm text-gray-600">Plan and schedule production runs, manage capacity, and optimize manufacturing workflows.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Resource Management</h4>
                                <p class="text-sm text-gray-600">Track raw materials, equipment utilization, and workforce allocation across production lines.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Quality Control</h4>
                                <p class="text-sm text-gray-600">Monitor production quality, manage inspection processes, and maintain quality standards.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Performance Analytics</h4>
                                <p class="text-sm text-gray-600">Access real-time production metrics, efficiency reports, and performance dashboards.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Supply Chain Coordination</h4>
                                <p class="text-sm text-gray-600">Coordinate with suppliers for raw materials and manage inventory levels for production.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Order Fulfillment</h4>
                                <p class="text-sm text-gray-600">Manage production orders from vendors and ensure timely delivery to customers.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Production Management Features</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>Real-time production monitoring and tracking</li>
                            <li>Automated scheduling and capacity planning</li>
                            <li>Integration with supplier and vendor systems</li>
                            <li>Quality assurance and compliance management</li>
                            <li>Inventory optimization and material requirements planning</li>
                            <li>Production cost analysis and reporting</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Next Steps</h3>
                        <ol class="list-decimal list-inside space-y-2 text-gray-600">
                            <li>Our admin team will verify your production management credentials</li>
                            <li>You'll receive a notification once your account is approved</li>
                            <li>Complete your production facility profile setup</li>
                            <li>Configure your production lines and capacity settings</li>
                            <li>Start managing production workflows and schedules</li>
                        </ol>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Please Wait for Approval</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Your account access is pending admin approval. Contact support if you have questions: <a href="mailto:support@aktina.com" class="font-medium underline">support@aktina.com</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>
