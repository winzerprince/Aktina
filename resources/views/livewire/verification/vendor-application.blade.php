<div class="space-y-4 lg:space-y-6">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Notifications Section -->
    <div class="bg-white rounded-lg shadow border p-4 lg:p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Notifications</h3>
        <livewire:notifications.notifications-list />
    </div>

    @if ($application)
        <!-- Existing Application Status -->
        <div class="bg-white rounded-lg shadow border p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                <h3 class="text-lg font-semibold text-gray-900">Application Status</h3>
                <livewire:components.status-badge :status="$application->status" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                    <p class="text-sm text-gray-900 font-mono break-all">{{ $application->application_reference }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Submitted Date</label>
                    <p class="text-sm text-gray-900">{{ $application->created_at->format('M d, Y g:i A') }}</p>
                </div>
                @if ($application->score)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Score</label>
                        <p class="text-sm text-gray-900 font-semibold">{{ $application->score }}/100</p>
                    </div>
                @endif
                @if ($application->meeting_schedule)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Date</label>
                        <p class="text-sm text-gray-900">{{ $application->meeting_schedule->format('M d, Y g:i A') }}</p>
                    </div>
                @endif
            </div>

            <!-- Status Progress -->
            <div class="mb-6">
                <livewire:components.progress-indicator :current-step="$application->getProgressStep()" />
            </div>

            @if ($application->isApproved())
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Application Approved!</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Congratulations! Your vendor application has been approved. You now have full access to the Aktina platform.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($application->isRejected())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Application Rejected</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Unfortunately, your application was not approved at this time. Please contact support for more information.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($application->pdf_path && file_exists(public_path($application->pdf_path)))
                <div class="mt-4">
                    <a href="{{ asset($application->pdf_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        View Submitted Application
                    </a>
                </div>
            @endif
        </div>
    @else
        <!-- New Application Form -->
        <div class="bg-white rounded-lg shadow border p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Vendor Application</h3>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex flex-col sm:flex-row">
                    <div class="flex-shrink-0 mb-3 sm:mb-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Application Requirements</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Upload a PDF file containing your vendor application</li>
                                <li>Maximum file size: 10MB</li>
                                <li>Your application will be automatically scored by our system</li>
                                <li>If scored favorably, you'll be invited to a meeting with our team</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="uploadApplication">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor Application PDF <span class="text-red-500">*</span>
                    </label>
                    <livewire:components.file-upload
                        wire:model="pdfFile"
                        accept=".pdf"
                        max-size="10240"
                        label="Upload your vendor application PDF"
                        help-text="PDF files up to 10MB" />
                    @error('pdfFile') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="uploadApplication"
                            :disabled="!$pdfFile || $isUploading">
                        <span wire:loading.remove wire:target="uploadApplication">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Submit Application
                        </span>
                        <span wire:loading wire:target="uploadApplication" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
