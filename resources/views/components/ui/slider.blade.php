@props([
    'label' => null,
    'name' => null,
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => 50,
    'showValue' => true,
    'color' => 'primary', // primary, secondary, success, warning, danger
])

@php
    $colorClasses = [
        'primary' => 'accent-[#008800]',
        'secondary' => 'accent-gray-500',
        'success' => 'accent-[#30cf36]',
        'warning' => 'accent-orange-500',
        'danger' => 'accent-red-500',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'mb-4']) }} x-data="{ value: {{ $value }} }">
    @if($label)
        <div class="flex justify-between items-center mb-2">
            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $label }}
            </label>
            @if($showValue)
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400" x-text="value"></span>
            @endif
        </div>
    @endif

    <div class="relative">
        <input
            type="range"
            name="{{ $name }}"
            id="{{ $name }}"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            x-model="value"
            class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer slider {{ $colorClasses[$color] }}"
        />

        <!-- Custom styling for webkit browsers -->
        <style>
            .slider::-webkit-slider-thumb {
                appearance: none;
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: #008800;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                transition: all 0.2s ease;
            }

            .slider::-webkit-slider-thumb:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            }

            .slider::-moz-range-thumb {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: #008800;
                cursor: pointer;
                border: none;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                transition: all 0.2s ease;
            }

            .slider::-moz-range-thumb:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            }

            .slider::-webkit-slider-track {
                height: 8px;
                background: linear-gradient(to right, #008800 0%, #008800 var(--value, 50%), #e5e7eb var(--value, 50%), #e5e7eb 100%);
                border-radius: 4px;
            }

            .slider::-moz-range-track {
                height: 8px;
                background: #e5e7eb;
                border-radius: 4px;
                border: none;
            }

            .slider::-moz-range-progress {
                height: 8px;
                background: #008800;
                border-radius: 4px;
            }
        </style>
    </div>

    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
        <span>{{ $min }}</span>
        <span>{{ $max }}</span>
    </div>
</div>
