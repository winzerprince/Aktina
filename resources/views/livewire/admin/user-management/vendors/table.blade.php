<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800 px-6 py-3">
            <div class="flex items-center space-x-2">
                <x-icon name="o-check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                <p class="text-green-800 dark:text-green-200 text-sm font-medium">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    {{-- Table Header --}}
    <div class="bg-zinc-50 dark:bg-zinc-900 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">Vendors Management</h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">View and manage vendor applications and status</p>
    </div>

    {{-- Table Content --}}
    <div class="overflow-x-auto">
        @php
            $headers = [
                ['key' => 'id', 'label' => 'ID', 'class' => 'w-16 text-center'],
                ['key' => 'name', 'label' => 'Vendor', 'class' => 'w-64'],
                ['key' => 'email', 'label' => 'Email', 'class' => 'w-64'],
                ['key' => 'application_status', 'label' => 'Application Status', 'class' => 'w-40'],
                ['key' => 'meeting_schedule', 'label' => 'Scheduled Visit', 'class' => 'w-40'],
                ['key' => 'created_at', 'label' => 'Applied', 'class' => 'w-32'],
                ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32 text-center'],
            ];
        @endphp

        <x-table
            :headers="$headers"
            :rows="$vendors"
            striped
            class="min-w-full dark:bg-zinc-800"
            with-pagination
            per-page="10"
        >
            {{-- Vendor ID Cell --}}
            @scope('cell_id', $vendor)
                <div class="text-center">
                    <x-badge :value="$vendor->id" class="badge-ghost dark:bg-zinc-700 dark:text-zinc-300 font-mono text-xs" />
                </div>
            @endscope

            {{-- Vendor Name Cell --}}
            @scope('cell_name', $vendor)
                <div class="flex items-center space-x-3 py-2">
                    <x-avatar :image="null" :label="$vendor->user->initials()" class="!w-10 !h-10" />
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $vendor->user->name }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $vendor->user->company_name ?? 'No Company' }}</div>
                    </div>
                </div>
            @endscope

            {{-- Email Cell --}}
            @scope('cell_email', $vendor)
                <div class="py-2">
                    <div class="text-zinc-900 dark:text-zinc-100">{{ $vendor->user->email }}</div>
                    @if($vendor->user->email_verified_at)
                        <div class="flex items-center space-x-1 mt-1">
                            <x-icon name="o-check-circle" class="w-3 h-3 text-green-500" />
                            <span class="text-xs text-green-600 dark:text-green-400">Verified</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-1 mt-1">
                            <x-icon name="o-exclamation-circle" class="w-3 h-3 text-amber-500" />
                            <span class="text-xs text-amber-600 dark:text-amber-400">Unverified</span>
                        </div>
                    @endif
                </div>
            @endscope

            {{-- Application Status Cell --}}
            @scope('cell_application_status', $vendor)
                <div class="py-2">
                    @if($vendor->application)
                        @php
                            $statusColors = [
                                'pending' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'partially approved' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            ];
                            $statusClass = $statusColors[$vendor->application->status] ?? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300';
                        @endphp
                        <x-badge :value="ucfirst($vendor->application->status)" class="{{ $statusClass }} badge-sm" />
                    @else
                        <x-badge value="No Application" class="bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300 badge-sm" />
                    @endif
                </div>
            @endscope

            {{-- Meeting Schedule Cell --}}
            @scope('cell_meeting_schedule', $vendor)
                <div class="py-2">
                    @if($vendor->application && $vendor->application->meeting_schedule)
                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $vendor->application->meeting_schedule->format('M d, Y') }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $vendor->application->meeting_schedule->diffForHumans() }}
                        </div>
                    @else
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Not scheduled</span>
                    @endif
                </div>
            @endscope

            {{-- Created At Cell --}}
            @scope('cell_created_at', $vendor)
                <div class="py-2">
                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $vendor->created_at->format('M d, Y') }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $vendor->created_at->diffForHumans() }}</div>
                </div>
            @endscope

            {{-- Actions Cell --}}
            @scope('cell_actions', $vendor)
                <div class="text-center py-2">
                    @if($vendor->user->email_verified_at)
                        <x-button
                            wire:click="toggleEmailVerification({{ $vendor->user->id }})"
                            class="btn-sm btn-warning"
                            icon="o-x-circle"
                            tooltip="Click to unverify email"
                        >
                            Unverify
                        </x-button>
                    @else
                        <x-button
                            wire:click="toggleEmailVerification({{ $vendor->user->id }})"
                            class="btn-sm btn-success"
                            icon="o-check-circle"
                            tooltip="Click to verify email"
                        >
                            Verify
                        </x-button>
                    @endif
                </div>
            @endscope

            {{-- Empty State --}}
            <x-slot:empty>
                <div class="text-center py-12">
                    <x-icon name="o-building-storefront" class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No vendors found</h3>
                    <p class="text-zinc-500 dark:text-zinc-400">There are no vendor applications to display at this time.</p>
                </div>
            </x-slot:empty>
        </x-table>
    </div>
</div>
