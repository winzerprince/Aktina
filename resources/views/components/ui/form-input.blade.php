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
    'iconPosition' => 'left', // left, right
    'error' => null,
    'help' => null,
    'size' => 'md', // sm, md, lg
])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-3 text-base',
        'lg' => 'px-5 py-4 text-lg',
    ];

    $baseClasses = 'w-full border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#008800] focus:border-[#008800] dark:bg-gray-700 dark:text-white transition-colors duration-200';

    if ($error) {
        $baseClasses .= ' border-red-500 focus:ring-red-500 focus:border-red-500';
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
