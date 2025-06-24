@props([
    'name' => '',
    'email' => '',
    'role' => '',
    'date' => '',
    'phone' => '',
    'score' => null,
    'business' => null,
    'type' => 'pending', // pending, vendor, user
    'status' => 'pending',
])

@php
    $roleColors = [
        'Vendor' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'Supplier' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
        'Retailer' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'HR Manager' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        'Production Manager' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    ];

    $scoreColor = '';
    if ($score) {
        if ($score >= 8) {
            $scoreColor = 'text-green-600 dark:text-green-400';
        } elseif ($score >= 6) {
            $scoreColor = 'text-yellow-600 dark:text-yellow-400';
        } else {
            $scoreColor = 'text-red-600 dark:text-red-400';
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden']) }}>
    <div class="p-6">
        <div class="flex items-start space-x-4">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-white font-medium">
                    {{ substr($name, 0, 1) }}
                </div>
            </div>

            <!-- User Info -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white truncate">{{ $name }}</h4>
                    @if($score)
                        <div class="text-right">
                            <div class="text-2xl font-bold {{ $scoreColor }}">{{ $score }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Score</div>
                        </div>
                    @endif
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ $email }}</p>

                <div class="mt-2 flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$role] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                        {{ $role }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ date('M j, Y', strtotime($date)) }}</span>
                </div>

                @if($business)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $business }}</p>
                @endif

                @if($phone)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $phone }}</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            @if($type === 'pending')
                <div class="flex space-x-2">
                    <x-ui.button variant="primary" size="sm" class="flex-1">{{ __('Approve') }}</x-ui.button>
                    <x-ui.button variant="outline" size="sm" class="flex-1">{{ __('Decline') }}</x-ui.button>
                </div>
            @elseif($type === 'vendor')
                <div class="space-y-2">
                    <div class="flex space-x-2">
                        <x-ui.button variant="outline" size="sm">{{ __('View Application') }}</x-ui.button>
                        <x-ui.button variant="primary" size="sm">{{ __('Approve') }}</x-ui.button>
                    </div>
                    <div class="flex space-x-2">
                        <x-ui.button variant="warning" size="sm" onclick="toggleInspectionDate(this)">
                            {{ __('Partial Approve') }}
                        </x-ui.button>
                        <x-ui.button variant="outline" size="sm">{{ __('Decline') }}</x-ui.button>
                    </div>

                    <!-- Inspection Date Picker (initially hidden) -->
                    <div class="inspection-date-picker hidden mt-3">
                        <x-ui.date-picker
                            name="inspection_date"
                            label="{{ __('Inspection Date') }}"
                            :min="date('Y-m-d')"
                            class="text-sm"
                        />
                        <div class="flex space-x-2 mt-2">
                            <x-ui.button variant="primary" size="sm">{{ __('Schedule') }}</x-ui.button>
                            <x-ui.button variant="outline" size="sm" onclick="toggleInspectionDate(this)">{{ __('Cancel') }}</x-ui.button>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex space-x-2">
                    <x-ui.button variant="outline" size="sm">{{ __('View Profile') }}</x-ui.button>
                    @if($status === 'blocked')
                        <x-ui.button variant="primary" size="sm">{{ __('Unblock') }}</x-ui.button>
                    @else
                        <x-ui.button variant="outline" size="sm">{{ __('Block') }}</x-ui.button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleInspectionDate(button) {
    const card = button.closest('.bg-white, .dark\\:bg-gray-800');
    const datePicker = card.querySelector('.inspection-date-picker');

    if (datePicker.classList.contains('hidden')) {
        datePicker.classList.remove('hidden');
    } else {
        datePicker.classList.add('hidden');
    }
}
</script>
