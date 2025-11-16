@props([
'title' => 'Filter & Pencarian',
'action' => '',
'method' => 'GET',
'fields' => [],
'theme' => 'blue',
'resetUrl' => '',
'showPerPage' => true,
'perPageOptions' => [50, 100, 150, 200],
'currentPerPage' => 50,
'gridCols' => 'md:grid-cols-4'
])

@php
// Theme colors configuration
$themes = [
'blue' => [
'bgGradient' => 'from-blue-50 to-cyan-50',
'iconColor' => 'text-blue-600',
'focusColor' => 'focus:ring-blue-500',
'buttonGradient' => 'from-blue-600 to-blue-700',
'buttonHoverGradient' => 'from-blue-700 hover:to-blue-800',
'hoverBg' => 'hover:bg-blue-50',
'hoverBorder' => 'hover:border-blue-400',
],
'purple' => [
'bgGradient' => 'from-purple-50 to-pink-50',
'iconColor' => 'text-purple-600',
'focusColor' => 'focus:ring-purple-500',
'buttonGradient' => 'from-purple-600 to-purple-700',
'buttonHoverGradient' => 'from-purple-700 hover:to-purple-800',
'hoverBg' => 'hover:bg-purple-50',
'hoverBorder' => 'hover:border-purple-400',
],
'red' => [
'bgGradient' => 'from-red-50 to-pink-50',
'iconColor' => 'text-red-600',
'focusColor' => 'focus:ring-red-500',
'buttonGradient' => 'from-red-600 to-red-700',
'buttonHoverGradient' => 'from-red-700 hover:to-red-800',
'hoverBg' => 'hover:bg-red-50',
'hoverBorder' => 'hover:border-red-400',
],
'indigo' => [
'bgGradient' => 'from-indigo-50 to-blue-50',
'iconColor' => 'text-indigo-600',
'focusColor' => 'focus:ring-indigo-500',
'buttonGradient' => 'from-indigo-600 to-indigo-700',
'buttonHoverGradient' => 'from-indigo-700 hover:to-indigo-800',
'hoverBg' => 'hover:bg-indigo-50',
'hoverBorder' => 'hover:border-indigo-400',
],
'purple-indigo' => [
'bgGradient' => 'from-purple-50 to-indigo-50',
'iconColor' => 'text-purple-600',
'focusColor' => 'focus:ring-purple-500',
'buttonGradient' => 'from-purple-600 to-purple-700',
'buttonHoverGradient' => 'from-purple-700 hover:to-purple-800',
'hoverBg' => 'hover:bg-purple-50',
'hoverBorder' => 'hover:border-purple-400',
]
];

$currentTheme = $themes[$theme] ?? $themes['blue'];
@endphp

<!-- Filter Section -->
<div class="p-6 bg-linear-to-r {{ $currentTheme['bgGradient'] }} border-b border-gray-200">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 {{ $currentTheme['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <h3 class="text-sm font-semibold text-gray-800">{{ $title }}</h3>
    </div>

    <form method="{{ $method }}" action="{{ $action }}" class="grid grid-cols-1 {{ $gridCols }} gap-4">
        @foreach($fields as $field)
        @if($field['type'] === 'text')
        <div class="{{ $field['colSpan'] ?? 'md:col-span-1' }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $field['label'] }}</label>
            <div class="relative">
                @if($field['withIcon'] ?? false)
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="{{ $field['name'] }}" value="{{ request($field['name']) }}"
                    class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 {{ $currentTheme['focusColor'] }} text-sm bg-white"
                    placeholder="{{ $field['placeholder'] ?? '' }}">
                @else
                <input type="text" name="{{ $field['name'] }}" value="{{ request($field['name']) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 {{ $currentTheme['focusColor'] }} text-sm bg-white"
                    placeholder="{{ $field['placeholder'] ?? '' }}">
                @endif
            </div>
        </div>
        @elseif($field['type'] === 'select')
        <div class="{{ $field['colSpan'] ?? 'md:col-span-1' }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $field['label'] }}</label>
            <div class="relative">
                <select name="{{ $field['name'] }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 {{ $currentTheme['focusColor'] }} focus:border-transparent appearance-none bg-white pr-10">
                    @if(isset($field['options']))
                    @foreach($field['options'] as $option)
                    <option value="{{ $option['value'] }}" {{ request($field['name'])==$option['value'] ? 'selected'
                        : '' }}>
                        {{ $option['label'] }}
                    </option>
                    @endforeach
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>
        @elseif($field['type'] === 'per_page')
        @if($showPerPage)
        <div class="{{ $field['colSpan'] ?? 'md:col-span-1' }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $field['label'] ?? 'Data per Halaman'
                }}</label>
            <select name="{{ $field['name'] ?? 'per_page' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 {{ $currentTheme['focusColor'] }} text-sm bg-white shadow-sm">
                @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ request('per_page', $currentPerPage)==$option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
                @endforeach
            </select>
        </div>
        @endif
        @endif
        @endforeach

        <!-- Tombol Filter dan Reset -->
        <div class="md:col-span-1 flex items-end gap-2">
            <button type="submit"
                class="flex-1 px-5 py-2.5 bg-linear-to-r {{ $currentTheme['buttonGradient'] }} {{ $currentTheme['buttonHoverGradient'] }} text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Filter
            </button>
            <a href="{{ $resetUrl }}"
                class="px-5 py-2.5 bg-linear-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </a>
        </div>
    </form>
</div>