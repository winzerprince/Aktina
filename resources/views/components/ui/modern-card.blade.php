<!-- Modern Card Component -->
@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'padding' => 'default', // none, sm, default, lg
    'shadow' => 'default', // none, sm, default, md, lg
])

@php
$paddings = [
    'none' => '',
    'sm' => 'p-4',
    'default' => 'p-6',
    'lg' => 'p-8',
];

$shadows = [
    'none' => '',
    'sm' => 'shadow-sm',
    'default' => 'shadow',
    'md' => 'shadow-md',
    'lg' => 'shadow-lg',
];

$classes = $paddings[$padding] . ' ' . $shadows[$shadow];
@endphp

<div {{ $attributes->merge(['class' => "bg-white border border-gray-200 rounded-xl $classes"]) }}>
    @if($title || $subtitle || $actions)
        <div class="flex items-center justify-between mb-4 {{ $padding === 'none' ? 'px-6 pt-6' : '' }}">
            <div>
                @if($title)
                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="flex items-center space-x-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
