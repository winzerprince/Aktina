@props([
    'value' => 0,
    'max' => 100,
    'color' => 'primary', // primary, secondary, success, warning, danger
    'size' => 'md', // sm, md, lg
    'label' => null,
    'showPercentage' => false,
    'animated' => false,
])

@php
    $percentage = $max > 0 ? ($value / $max) * 100 : 0;

    $colorClasses = [
        'primary' => 'bg-gradient-to-r from-[#044c03] to-[#008800]',
        'secondary' => 'bg-gradient-to-r from-gray-600 to-gray-400',
        'success' => 'bg-gradient-to-r from-[#008800] to-[#30cf36]',
        'warning' => 'bg-gradient-to-r from-orange-500 to-yellow-400',
        'danger' => 'bg-gradient-to-r from-red-600 to-red-400',
    ];

    $sizeClasses = [
        'sm' => 'h-1',
        'md' => 'h-2',
        'lg' => 'h-3',
    ];

    $bgHeight = $sizeClasses[$size];
    $barHeight = match($size) {
        'sm' => 'h-1.5',
        'md' => 'h-2.5',
        'lg' => 'h-3.5',
        default => 'h-2.5'
    };
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label || $showPercentage)
        <div class="flex justify-between items-center mb-2">
            @if($label)
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
            @endif
            @if($showPercentage)
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ number_format($percentage, 1) }}%</span>
            @endif
        </div>
    @endif

    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden {{ $bgHeight }}">
        <div
            class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-lg transition-all duration-500 ease-soft {{ $colorClasses[$color] }} {{ $barHeight }} {{ $animated ? 'animate-pulse' : '' }}"
            style="width: {{ $percentage }}%; margin-top: {{ $size === 'sm' ? '-0.125rem' : ($size === 'lg' ? '-0.25rem' : '-0.188rem') }}"
            role="progressbar"
            aria-valuenow="{{ $value }}"
            aria-valuemin="0"
            aria-valuemax="{{ $max }}"
        ></div>
    </div>
</div>
