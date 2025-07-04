@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'value' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'error' => null,
    'help' => null,
    'size' => 'md',
    'rounded' => false,
])

@php
    // Base classes for the input field
    $baseClasses = 'w-full border focus:outline-none focus:ring-2 transition-colors duration-200';
    
    // Size classes
    $sizeClasses = match ($size) {
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-base',
        'lg' => 'px-5 py-3 text-base',
        'xl' => 'px-6 py-3.5 text-lg',
        default => 'px-4 py-2.5 text-base',
    };
    
    // State classes (error, disabled, etc.)
    $stateClasses = '';
    
    if ($error) {
        $stateClasses .= ' border-danger-500 focus:border-danger-500 focus:ring-danger-500';
    } else {
        $stateClasses .= ' border-neutral-300 dark:border-neutral-600 focus:border-primary-500 focus:ring-primary-500';
    }
    
    if ($disabled) {
        $baseClasses .= ' opacity-60 cursor-not-allowed';
    }

    $inputClasses = $baseClasses . ' ' . $sizeClasses[$size];

    if ($icon) {
        if ($iconPosition === 'left') {
            $inputClasses .= ' pl-10';
        } else {
            $inputClasses .= ' pr-10';
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 {{ $iconPosition === 'left' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                <i class="ni ni-{{ $icon }} text-gray-400"></i>
            </div>
        @endif

        @if($type === 'textarea')
            <textarea
                name="{{ $name }}"
                id="{{ $name }}"
                class="{{ $inputClasses }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                rows="4"
            >{{ $value }}</textarea>
        @elseif($type === 'select')
            <select
                name="{{ $name }}"
                id="{{ $name }}"
                class="{{ $inputClasses }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
            >
                {{ $slot }}
            </select>
        @else
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $name }}"
                class="{{ $inputClasses }}"
                placeholder="{{ $placeholder }}"
                value="{{ $value }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            />
        @endif
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @elseif($help)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
