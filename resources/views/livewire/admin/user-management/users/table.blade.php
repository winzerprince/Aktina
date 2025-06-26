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
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">Users Management</h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">View and manage all system users</p>
    </div>

    {{-- Table Content --}}
    <div class="overflow-x-auto">
        @php
            $headers = [
                ['key' => 'id', 'label' => 'ID', 'class' => 'w-16 text-center'],
                ['key' => 'name', 'label' => 'User', 'class' => 'w-64'],
                ['key' => 'email', 'label' => 'Email', 'class' => 'w-64'],
                ['key' => 'role', 'label' => 'Role', 'class' => 'w-32'],
                ['key' => 'company_name', 'label' => 'Company', 'class' => 'w-48'],
                ['key' => 'verified', 'label' => 'Status', 'class' => 'w-24 text-center'],
                ['key' => 'created_at', 'label' => 'Joined', 'class' => 'w-32'],
                ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32 text-center'],
            ];
        @endphp

        <x-table
            :headers="$headers"
            :rows="$users"
            striped
            class="min-w-full dark:bg-zinc-800"
            with-pagination
            per-page="10"
        >
            {{-- User ID Cell --}}
            @scope('cell_id', $user)
                <div class="text-center">
                    <x-badge :value="$user->id" class="badge-ghost dark:bg-zinc-700 dark:text-zinc-300 font-mono text-xs" />
                </div>
            @endscope

            {{-- User Name Cell --}}
            @scope('cell_name', $user)
                <div class="flex items-center space-x-3 py-2">
                    <x-avatar :image="null" :label="$user->initials()" class="!w-10 !h-10" />
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->company_type }}</div>
                    </div>
                </div>
            @endscope

            {{-- Email Cell --}}
            @scope('cell_email', $user)
                <div class="py-2">
                    <div class="text-zinc-900 dark:text-zinc-100">{{ $user->email }}</div>
                    @if($user->email_verified_at)
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

            {{-- Role Cell --}}
            @scope('cell_role', $user)
                <div class="py-2">
                    @php
                        $roleColors = [
                            'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                            'supplier' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'vendor' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'retailer' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                            'hr_manager' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
                            'production_manager' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                        ];
                        $roleClass = $roleColors[$user->role] ?? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300';
                    @endphp
                    <x-badge :value="ucfirst(str_replace('_', ' ', $user->role))" class="{{ $roleClass }} badge-sm" />
                </div>
            @endscope

            {{-- Company Name Cell --}}
            @scope('cell_company_name', $user)
                <div class="py-2">
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $user->company_name ?? 'N/A' }}</span>
                </div>
            @endscope

            {{-- Verified Status Cell --}}
            @scope('cell_verified', $user)
                <div class="text-center py-2">
                    @if($user->verified)
                        <x-badge value="Active" class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 badge-sm" />
                    @else
                        <x-badge value="Inactive" class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 badge-sm" />
                    @endif
                </div>
            @endscope

            {{-- Created At Cell --}}
            @scope('cell_created_at', $user)
                <div class="py-2">
                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->created_at->format('M d, Y') }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->created_at->diffForHumans() }}</div>
                </div>
            @endscope

            {{-- Actions Cell --}}
            @scope('cell_actions', $user)
                <div class="text-center py-2">
                    @if($user->email_verified_at)
                        <x-button
                            wire:click="toggleVerification({{ $user->id }})"
                            class="btn-sm btn-warning"
                            icon="o-x-circle"
                            tooltip="Click to unverify email"
                        >
                            Unverify Email
                        </x-button>
                    @else
                        <x-button
                            wire:click="toggleVerification({{ $user->id }})"
                            class="btn-sm btn-success"
                            icon="o-check-circle"
                            tooltip="Click to verify email"
                        >
                            Verify Email
                        </x-button>
                    @endif
                </div>
            @endscope

            {{-- Empty State --}}
            <x-slot:empty>
                <div class="text-center py-12">
                    <x-icon name="o-users" class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No users found</h3>
                    <p class="text-zinc-500 dark:text-zinc-400">There are no users to display at this time.</p>
                </div>
            </x-slot:empty>
        </x-table>
    </div>
</div>
