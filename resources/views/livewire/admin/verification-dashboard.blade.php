<div class="space-y-4 lg:space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">
        <!-- Pending Applications -->
        <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg shadow-lg p-4 lg:p-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 lg:h-8 lg:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 lg:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-yellow-100 truncate">Pending Applications</dt>
                        <dd class="text-xl lg:text-2xl font-bold">{{ $stats['pending_applications'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Scored Applications -->
        <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg shadow-lg p-4 lg:p-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 lg:h-8 lg:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-3 lg:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-blue-100 truncate">Scored Applications</dt>
                        <dd class="text-2xl font-bold">{{ $stats['scored_applications'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Meetings Scheduled -->
        <div class="bg-gradient-to-r from-purple-400 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-purple-100 truncate">Meetings Scheduled</dt>
                        <dd class="text-2xl font-bold">{{ $stats['meetings_scheduled'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Awaiting Decision -->
        <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-green-100 truncate">Awaiting Decision</dt>
                        <dd class="text-2xl font-bold">{{ $stats['meetings_completed'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Applications -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Applications</h3>
                        <a href="{{ route('admin.applications.index') }}"
                           class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            View all
                        </a>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Vendor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Score
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($stats['recent_applications'] as $application)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $application->vendor?->user?->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $application->application_reference }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <livewire:components.status-badge :status="$application->status" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        @if($application->score)
                                            {{ $application->score }}/100
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $application->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No recent applications
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overview</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Applications</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['total_applications'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Unverified Users</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['unverified_users'] }}</span>
                    </div>
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Application Status Distribution</div>
                        <div class="space-y-2">
                            @if($stats['total_applications'] > 0)
                                <div class="flex items-center text-xs">
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Pending: {{ round(($stats['pending_applications'] / $stats['total_applications']) * 100) }}%</span>
                                </div>
                                <div class="flex items-center text-xs">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full mr-2"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Scored: {{ round(($stats['scored_applications'] / $stats['total_applications']) * 100) }}%</span>
                                </div>
                                <div class="flex items-center text-xs">
                                    <div class="w-2 h-2 bg-purple-400 rounded-full mr-2"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Meetings: {{ round(($stats['meetings_scheduled'] / $stats['total_applications']) * 100) }}%</span>
                                </div>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400">No applications yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.applications.index') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Manage Applications
                    </a>

                    <button wire:click="$refresh"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
