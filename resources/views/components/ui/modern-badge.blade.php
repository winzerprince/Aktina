<!-- Modern Badge Component -->
@props([
    'variant' => 'default', // default, primary, secondary, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'dot' => false,
])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-800',
    'primary' => 'bg-blue-100 text-blue-800',
    'secondary' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger' => 'bg-red-100 text-red-800',
    'info' => 'bg-indigo-100 text-indigo-800',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-xs',
    'lg' => 'px-3 py-1 text-sm',
];

$classes = $variants[$variant] . ' ' . $sizes[$size];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full $classes"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-current"></span>
    @endif
    {{ $slot }}
</span>
