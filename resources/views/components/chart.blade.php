@props([
    'type' => 'line',
    'title' => null,
    'subtitle' => null,
    'height' => '300px',
    'data' => [],
    'labels' => [],
    'datasets' => null,
    'options' => [],
    'color' => 'blue',
    'showLegend' => false,
    'showGrid' => true,
    'fillArea' => false
])

@php
    $colorPalettes = [
        'blue' => [
            'border' => 'rgb(59, 130, 246)',
            'background' => 'rgba(59, 130, 246, 0.2)'
        ],
        'green' => [
            'border' => 'rgb(20, 184, 166)',
            'background' => 'rgba(20, 184, 166, 0.2)'
        ],
        'red' => [
            'border' => 'rgb(239, 68, 68)',
            'background' => 'rgba(239, 68, 68, 0.2)'
        ],
        'yellow' => [
            'border' => 'rgb(245, 158, 11)',
            'background' => 'rgba(245, 158, 11, 0.2)'
        ],
        'purple' => [
            'border' => 'rgb(139, 92, 246)',
            'background' => 'rgba(139, 92, 246, 0.2)'
        ],
        'orange' => [
            'border' => 'rgb(251, 146, 60)',
            'background' => 'rgba(251, 146, 60, 0.2)'
        ]
    ];

    $chartId = 'chart-' . uniqid();
    $colors = $colorPalettes[$color] ?? $colorPalettes['blue'];

    // Default options
    $defaultOptions = [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => $showLegend
            ],
            'tooltip' => [
                'enabled' => true,
                'callbacks' => [
                    'label' => 'function(context) {
                        return context.dataset.label + \': \' + context.parsed.y;
                    }'
                ]
            ]
        ],
        'scales' => $showGrid ? [
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'stepSize' => 2
                ]
            ],
            'x' => [
                'grid' => [
                    'display' => false
                ]
            ]
        ] : []
    ];

    // Merge with custom options
    $chartOptions = array_merge_recursive($defaultOptions, $options);

    // Prepare datasets
    if ($datasets === null && !empty($data)) {
        $datasets = [[
            'label' => $title ?? 'Data',
            'data' => $data,
            'borderColor' => $colors['border'],
            'backgroundColor' => $colors['background'],
            'fill' => $fillArea,
            'tension' => 0.4,
            'pointRadius' => 4,
            'pointHoverRadius' => 6,
            'borderWidth' => 2
        ]];
    }
@endphp

<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    <!-- Chart Header -->
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-gray-200">
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <!-- Chart Container -->
    <div class="p-6">
        <div style="height: {{ $height }};">
            <canvas id="{{ $chartId }}"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $chartId }}').getContext('2d');

    const chartData = {
        labels: @json($labels),
        datasets: @json($datasets ?? [])
    };

    const chartOptions = @json($chartOptions);

    // Convert function strings to actual functions
    if (chartOptions.plugins.tooltip.callbacks.label) {
        chartOptions.plugins.tooltip.callbacks.label = eval('(' + chartOptions.plugins.tooltip.callbacks.label + ')');
    }

    new Chart(ctx, {
        type: '{{ $type }}',
        data: chartData,
        options: chartOptions
    });
});
</script>
@endpush
