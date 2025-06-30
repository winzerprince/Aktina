<!-- Modern Input Component -->
@props([
    'label' => null,
    'error' => null,
    'helpText' => null,
    'required' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
])

<div class="w-full">
    @if($label)
        <label {{ $attributes->only('for') }} class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
            </div>
        @endif

        <input
            {{ $attributes->except(['for'])->merge([
                'class' => 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200' .
                ($icon && $iconPosition === 'left' ? ' pl-10' : '') .
                ($icon && $iconPosition === 'right' ? ' pr-10' : '') .
                ($error ? ' border-red-300 focus:border-red-500 focus:ring-red-500' : '')
            ]) }}
        >

        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif

    @if($helpText && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $helpText }}</p>
    @endif
</div>
