@props([
    'headers',
    'data',
    'emptyMessage' => 'Tidak ada data',
    'emptyIcon' => null,
    'striped' => true,
    'hover' => true,
    'bordered' => true,
    'compact' => false,
    'actions' => null,
    'pagination' => null,
    'showPerPage' => false,
    'perPageOptions' => [10, 25, 50, 100]
])

@php
    $tableClasses = 'w-full';
    $headerClasses = 'bg-gray-800';
    $rowClasses = $striped ? 'bg-white divide-y divide-gray-200' : 'bg-white';
    $cellClasses = $compact ? 'px-4 py-2 text-sm' : 'px-6 py-4 text-sm';
    $headerCellClasses = $compact ? 'px-4 py-2 text-xs font-bold text-white uppercase tracking-wider' : 'px-6 py-4 text-xs font-bold text-white uppercase tracking-wider';
    $borderClasses = $bordered ? 'border-r border-gray-200' : '';
    $hoverClasses = $hover ? 'hover:bg-gray-50 transition-colors' : '';
@endphp

<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    <!-- Table Header with Actions -->
    @if($actions || $showPerPage)
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    {{ $actions ?? '' }}
                </div>

                @if($showPerPage && $pagination)
                    <form action="{{ request()->url() }}" method="GET" class="flex items-center gap-2">
                        @foreach(request()->except('per_page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label class="text-sm text-gray-600">Show</label>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}" {{ request('per_page', $pagination->perPage()) == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        <span class="text-sm text-gray-600">entries</span>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="{{ $tableClasses }}">
            <!-- Table Header -->
            <thead class="{{ $headerClasses }}">
                <tr>
                    @foreach($headers as $key => $header)
                        @if(is_array($header))
                            <th class="{{ $headerCellClasses }} {{ $borderClasses }} {{ $header['class'] ?? '' }}" {{ $header['attributes'] ?? '' }}>
                                {{ $header['label'] }}
                            </th>
                        @else
                            <th class="{{ $headerCellClasses }} {{ $borderClasses }}">
                                {{ $header }}
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="{{ $rowClasses }}">
                @forelse($data as $index => $row)
                    <tr class="{{ $hoverClasses }}">
                        @foreach($headers as $key => $header)
                            @php
                                $cellValue = is_object($row) ? $row->$key : $row[$key];
                                $cellClass = $borderClasses;

                                // Check if header is array with custom renderer
                                if (is_array($header) && isset($header['renderer'])) {
                                    $renderer = $header['renderer'];
                                    $cellValue = $renderer($row, $index);
                                }

                                // Apply custom cell class if defined
                                if (is_array($header) && isset($header['cellClass'])) {
                                    $cellClass .= ' ' . $header['cellClass'];
                                }
                            @endphp
                            <td class="{{ $cellClasses }} {{ $cellClass }}">
                                {!! $cellValue !!}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-8 text-center text-gray-500">
                            @if($emptyIcon)
                                <div class="w-16 h-16 mx-auto text-gray-400 mb-3">{!! $emptyIcon !!}</div>
                            @else
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @endif
                            <p class="text-lg font-medium">{{ $emptyMessage }}</p>
                            {{ $slot }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer with Pagination -->
    @if($pagination)
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ $pagination->firstItem() ?? 0 }}</span>
                    to <span class="font-medium">{{ $pagination->lastItem() ?? 0 }}</span>
                    of <span class="font-medium">{{ $pagination->total() }}</span> entries
                </div>
                <div class="flex items-center gap-2">
                    {{ $pagination->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
