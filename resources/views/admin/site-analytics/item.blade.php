@extends('admin.layout')

@section('body_class', 'admin-dashboard-page')

@php
    $F = \App\Services\Analytics\Format::class;
    $periodQuery = $period->query();
    $visibleDims = array_values(array_filter($view['dimensions'], fn ($d) => empty($d['hidden'])));
    $title = implode(' · ', array_filter(array_map(
        fn ($d) => isset($match[$d['name']]) ? $F::dimension($match[$d['name']], $d['format'] ?? null) : null,
        $visibleDims
    )));
    $pathDim = collect($visibleDims)->first(fn ($d) => ($d['format'] ?? null) === 'path' && str_starts_with($match[$d['name']] ?? '', '/'));
    $metricsByKey = collect($definition['metrics'])->keyBy('key');
    $colors = ['#0d6efd', '#198754'];
@endphp

@section('title', $title . ' - Site Analytics')

@section('content')
    <div class="container-fluid py-4 admin-dashboard">

        {{-- ── Header ── --}}
        <div class="db-welcome-banner mb-4">
            <div class="db-welcome-content">
                <div style="min-width: 0;">
                    <a href="{{ route('admin.site-analytics.report', ['reportKey' => $reportKey, 'view' => $viewKey] + $periodQuery) }}"
                        class="text-white-50 small text-decoration-none">
                        <i class="fa fa-arrow-left me-1"></i>{{ custom_trans($definition['title'], 'admin') }}
                        @if (count($definition['views']) > 1) / {{ custom_trans($view['label'], 'admin') }} @endif
                    </a>
                    <h1 class="db-welcome-title text-break" @if ($pathDim) dir="ltr" @endif>{{ $title }}</h1>
                    <small class="text-white-50">{{ $period->label() }}</small>
                    @if ($pathDim)
                        <a href="{{ url($match[$pathDim['name']]) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light ms-2">
                            <i class="fa fa-external-link-alt me-1"></i>{{ custom_trans('Open page', 'admin') }}
                        </a>
                    @endif
                </div>
                <div class="db-welcome-meta">
                    @include('admin.site-analytics.partials.period', ['keep' => ['view' => $viewKey, 'f' => $match]])
                </div>
            </div>
        </div>

        @include('admin.site-analytics.partials.nav', ['active' => $reportKey])

        @include('admin.site-analytics.partials.status')

        @if ($item)
            @if (!$item['totals'])
                <div class="alert alert-info">{{ custom_trans('No data for this item in the selected period.', 'admin') }}</div>
            @else
                {{-- ── Totals ── --}}
                <div class="row g-3 mb-4">
                    @foreach ($definition['metrics'] as $metric)
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="border rounded-3 bg-white p-3 h-100">
                                <div class="text-muted small text-uppercase">{{ custom_trans($metric['label'], 'admin') }}</div>
                                <div class="fs-4 fw-bold">{{ $F::metric($item['totals'][$metric['key']], $metric['format']) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ── Daily trend ── --}}
                <div class="db-widget mb-4">
                    <div class="db-widget-header db-widget-primary">
                        <i class="fa fa-chart-line"></i>
                        <span>{{ custom_trans('Day by day', 'admin') }}</span>
                    </div>
                    <div class="db-widget-body">
                        <div style="position: relative; height: 300px;">
                            <canvas id="itemTrendChart" role="img" aria-label="Daily trend"></canvas>
                        </div>
                    </div>
                </div>

                {{-- ── Breakdowns ── --}}
                <div class="row g-3 g-md-4 mb-4">
                    @foreach ($item['breakdowns'] as $dimension => $breakdown)
                        @php $info = \App\Services\Analytics\ReportCatalog::breakdownInfo($dimension); @endphp
                        <div class="col-md-6 col-xl-{{ count($item['breakdowns']) >= 4 ? 3 : 4 }}">
                            <div class="db-widget h-100">
                                <div class="db-widget-header db-widget-info">
                                    <i class="fa {{ $info['icon'] }}"></i>
                                    <span>{{ custom_trans($info['label'], 'admin') }}</span>
                                </div>
                                <div class="db-widget-body p-3" @if (($info['format'] ?? null) === 'path') dir="ltr" @endif>
                                    @if ($breakdown['error'])
                                        <div class="text-muted small"><i class="fa fa-exclamation-circle me-1"></i>{{ custom_trans('Not available for this item', 'admin') }}</div>
                                    @else
                                        @include('admin.site-analytics.partials.bars', [
                                            'rows' => array_map(fn ($r) => ['label' => $F::dimension($r['label'], $info['format'] ?? null), 'value' => $r['value']], $breakdown['rows']),
                                            'icons' => [],
                                            'unit' => $metricsByKey[$item['main_metric']]['label'] ?? '',
                                            'showPercent' => true,
                                        ])
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="text-muted small mb-0">
                    <i class="fa fa-info-circle me-1"></i>
                    {{ custom_trans('Breakdowns are by', 'admin') }} {{ mb_strtolower(custom_trans($metricsByKey[$item['main_metric']]['label'] ?? '', 'admin')) }}.
                    {{ custom_trans('Updated', 'admin') }} {{ \Carbon\Carbon::parse($item['fetched_at'])->diffForHumans() }}.
                </p>
            @endif
        @endif
    </div>
@endsection

@if ($item && $item['totals'])
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                const trend = @json($item['trend']);
                const labels = @json(collect($item['trend']['series'])->keys()->map(fn ($k) => custom_trans($metricsByKey[$k]['label'] ?? $k, 'admin'))->values());
                const colors = @json($colors);
                const dark = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = dark ? '#c9ccd1' : '#6c757d';
                const gridColor = dark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';

                new Chart(document.getElementById('itemTrendChart'), {
                    type: 'line',
                    data: {
                        labels: trend.labels,
                        datasets: Object.keys(trend.series).map((key, i) => ({
                            label: labels[i],
                            data: trend.series[key],
                            borderColor: colors[i % colors.length],
                            backgroundColor: colors[i % colors.length] + '1a',
                            fill: true,
                            tension: 0.3,
                            pointRadius: trend.labels.length > 31 ? 0 : 3,
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { labels: { color: textColor } } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: textColor, maxTicksLimit: 12 } },
                            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endif
