@props([
    'title' => '',
    'value' => '',
    'description' => null,
    'icon' => 'chart-bar',
    'color' => 'primary', // primary, success, warning, danger, info
    'change' => null, // Change percentage/value
    'changeType' => 'neutral', // positive, negative, neutral
    'loading' => false,
])

@php
    $colorClasses = [
        'primary' => 'bg-gradient-to-br from-[#044c03] to-[#008800] text-white',
        'success' => 'bg-gradient-to-br from-[#008800] to-[#30cf36] text-white',
        'warning' => 'bg-gradient-to-br from-orange-500 to-yellow-400 text-white',
        'danger' => 'bg-gradient-to-br from-red-600 to-red-400 text-white',
        'info' => 'bg-gradient-to-br from-blue-600 to-blue-400 text-white',
    ];

    $changeColorClasses = [
        'positive' => 'text-green-600 dark:text-green-400',
        'negative' => 'text-red-600 dark:text-red-400',
        'neutral' => 'text-gray-600 dark:text-gray-400',
    ];

    $trendIcons = [
        'up' => 'fas fa-arrow-up text-green-400',
        'down' => 'fas fa-arrow-down text-red-400',
        'neutral' => 'fas fa-minus text-gray-400',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl shadow-soft-xl ' . $colorClasses[$color]]) }}>
    @if($loading)
        <div class="absolute inset-0 bg-white bg-opacity-20 flex items-center justify-center z-10">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
        </div>
    @endif
    
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="{{ $icon }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium opacity-90">{{ $title }}</h3>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-3xl font-bold">{{ $value }}</p>
                            @if($trend && $trendValue)
                                <div class="flex items-center space-x-1">
                                    <i class="{{ $trendIcons[$trend] }} text-sm"></i>
                                    <span class="text-sm opacity-90">{{ $trendValue }}</span>
                                </div>
                            @endif
                        </div>
                        @if($subtitle)
                            <p class="text-sm opacity-75 mt-1">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white bg-opacity-10 rounded-full"></div>
    <div class="absolute bottom-0 right-0 -mb-8 -mr-8 w-16 h-16 bg-white bg-opacity-5 rounded-full"></div>
</div>
