@props([
    'status' => 'online', // online, offline, pending, active, inactive, success, warning, danger
    'variant' => 'gradient', // gradient, solid, outline
    'size' => 'sm', // xs, sm, md, lg
])

@php
    $statusConfig = [
        'online' => [
            'gradient' => 'bg-gradient-to-tl from-[#008800] to-[#30cf36]',
            'solid' => 'bg-[#008800]',
            'outline' => 'border border-[#008800] text-[#008800]',
            'text' => 'Online'
        ],
        'offline' => [
            'gradient' => 'bg-gradient-to-tl from-gray-600 to-gray-400',
            'solid' => 'bg-gray-500',
            'outline' => 'border border-gray-500 text-gray-500',
            'text' => 'Offline'
        ],
        'pending' => [
            'gradient' => 'bg-gradient-to-tl from-orange-500 to-yellow-400',
            'solid' => 'bg-orange-500',
            'outline' => 'border border-orange-500 text-orange-500',
            'text' => 'Pending'
        ],
        'active' => [
            'gradient' => 'bg-gradient-to-tl from-[#008800] to-[#30cf36]',
            'solid' => 'bg-[#008800]',
            'outline' => 'border border-[#008800] text-[#008800]',
            'text' => 'Active'
        ],
        'inactive' => [
            'gradient' => 'bg-gradient-to-tl from-gray-600 to-gray-400',
            'solid' => 'bg-gray-500',
            'outline' => 'border border-gray-500 text-gray-500',
            'text' => 'Inactive'
        ],
        'success' => [
            'gradient' => 'bg-gradient-to-tl from-[#008800] to-[#30cf36]',
            'solid' => 'bg-[#008800]',
            'outline' => 'border border-[#008800] text-[#008800]',
            'text' => 'Success'
        ],
        'warning' => [
            'gradient' => 'bg-gradient-to-tl from-orange-500 to-yellow-400',
            'solid' => 'bg-orange-500',
            'outline' => 'border border-orange-500 text-orange-500',
            'text' => 'Warning'
        ],
        'danger' => [
            'gradient' => 'bg-gradient-to-tl from-red-600 to-red-400',
            'solid' => 'bg-red-500',
            'outline' => 'border border-red-500 text-red-500',
            'text' => 'Danger'
        ],
    ];

    $sizeClasses = [
        'xs' => 'px-1.5 py-0.5 text-xs',
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];

    $baseClasses = 'inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none rounded-lg';
    $variantClass = $statusConfig[$status][$variant] ?? $statusConfig['offline'][$variant];
    $textColor = $variant === 'outline' ? '' : 'text-white';

    $classes = $baseClasses . ' ' . $variantClass . ' ' . $sizeClasses[$size] . ' ' . $textColor;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot->isEmpty() ? $statusConfig[$status]['text'] : $slot }}
</span>
