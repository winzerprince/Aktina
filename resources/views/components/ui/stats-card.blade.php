@props([
    'title' => '',
    'value' => '',
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'icon' => 'chart-bar',
    'iconBg' => 'primary' // primary, secondary, success, warning, danger
])

@php
    $iconBgClasses = [
        'primary' => 'bg-gradient-to-tl from-[#044c03] to-[#008800]',
        'secondary' => 'bg-gradient-to-tl from-gray-600 to-gray-400',
        'success' => 'bg-gradient-to-tl from-[#008800] to-[#30cf36]',
        'warning' => 'bg-gradient-to-tl from-orange-500 to-yellow-400',
        'danger' => 'bg-gradient-to-tl from-red-600 to-red-400'
    ];

    $changeClasses = [
        'positive' => 'text-[#008800]',
        'negative' => 'text-red-600',
        'neutral' => 'text-gray-500'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'relative flex flex-col min-w-0 break-words bg-white dark:bg-zinc-800 shadow-soft-xl rounded-2xl bg-clip-border']) }}>
    <div class="flex-auto p-4">
        <div class="flex flex-row -mx-3">
            <div class="flex-none w-2/3 max-w-full px-3">
                <div>
                    <p class="mb-0 font-sans font-semibold leading-normal text-sm text-gray-600 dark:text-gray-300">{{ $title }}</p>
                    <h5 class="mb-0 font-bold text-gray-800 dark:text-white">
                        {{ $value }}
                        @if($change)
                            <span class="leading-normal text-sm font-weight-bolder {{ $changeClasses[$changeType] }}">
                                {{ $changeType === 'positive' ? '+' : ($changeType === 'negative' ? '-' : '') }}{{ $change }}
                            </span>
                        @endif
                    </h5>
                </div>
            </div>
            <div class="px-3 text-right basis-1/3">
                <div class="inline-block w-12 h-12 text-center rounded-lg {{ $iconBgClasses[$iconBg] }}">
                    <i class="ni leading-none ni-{{ $icon }} text-lg relative top-3.5 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>
