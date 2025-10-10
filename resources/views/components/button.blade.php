@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'onclick' => null
])

@php
    $variantClasses = [
        'primary' => [
            'bg' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'text' => 'text-white',
            'border' => 'border-transparent'
        ],
        'success' => [
            'bg' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            'text' => 'text-white',
            'border' => 'border-transparent'
        ],
        'danger' => [
            'bg' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
            'text' => 'text-white',
            'border' => 'border-transparent'
        ],
        'warning' => [
            'bg' => 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-500',
            'text' => 'text-white',
            'border' => 'border-transparent'
        ],
        'secondary' => [
            'bg' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
            'text' => 'text-white',
            'border' => 'border-transparent'
        ],
        'outline' => [
            'bg' => 'bg-white hover:bg-gray-50',
            'text' => 'text-gray-700',
            'border' => 'border-gray-300'
        ],
        'outline-primary' => [
            'bg' => 'bg-white hover:bg-blue-50',
            'text' => 'text-blue-600',
            'border' => 'border-blue-300'
        ],
        'outline-success' => [
            'bg' => 'bg-white hover:bg-green-50',
            'text' => 'text-green-600',
            'border' => 'border-green-300'
        ],
        'outline-danger' => [
            'bg' => 'bg-white hover:bg-red-50',
            'text' => 'text-red-600',
            'border' => 'border-red-300'
        ],
        'ghost' => [
            'bg' => 'bg-transparent hover:bg-gray-100',
            'text' => 'text-gray-700',
            'border' => 'border-transparent'
        ]
    ];

    $sizeClasses = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'xl' => 'px-8 py-4 text-lg'
    ];

    $iconSizes = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
        'xl' => 'w-7 h-7'
    ];

    $classes = $variantClasses[$variant] ?? $variantClasses['primary'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];

    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed';
    $allClasses = "{$baseClasses} {$classes['bg']} {$classes['text']} {$classes['border']} {$sizeClass}";
@endphp

@if($href)
    <a href="{{ $href }}"
       class="{{ $allClasses }} {{ $disabled ? 'pointer-events-none opacity-50' : '' }}"
       {{ $disabled ? 'tabindex="-1"' : '' }}
       {{ $onclick ? "onclick=\"{$onclick}\"" : '' }}>
@else
    <button type="{{ $type }}"
            class="{{ $allClasses }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $onclick ? "onclick=\"{$onclick}\"" : '' }}>
@endif

    <!-- Loading spinner -->
    @if($loading)
        <svg class="animate-spin {{ $iconSize }} {{ $icon ? 'mr-2' : '' }}" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif

    <!-- Icon -->
    @if($icon && $iconPosition === 'left' && !$loading)
        <span class="mr-2 {{ $iconSize }}">{!! $icon !!}</span>
    @endif

    <!-- Button content -->
    <span>{{ $slot }}</span>

    <!-- Right icon -->
    @if($icon && $iconPosition === 'right' && !$loading)
        <span class="ml-2 {{ $iconSize }}">{!! $icon !!}</span>
    @endif

@if($href)
    </a>
@else
    </button>
@endif
