@props([
    'variant' => 'primary', // primary, secondary, success, warning, danger, outline
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'loading' => false,
    'disabled' => false,
])

@php
    $variantClasses = [
        'primary' => 'bg-gradient-to-tl from-[#044c03] to-[#008800] text-white hover:shadow-soft-2xl hover:scale-102',
        'secondary' => 'bg-gradient-to-tl from-gray-600 to-gray-400 text-white hover:shadow-soft-2xl hover:scale-102',
        'success' => 'bg-gradient-to-tl from-[#008800] to-[#30cf36] text-white hover:shadow-soft-2xl hover:scale-102',
        'warning' => 'bg-gradient-to-tl from-orange-500 to-yellow-400 text-white hover:shadow-soft-2xl hover:scale-102',
        'danger' => 'bg-gradient-to-tl from-red-600 to-red-400 text-white hover:shadow-soft-2xl hover:scale-102',
        'outline' => 'border border-[#008800] text-[#008800] hover:bg-[#008800] hover:text-white',
    ];

    $sizeClasses = [
        'sm' => 'px-4 py-2 text-xs',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-4 text-base',
    ];

    $baseClasses = 'inline-block font-bold text-center uppercase align-middle transition-all ease-in border-0 rounded-lg select-none shadow-soft-md leading-pro';

    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];

    if ($disabled || $loading) {
        $classes .= ' opacity-65 cursor-not-allowed';
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="ni ni-{{ $icon }} mr-2"></i>
        @endif

        @if($loading)
            <i class="fas fa-spinner fa-spin mr-2"></i>
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right')
            <i class="ni ni-{{ $icon }} ml-2"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled || $loading) disabled @endif>
        @if($icon && $iconPosition === 'left')
            <i class="ni ni-{{ $icon }} mr-2"></i>
        @endif

        @if($loading)
            <i class="fas fa-spinner fa-spin mr-2"></i>
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right')
            <i class="ni ni-{{ $icon }} ml-2"></i>
        @endif
    </button>
@endif
