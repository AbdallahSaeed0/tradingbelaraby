@extends('admin.layout')

@section('body_class', 'admin-dashboard-page')

@section('title', custom_trans($definition['title'], 'admin') . ' - Site Analytics')

@php
    $visibleDims = array_values(array_filter($view['dimensions'], fn ($d) => empty($d['hidden'])));
    $periodQuery = $period->query();
    $baseQuery = array_filter(['view' => $viewKey, 'q' => $search] + $periodQuery, fn ($v) => $v !== '' && $v !== null);
    $sortUrl = function (string $metric) use ($baseQuery, $sort, $desc) {
        $dir = $sort === $metric && $desc ? 'asc' : 'desc';
        return request()->url() . '?' . http_build_query($baseQuery + ['sort' => $metric, 'dir' => $dir]);
    };
    $itemUrl = function (array $row) use ($reportKey, $viewKey, $periodQuery) {
        return route('admin.site-analytics.item', ['reportKey' => $reportKey, 'view' => $viewKey, 'f' => $row['dimensions']] + $periodQuery);
    };
    $rowLabel = function (array $row) use ($visibleDims) {
        return implode(' · ', array_map(fn ($d) => \App\Services\Analytics\Format::dimension($row['dimensions'][$d['name']] ?? '', $d['format'] ?? null), $visibleDims));
    };
    $chartMetric = collect($definition['metrics'])->firstWhere('key', $sort ?? $sortable[0]) ?? $definition['metrics'][0];
    $isTimeView = !empty($view['sort_dimension']) && $sort === null;
@endphp

@section('content')
    <div class="container-fluid py-4 admin-dashboard">

        {{-- ── Header ── --}}
        <div class="db-welcome-banner mb-4">
            <div class="db-welcome-content">
                <div>
                    <h1 class="db-welcome-title"><i class="fa {{ $definition['icon'] }} me-2"></i>{{ custom_trans($definition['title'], 'admin') }}</h1>
                    <p class="db-welcome-sub mb-0">{{ custom_trans($definition['description'], 'admin') }}</p>
                    <small class="text-white-50">{{ $period->label() }}</small>
                </div>
                <div class="db-welcome-meta">
                    @include('admin.site-analytics.partials.period', ['keep' => array_filter(['view' => $viewKey, 'q' => $search, 'sort' => request('sort'), 'dir' => request('dir')])])
                </div>
            </div>
        </div>

        @include('admin.site-analytics.partials.nav', ['active' => $reportKey])

        @include('admin.site-analytics.partials.status')

        {{-- ── Group-by tabs ── --}}
        @if (count($definition['views']) > 1)
            <ul class="nav nav-tabs mb-3">
                @foreach ($definition['views'] as $key => $v)
                    <li class="nav-item">
                        <a class="nav-link {{ $key === $viewKey ? 'active' : '' }}"
                            href="{{ route('admin.site-analytics.report', ['reportKey' => $reportKey, 'view' => $key] + $periodQuery) }}">
                            {{ custom_trans($v['label'], 'admin') }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($table)
            {{-- ── Totals ── --}}
            @if ($table['totals'])
                <div class="row g-3 mb-4">
                    @foreach ($definition['metrics'] as $metric)
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="border rounded-3 bg-white p-3 h-100">
                                <div class="text-muted small text-uppercase">{{ custom_trans($metric['label'], 'admin') }}</div>
                                <div class="fs-4 fw-bold">{{ \App\Services\Analytics\Format::metric($table['totals'][$metric['key']], $metric['format']) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($search !== '' || !empty($view['exclude_not_set']))
                    <p class="text-muted small mt-n2 mb-3">
                        <i class="fa fa-filter me-1"></i>{{ custom_trans('Totals include only the rows matching the current filter.', 'admin') }}
                    </p>
                @endif
            @endif

            {{-- ── Chart of the rows on this page ── --}}
            @if (count($table['rows']) > 1)
                <div class="db-widget mb-4">
                    <div class="db-widget-header db-widget-primary">
                        <i class="fa fa-chart-bar"></i>
                        <span>
                            {{ custom_trans($chartMetric['label'], 'admin') }}
                            {{ $isTimeView ? '' : '— ' . custom_trans('top', 'admin') . ' ' . min(10, count($table['rows'])) }}
                        </span>
                    </div>
                    <div class="db-widget-body">
                        <div style="position: relative; height: {{ $isTimeView ? 260 : max(200, min(10, count($table['rows'])) * 32) }}px;">
                            <canvas id="reportChart" role="img" aria-label="{{ $chartMetric['label'] }}"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Table ── --}}
            <div class="db-widget mb-4">
                <div class="db-widget-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <form method="GET" action="{{ request()->url() }}" class="d-flex gap-2" role="search">
                            @foreach (array_filter(['view' => $viewKey, 'sort' => request('sort'), 'dir' => request('dir')] + $periodQuery) as $name => $value)
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endforeach
                            <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm"
                                placeholder="{{ custom_trans('Search', 'admin') }} {{ mb_strtolower(custom_trans($visibleDims[0]['label'], 'admin')) }}..." style="min-width: 220px;">
                            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                            @if ($search !== '')
                                <a class="btn btn-sm btn-outline-secondary" href="{{ request()->url() . '?' . http_build_query(array_diff_key($baseQuery, ['q' => 1])) }}">
                                    <i class="fa fa-times"></i>
                                </a>
                            @endif
                        </form>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">{{ number_format($table['row_count']) }} {{ custom_trans('rows', 'admin') }}</span>
                            <a class="btn btn-sm btn-outline-success" href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null, 'refresh' => null]) }}">
                                <i class="fa fa-file-csv me-1"></i>{{ custom_trans('Export CSV', 'admin') }}
                            </a>
                        </div>
                    </div>

                    @if (count($table['rows']))
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        @foreach ($visibleDims as $dim)
                                            <th>{{ custom_trans($dim['label'], 'admin') }}</th>
                                        @endforeach
                                        @foreach ($definition['metrics'] as $metric)
                                            <th class="text-end text-nowrap">
                                                @if (in_array($metric['key'], $sortable, true))
                                                    <a href="{{ $sortUrl($metric['key']) }}" class="text-reset text-decoration-none">
                                                        {{ custom_trans($metric['label'], 'admin') }}
                                                        @if ($sort === $metric['key'])
                                                            <i class="fa fa-sort-{{ $desc ? 'down' : 'up' }} text-primary"></i>
                                                        @else
                                                            <i class="fa fa-sort text-muted opacity-50"></i>
                                                        @endif
                                                    </a>
                                                @else
                                                    {{ custom_trans($metric['label'], 'admin') }}
                                                @endif
                                            </th>
                                        @endforeach
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($table['rows'] as $i => $row)
                                        <tr>
                                            <td class="text-muted small">{{ ($paginator->currentPage() - 1) * $paginator->perPage() + $i + 1 }}</td>
                                            @foreach ($visibleDims as $j => $dim)
                                                @php $value = $row['dimensions'][$dim['name']] ?? ''; @endphp
                                                <td style="max-width: 380px;" @if (($dim['format'] ?? null) === 'path') dir="ltr" @endif>
                                                    @if ($j === 0)
                                                        <a href="{{ $itemUrl($row) }}" class="fw-semibold text-truncate d-block" title="{{ \App\Services\Analytics\Format::dimension($value, $dim['format'] ?? null) }}">
                                                            {{ \App\Services\Analytics\Format::dimension($value, $dim['format'] ?? null) }}
                                                        </a>
                                                    @else
                                                        <span class="text-truncate d-block" title="{{ \App\Services\Analytics\Format::dimension($value, $dim['format'] ?? null) }}">{{ \App\Services\Analytics\Format::dimension($value, $dim['format'] ?? null) }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            @foreach ($definition['metrics'] as $metric)
                                                <td class="text-end text-nowrap {{ $sort === $metric['key'] ? 'fw-semibold' : '' }}">
                                                    {{ \App\Services\Analytics\Format::metric($row['metrics'][$metric['key']], $metric['format']) }}
                                                </td>
                                            @endforeach
                                            <td class="text-end text-nowrap">
                                                <a href="{{ $itemUrl($row) }}" class="btn btn-sm btn-outline-primary" title="{{ custom_trans('Details', 'admin') }}">
                                                    <i class="fa fa-chart-line"></i>
                                                </a>
                                                @php $pathDim = collect($visibleDims)->first(fn ($d) => ($d['format'] ?? null) === 'path'); @endphp
                                                @if ($pathDim && str_starts_with($row['dimensions'][$pathDim['name']] ?? '', '/'))
                                                    <a href="{{ url($row['dimensions'][$pathDim['name']]) }}" target="_blank" rel="noopener"
                                                        class="btn btn-sm btn-outline-secondary" title="{{ custom_trans('Open page', 'admin') }}">
                                                        <i class="fa fa-external-link-alt"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($paginator->hasPages())
                            <div class="mt-3">{{ $paginator->links('pagination::bootstrap-5') }}</div>
                        @endif
                    @else
                        <div class="db-widget-empty">
                            <i class="fa {{ $definition['icon'] }}"></i>
                            <span>{{ $search !== '' ? custom_trans('No rows match your search', 'admin') : custom_trans('No data in this period yet', 'admin') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <p class="text-muted small mb-0">
                <i class="fa fa-info-circle me-1"></i>
                {{ custom_trans('Click a row to see its daily trend and where its visitors come from.', 'admin') }}
                {{ custom_trans('Updated', 'admin') }} {{ \Carbon\Carbon::parse($table['fetched_at'])->diffForHumans() }}.
            </p>
        @endif
    </div>
@endsection

@if ($table && count($table['rows']) > 1)
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                @php
                    $chartRows = $isTimeView ? $table['rows'] : array_slice($table['rows'], 0, 10);
                @endphp
                const labels = @json(array_map($rowLabel, $chartRows));
                const values = @json(array_map(fn ($r) => round($r['metrics'][$chartMetric['key']], 4), $chartRows));
                const format = @json($chartMetric['format']);
                const horizontal = @json(!$isTimeView);
                const dark = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = dark ? '#c9ccd1' : '#6c757d';
                const gridColor = dark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
                const fmt = v => format === 'percent' ? (v * 100).toFixed(1) + '%'
                    : format === 'duration' ? (v >= 60 ? Math.floor(v / 60) + 'm ' + Math.round(v % 60) + 's' : Math.round(v) + 's')
                    : format === 'decimal' ? v.toFixed(2) : v.toLocaleString();
                const valueAxis = { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, callback: fmt } };
                const labelAxis = { grid: { display: false }, ticks: { color: textColor, callback: function(v) {
                    const l = this.getLabelForValue(v);
                    return l.length > 40 ? l.slice(0, 38) + '…' : l;
                } } };

                new Chart(document.getElementById('reportChart'), {
                    type: 'bar',
                    data: { labels: labels, datasets: [{ data: values, backgroundColor: 'rgba(13,110,253,0.75)', borderRadius: 4, maxBarThickness: 28 }] },
                    options: {
                        indexAxis: horizontal ? 'y' : 'x',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => fmt(ctx.raw) } } },
                        scales: horizontal ? { x: valueAxis, y: labelAxis } : { x: labelAxis, y: valueAxis }
                    }
                });
            })();
        </script>
    @endpush
@endif
