@props([
    'data',
    'showInfo' => true,
    'showPerPage' => false,
    'perPageOptions' => [10, 25, 50, 100],
    'align' => 'right'
])

@php
    $alignClasses = [
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end'
    ];

    $alignClass = $alignClasses[$align] ?? $alignClasses['right'];
@endphp

<div class="flex flex-col sm:flex-row items-center justify-between gap-4">
    <!-- Pagination Info -->
    @if($showInfo && $data)
        <div class="text-sm text-gray-600">
            Showing
            <span class="font-medium">{{ $data->firstItem() ?? 0 }}</span>
            to
            <span class="font-medium">{{ $data->lastItem() ?? 0 }}</span>
            of
            <span class="font-medium">{{ $data->total() }}</span>
            results
        </div>
    @endif

    <!-- Per Page Selector -->
    @if($showPerPage && $data)
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Show</label>
            <form action="{{ request()->url() }}" method="GET" class="flex items-center gap-2">
                @foreach(request()->except('per_page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select
                    name="per_page"
                    onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" {{ request('per_page', $data->perPage()) == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                <span class="text-sm text-gray-600">entries</span>
            </form>
        </div>
    @endif

    <!-- Pagination Links -->
    @if($data && $data->hasPages())
        <div class="flex items-center gap-1 {{ $alignClass }}">
            {{-- Previous Link --}}
            @if($data->onFirstPage())
                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </span>
            @else
                <a href="{{ $data->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach($elements = $data->links()->elements as $element)
                {{-- "Three Dots" Separator --}}
                @if(is_string($element))
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if(is_array($element))
                    @foreach($element as $page => $url)
                        @if($page == $data->currentPage())
                            <span aria-current="page" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Link --}}
            @if($data->hasMorePages())
                <a href="{{ $data->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Next
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                    Next
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    @endif
</div>
