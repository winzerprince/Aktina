<!-- Modern Button Component -->
@props([
    'variant' => 'primary', // primary, secondary, success, warning, danger, ghost
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
])

@php
$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border-transparent',
    'secondary' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 text-white border-transparent',
    'success' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 text-white border-transparent',
    'warning' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500 text-white border-transparent',
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white border-transparent',
    'ghost' => 'bg-transparent hover:bg-gray-50 focus:ring-gray-500 text-gray-700 border-gray-300',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$classes = $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center border font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed $classes"]) }}
    @if($disabled || $loading) disabled @endif
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif($icon && $iconPosition === 'left')
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right')
        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif
</button>
