@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'normal', // none, sm, normal, lg
    'shadow' => true,
    'border' => false,
    'header' => false,
    'footer' => false,
    'variant' => 'default', // default, gradient, image
    'backgroundImage' => null,
    'gradientFrom' => null,
    'gradientTo' => null,
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-4',
        'normal' => 'p-6',
        'lg' => 'p-8',
    ];

    $baseClasses = 'relative flex flex-col min-w-0 break-words bg-white dark:bg-zinc-800 bg-clip-border rounded-2xl';

    if ($shadow) {
        $baseClasses .= ' shadow-soft-xl';
    }

    if ($border) {
        $baseClasses .= ' border border-gray-200 dark:border-gray-700';
    }

    $cardClasses = $baseClasses;
    $contentPadding = $paddingClasses[$padding];
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if($variant === 'image' && $backgroundImage)
        <div class="relative h-full overflow-hidden bg-cover rounded-xl" style="background-image: url('{{ $backgroundImage }}')">
            <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 opacity-80"></span>
            <div class="relative z-10 flex flex-col flex-auto h-full {{ $contentPadding }}">
                @if($title)
                    <h5 class="pt-2 mb-6 font-bold text-white">{{ $title }}</h5>
                @endif

                <div class="text-white">
                    {{ $slot }}
                </div>
            </div>
        </div>
    @elseif($variant === 'gradient' && $gradientFrom && $gradientTo)
        <div class="h-full bg-gradient-to-tl from-{{ $gradientFrom }} to-{{ $gradientTo }} rounded-xl {{ $contentPadding }}">
            @if($title)
                <h5 class="pt-2 mb-6 font-bold text-white">{{ $title }}</h5>
            @endif

            <div class="text-white">
                {{ $slot }}
            </div>
        </div>
    @else
        @if($header || $title || $subtitle)
            <div class="p-6 pb-0 mb-0 bg-white dark:bg-zinc-800 border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                @if($header)
                    {{ $header }}
                @endif

                @if($title)
                    <h5 class="font-bold text-gray-800 dark:text-white mb-2">{{ $title }}</h5>
                @endif

                @if($subtitle)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        <div class="flex-auto {{ $header || $title || $subtitle ? 'p-6 pt-0' : $contentPadding }}">
            {{ $slot }}
        </div>

        @if($footer)
            <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-700 rounded-b-2xl border-t border-gray-200 dark:border-gray-600">
                {{ $footer }}
            </div>
        @endif
    @endif
</div>
