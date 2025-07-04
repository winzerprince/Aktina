@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'disabled' => false,
    'rounded' => false,
    'fullWidth' => false,
])

@php
    // Base classes
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm';
    
    // Size classes
    $sizeClasses = match ($size) {
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
        'xl' => 'px-6 py-3.5 text-lg',
        default => 'px-4 py-2.5 text-sm',
    };

    // Variant classes
    $variantClasses = match ($variant) {
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 disabled:bg-primary-300',
        'secondary' => 'bg-secondary-600 text-white hover:bg-secondary-700 focus:ring-secondary-500 disabled:bg-secondary-300',
        'success' => 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500 disabled:bg-success-300',
        'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500 disabled:bg-danger-300',
        'warning' => 'bg-warning-500 text-white hover:bg-warning-600 focus:ring-warning-400 disabled:bg-warning-200',
        'outline-primary' => 'border border-primary-600 text-primary-600 bg-white hover:bg-primary-50 focus:ring-primary-500 disabled:opacity-50',
        'outline-secondary' => 'border border-secondary-600 text-secondary-600 bg-white hover:bg-secondary-50 focus:ring-secondary-500 disabled:opacity-50',
        'outline-success' => 'border border-success-600 text-success-600 bg-white hover:bg-success-50 focus:ring-success-500 disabled:opacity-50',
        'outline-danger' => 'border border-danger-600 text-danger-600 bg-white hover:bg-danger-50 focus:ring-danger-500 disabled:opacity-50',
        'outline-warning' => 'border border-warning-500 text-warning-500 bg-white hover:bg-warning-50 focus:ring-warning-400 disabled:opacity-50',
        'text' => 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-800 focus:ring-neutral-500 disabled:opacity-50',
        'neutral' => 'bg-neutral-200 text-neutral-800 hover:bg-neutral-300 focus:ring-neutral-400 disabled:opacity-50',
        default => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 disabled:bg-primary-300',
    };

    // Rounded
    $roundedClasses = $rounded ? 'rounded-full' : 'rounded-md';

    // Width
    $widthClasses = $fullWidth ? 'w-full' : '';

    // Loading & Disabled
    $stateClasses = ($disabled || $loading) ? 'opacity-65 cursor-not-allowed' : '';

    // Combine all classes
    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses} {$roundedClasses} {$widthClasses} {$stateClasses}";
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
