<x-layouts.auth title="Account Verification">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/aktina-logo.svg') }}" alt="Aktina" class="h-12 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-gray-900">Welcome to Aktina SCM!</h1>
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
                        <p class="text-blue-700">Your account is currently being reviewed by our admin team. You'll receive access once approved.</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">About Aktina Supply Chain Management</h2>
                        <p class="text-gray-600 mb-4">
                            Aktina is a comprehensive supply chain management platform that connects vendors, suppliers, retailers, and internal teams to streamline operations and enhance collaboration across the entire supply chain.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Platform Features</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Integrated Dashboard</h4>
                                <p class="text-sm text-gray-600">Access a unified dashboard with real-time insights into your role-specific operations and metrics.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Collaboration Tools</h4>
                                <p class="text-sm text-gray-600">Communicate and collaborate seamlessly with partners across the supply chain network.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Analytics & Reporting</h4>
                                <p class="text-sm text-gray-600">Generate detailed reports and gain insights through advanced analytics and data visualization.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Secure Platform</h4>
                                <p class="text-sm text-gray-600">Enterprise-grade security ensures your data and transactions are protected at all times.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">What to Expect</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>Role-based access to relevant features and data</li>
                            <li>Intuitive user interface designed for efficiency</li>
                            <li>Real-time notifications and updates</li>
                            <li>Comprehensive training and support resources</li>
                            <li>Regular platform updates and new features</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Next Steps</h3>
                        <ol class="list-decimal list-inside space-y-2 text-gray-600">
                            <li>Our admin team will review your account and role assignment</li>
                            <li>You'll receive a notification once your account is approved</li>
                            <li>Complete your profile setup based on your assigned role</li>
                            <li>Explore the platform features relevant to your position</li>
                            <li>Start collaborating with your supply chain partners</li>
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

                    <div class="text-center mt-8">
                        <p class="text-gray-500 text-sm">
                            Need immediate assistance? Contact our support team at
                            <a href="mailto:support@aktina.com" class="text-blue-600 hover:text-blue-500 font-medium">support@aktina.com</a>
                            or call 1-800-AKTINA.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>
