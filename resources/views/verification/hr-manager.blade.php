<x-layouts.auth title="HR Manager Verification">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/aktina-logo.svg') }}" alt="Aktina" class="h-12 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-gray-900">Welcome, HR Manager!</h1>
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
                        <p class="text-blue-700">Your HR manager account is currently being reviewed by our admin team. You'll receive access once approved.</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">About Aktina HR Management</h2>
                        <p class="text-gray-600 mb-4">
                            As an HR manager in the Aktina platform, you'll manage human resources across the supply chain organization, handle employee relations, and ensure workforce optimization.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">What You'll Have Access To</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Employee Management</h4>
                                <p class="text-sm text-gray-600">Manage employee records, track performance, and handle onboarding/offboarding processes.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Recruitment & Hiring</h4>
                                <p class="text-sm text-gray-600">Post job openings, manage applications, and coordinate the hiring process across departments.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Training & Development</h4>
                                <p class="text-sm text-gray-600">Organize training programs, track certifications, and manage employee development plans.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Payroll & Benefits</h4>
                                <p class="text-sm text-gray-600">Coordinate with payroll systems and manage employee benefits and compensation packages.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Compliance Management</h4>
                                <p class="text-sm text-gray-600">Ensure HR compliance with labor laws, safety regulations, and company policies.</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Analytics & Reporting</h4>
                                <p class="text-sm text-gray-600">Generate HR reports, track key metrics, and analyze workforce trends and performance.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">HR Management Features</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>Centralized employee database and record management</li>
                            <li>Automated workflow for recruitment and onboarding</li>
                            <li>Performance evaluation and review management</li>
                            <li>Training scheduling and certification tracking</li>
                            <li>Leave management and attendance tracking</li>
                            <li>Employee self-service portal integration</li>
                            <li>HR analytics and workforce planning tools</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Next Steps</h3>
                        <ol class="list-decimal list-inside space-y-2 text-gray-600">
                            <li>Our admin team will verify your HR management credentials</li>
                            <li>You'll receive a notification once your account is approved</li>
                            <li>Complete your HR department profile setup</li>
                            <li>Configure organizational structure and reporting relationships</li>
                            <li>Start managing employee records and HR processes</li>
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
