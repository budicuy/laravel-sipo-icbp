@props([
    'title',
    'value',
    'icon',
    'color' => 'blue',
    'trend' => null,
    'loading' => false
])

@php
    $colorClasses = [
        'blue' => [
            'bg' => 'from-blue-500 via-blue-600 to-blue-700',
            'iconBg' => 'bg-blue-600',
            'dotBg' => 'bg-blue-200',
            'text' => 'text-blue-100'
        ],
        'green' => [
            'bg' => 'from-green-500 via-green-600 to-green-700',
            'iconBg' => 'bg-green-600',
            'dotBg' => 'bg-green-200',
            'text' => 'text-green-100'
        ],
        'yellow' => [
            'bg' => 'from-yellow-400 via-yellow-500 to-yellow-600',
            'iconBg' => 'bg-yellow-600',
            'dotBg' => 'bg-yellow-200',
            'text' => 'text-yellow-100'
        ],
        'red' => [
            'bg' => 'from-red-500 via-red-600 to-red-700',
            'iconBg' => 'bg-red-600',
            'dotBg' => 'bg-red-200',
            'text' => 'text-red-100'
        ],
        'gray' => [
            'bg' => 'from-gray-600 via-gray-700 to-gray-800',
            'iconBg' => 'bg-gray-700',
            'dotBg' => 'bg-gray-300',
            'text' => 'text-gray-200'
        ],
        'purple' => [
            'bg' => 'from-purple-500 via-purple-600 to-purple-700',
            'iconBg' => 'bg-purple-600',
            'dotBg' => 'bg-purple-200',
            'text' => 'text-purple-100'
        ],
        'orange' => [
            'bg' => 'from-orange-500 via-orange-600 to-orange-700',
            'iconBg' => 'bg-orange-600',
            'dotBg' => 'bg-orange-200',
            'text' => 'text-orange-100'
        ]
    ];

    $classes = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="relative overflow-hidden bg-gradient-to-br {{ $classes['bg'] }} rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-300 {{ $loading ? 'opacity-75' : '' }}">
    <!-- Decorative circles -->
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>
    <div class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-white opacity-5 rounded-full"></div>

    <div class="relative">
        <!-- Title with indicator -->
        <div class="flex items-center gap-2 mb-2">
            <div class="w-2 h-2 {{ $classes['dotBg'] }} rounded-full {{ !$loading ? 'animate-pulse' : '' }}"></div>
            <h3 class="text-xs font-medium {{ $classes['text'] }}">{{ $title }}</h3>
        </div>

        <!-- Main content -->
        <div class="flex items-end justify-between">
            <!-- Value -->
            <div>
                @if($loading)
                    <div class="h-12 w-24 bg-white bg-opacity-20 rounded animate-pulse"></div>
                @else
                    <p class="text-5xl font-bold">{{ $value ?? '-' }}</p>
                @endif

                <!-- Trend indicator -->
                @if($trend && !$loading)
                    <div class="flex items-center mt-2 text-sm {{ $trend['direction'] === 'up' ? 'text-green-300' : 'text-red-300' }}">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            @if($trend['direction'] === 'up')
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                        {{ $trend['value'] ?? '' }}
                        <span class="ml-1">{{ $trend['label'] ?? '' }}</span>
                    </div>
                @endif
            </div>

            <!-- Icon -->
            @if($icon && !$loading)
                <div class="{{ $classes['iconBg'] }} p-3 rounded-lg shadow-lg">
                    {!! $icon !!}
                </div>
            @elseif($loading)
                <div class="{{ $classes['iconBg'] }} p-3 rounded-lg shadow-lg opacity-50">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded animate-pulse"></div>
                </div>
            @endif
        </div>
    </div>
</div>
