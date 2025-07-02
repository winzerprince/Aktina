<div class="bg-white rounded-lg shadow border border-gray-200 p-6">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Generate Reports</h3>
        <p class="text-sm text-gray-600">Export analytics data in CSV or PDF format</p>
    </div>

    <!-- Report Configuration Form -->
    <form wire:submit.prevent="generateReport" class="space-y-6">
        <!-- Report Type Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="reportType" class="block text-sm font-medium text-gray-700 mb-2">
                    Report Type
                </label>
                <select wire:model="reportType" 
                        id="reportType"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($availableReports as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('reportType')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                    Format
                </label>
                <select wire:model="format" 
                        id="format"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="csv">CSV (Excel Compatible)</option>
                    <option value="pdf">PDF (Printable)</option>
                </select>
                @error('format')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-2">
                    Start Date
                </label>
                <input wire:model="startDate" 
                       type="date" 
                       id="startDate"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('startDate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-2">
                    End Date
                </label>
                <input wire:model="endDate" 
                       type="date" 
                       id="endDate"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('endDate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Role Filter (for applicable reports) -->
        @if(in_array($reportType, ['sales', 'users', 'orders']))
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    Filter by Role (Optional)
                </label>
                <select wire:model="role" 
                        id="role"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($availableRoles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="flex space-x-3">
                <button type="button" 
                        wire:click="previewReport"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview
                </button>

                <button type="button" 
                        wire:click="resetFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </button>
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <div wire:loading wire:target="generateReport" class="mr-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <svg wire:loading.remove wire:target="generateReport" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span wire:loading.remove wire:target="generateReport">Generate & Download</span>
                <span wire:loading wire:target="generateReport">Generating...</span>
            </button>
        </div>
    </form>

    <!-- Quick Actions -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button wire:click="$set('reportType', 'sales'); $set('startDate', '{{ now()->subDays(7)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                Sales (7 days)
            </button>
            <button wire:click="$set('reportType', 'inventory'); $set('startDate', '{{ now()->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                Current Inventory
            </button>
            <button wire:click="$set('reportType', 'orders'); $set('startDate', '{{ now()->subDays(30)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                Orders (30 days)
            </button>
            <button wire:click="$set('reportType', 'comprehensive'); $set('startDate', '{{ now()->subDays(90)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                Full Report (90 days)
            </button>
        </div>
    </div>

    <!-- Report Info -->
    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Report Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>CSV reports are compatible with Excel and other spreadsheet applications</li>
                        <li>PDF reports include charts and formatted summaries</li>
                        <li>Large reports may take a few moments to generate</li>
                        <li>All reports include metadata and generation timestamps</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Scripts -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('reportGenerated', (data) => {
            // Show success notification
            console.log('Report generated successfully:', data);
        });

        Livewire.on('reportError', (data) => {
            // Show error notification
            alert('Error: ' + data.message);
        });

        Livewire.on('reportPreview', (data) => {
            // Show preview modal or redirect to preview page
            console.log('Report preview:', data);
        });
    });
</script>
