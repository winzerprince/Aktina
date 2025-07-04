@props([
    'type' => 'info', 
    'title' => null,
    'dismissible' => false,
    'icon' => true,
    'size' => 'md',
    'outlined' => false,
    'rounded' => false,
])

@php
    if ($outlined) {
        $typeConfig = [
            'success' => [
                'bg' => 'bg-success-50',
                'border' => 'border border-success-500',
                'text' => 'text-success-700',
                'icon' => 'check-circle',
                'iconColor' => 'text-success-500',
            ],
            'warning' => [
                'bg' => 'bg-warning-50',
                'border' => 'border border-warning-500',
                'text' => 'text-warning-700',
                'icon' => 'exclamation-triangle',
                'iconColor' => 'text-warning-500',
            ],
            'danger' => [
                'bg' => 'bg-danger-50',
                'border' => 'border border-danger-500',
                'text' => 'text-danger-700',
                'icon' => 'exclamation-circle',
                'iconColor' => 'text-danger-500',
            ],
            'info' => [
                'bg' => 'bg-primary-50',
                'border' => 'border border-primary-500',
                'text' => 'text-primary-700',
                'icon' => 'information-circle',
                'iconColor' => 'text-primary-500',
            ],
        ];
    } else {
        $typeConfig = [
            'success' => [
                'bg' => 'bg-success-500',
                'border' => 'border-success-600',
                'text' => 'text-white',
                'icon' => 'check-circle',
                'iconColor' => 'text-white',
            ],
            'warning' => [
                'bg' => 'bg-warning-500',
                'border' => 'border-warning-600',
                'text' => 'text-white',
                'icon' => 'exclamation-triangle',
                'iconColor' => 'text-white',
            ],
            'danger' => [
                'bg' => 'bg-danger-500',
                'border' => 'border-danger-600',
                'text' => 'text-white',
                'icon' => 'exclamation-circle',
                'iconColor' => 'text-white',
            ],
            'info' => [
                'bg' => 'bg-primary-500',
                'border' => 'border-primary-600',
                'text' => 'text-white',
                'icon' => 'information-circle',
                'iconColor' => 'text-white',
            ],
            'border' => 'border-blue-500',
            'text' => 'text-white',
            'icon' => 'information-circle'
        ],
    ];

    $sizeClasses = [
        'sm' => 'p-3 text-sm',
        'md' => 'p-4 text-base',
        'lg' => 'p-6 text-lg',
    ];

    $config = $typeConfig[$type];
    $baseClasses = 'rounded-lg shadow-soft-xl border-l-4 ' . $config['bg'] . ' ' . $config['border'] . ' ' . $config['text'] . ' ' . $sizeClasses[$size];
@endphp

<div
    {{ $attributes->merge(['class' => $baseClasses]) }}
    @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif
>
    <div class="flex items-start">
        @if($icon)
            <div class="flex-shrink-0 mr-3">
                @if($config['icon'] === 'check-circle')
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @elseif($config['icon'] === 'exclamation-triangle')
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                @elseif($config['icon'] === 'exclamation-circle')
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </div>
        @endif

        <div class="flex-1">
            @if($title)
                <h4 class="font-bold mb-1">{{ $title }}</h4>
            @endif

            <div class="{{ $title ? 'text-sm opacity-90' : '' }}">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
            <button type="button" class="flex-shrink-0 ml-3 opacity-70 hover:opacity-100 transition-opacity" @click="show = false">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>
</div>
