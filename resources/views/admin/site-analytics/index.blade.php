@extends('admin.layout')

@section('body_class', 'admin-dashboard-page')

@section('title', 'Site Analytics')

@php
    $formatDuration = function ($seconds) {
        $seconds = (int) round($seconds);
        return $seconds >= 60 ? intdiv($seconds, 60) . 'm ' . ($seconds % 60) . 's' : $seconds . 's';
    };
    $channelIcons = [
        'Organic Search' => 'fa-search',
        'Direct' => 'fa-mouse-pointer',
        'Organic Social' => 'fa-share-alt',
        'Paid Social' => 'fa-bullhorn',
        'Referral' => 'fa-link',
        'Email' => 'fa-envelope',
        'Paid Search' => 'fa-ad',
        'Unassigned' => 'fa-question',
    ];
@endphp

@section('content')
    <div class="container-fluid py-4 admin-dashboard">

        {{-- â”€â”€ Header â”€â”€ --}}
        <div class="db-welcome-banner mb-4">
            <div class="db-welcome-content">
                <div>
                    <h1 class="db-welcome-title">{{ custom_trans('Site Analytics', 'admin') }}</h1>
                    <p class="db-welcome-sub">{{ custom_trans('Website traffic from Google Analytics', 'admin') }}</p>
                </div>
                <div class="db-welcome-meta d-flex flex-wrap align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Period">
                        @foreach ($periods as $period)
                            <a href="{{ route('admin.site-analytics.index', ['days' => $period]) }}"
                                class="btn {{ $days === $period ? 'btn-light' : 'btn-outline-light' }}">
                                {{ $period }} {{ custom_trans('days', 'admin') }}
                            </a>
                        @endforeach
                    </div>
                    @if ($report)
                        <a href="{{ route('admin.site-analytics.index', ['days' => $days, 'refresh' => 1]) }}"
                            class="btn btn-sm btn-outline-light" title="{{ custom_trans('Refresh data', 'admin') }}">
                            <i class="fa fa-sync-alt"></i>
                        </a>
                        <span class="db-updated-time">
                            {{ custom_trans('Updated', 'admin') }}
                            {{ \Carbon\Carbon::parse($report['fetched_at'])->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if ($problem)
            <div class="alert alert-warning">
                <h5 class="alert-heading"><i class="fa fa-plug me-2"></i>{{ custom_trans('Google Analytics is not connected yet', 'admin') }}</h5>
                <p class="mb-2">{{ $problem }}</p>
                <ul class="mb-0 small">
                    <li>Set <code>ANALYTICS_PROPERTY_ID</code> in <code>.env</code> (the number after <code>p</code> in the Analytics URL).</li>
                    <li>Place the service account JSON key at <code>storage/app/analytics/service-account-credentials.json</code>.</li>
                    <li>Run <code>php artisan config:clear</code>.</li>
                </ul>
            </div>
        @elseif ($error)
            <div class="alert alert-danger">
                <h5 class="alert-heading"><i class="fa fa-exclamation-triangle me-2"></i>{{ custom_trans('Could not load Google Analytics data', 'admin') }}</h5>
                <p class="mb-1 small text-break">{{ \Illuminate\Support\Str::limit($error, 500) }}</p>
                <p class="mb-0 small">Check that the Google Analytics Data API is enabled and the service account has Viewer access on the property.</p>
            </div>
        @else
            @php
                $t = $report['totals'];
                $cards = [
                    ['key' => 'activeUsers', 'label' => 'Visitors', 'icon' => 'fa-users', 'color' => 'primary', 'format' => fn ($v) => number_format($v)],
                    ['key' => 'screenPageViews', 'label' => 'Page Views', 'icon' => 'fa-eye', 'color' => 'success', 'format' => fn ($v) => number_format($v)],
                    ['key' => 'sessions', 'label' => 'Sessions', 'icon' => 'fa-door-open', 'color' => 'info', 'format' => fn ($v) => number_format($v)],
                    ['key' => 'newUsers', 'label' => 'New Visitors', 'icon' => 'fa-user-plus', 'color' => 'warning', 'format' => fn ($v) => number_format($v)],
                ];
                $metricCards = [
                    ['key' => 'averageSessionDuration', 'label' => 'Avg. Session Time', 'icon' => 'fa-clock', 'color' => 'secondary', 'format' => $formatDuration],
                    ['key' => 'engagementRate', 'label' => 'Engagement Rate', 'icon' => 'fa-hand-pointer', 'color' => 'amber', 'format' => fn ($v) => round($v * 100, 1) . '%'],
                ];
            @endphp

            {{-- â”€â”€ Headline numbers â”€â”€ --}}
            <div class="row g-3 g-md-4 mb-4">
                @foreach ($cards as $card)
                    @php $m = $t[$card['key']]; @endphp
                    <div class="col-6 col-md-3">
                        <div class="db-stat-card db-stat-{{ $card['color'] }}">
                            <div class="db-stat-icon-wrap"><i class="fa {{ $card['icon'] }}"></i></div>
                            <div class="db-stat-body">
                                <span class="db-stat-label">{{ custom_trans($card['label'], 'admin') }}</span>
                                <span class="db-stat-value">{{ ($card['format'])($m['value']) }}</span>
                                @include('admin.site-analytics.partials.change', ['change' => $m['change']])
                            </div>
                            <div class="db-stat-accent"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-3 g-md-4 mb-4">
                {{-- Realtime --}}
                <div class="col-md-4">
                    <div class="db-metric-card">
                        <div class="db-metric-icon db-metric-dark"><i class="fa fa-signal"></i></div>
                        <div class="db-metric-body">
                            <span class="db-metric-label">
                                {{ custom_trans('Active right now', 'admin') }}
                                <span class="db-live-dot ms-1"></span>
                            </span>
                            <span class="db-metric-value" id="realtimeActiveUsers">{{ $realtime['active_users'] ?? 0 }}</span>
                            <small class="text-muted">{{ custom_trans('last 30 minutes', 'admin') }}</small>
                        </div>
                    </div>
                </div>
                @foreach ($metricCards as $card)
                    @php $m = $t[$card['key']]; @endphp
                    <div class="col-md-4">
                        <div class="db-metric-card">
                            <div class="db-metric-icon db-metric-{{ $card['color'] }}"><i class="fa {{ $card['icon'] }}"></i></div>
                            <div class="db-metric-body">
                                <span class="db-metric-label">{{ custom_trans($card['label'], 'admin') }}</span>
                                <span class="db-metric-value">{{ ($card['format'])($m['value']) }}</span>
                                @include('admin.site-analytics.partials.change', ['change' => $m['change']])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- â”€â”€ Traffic chart â”€â”€ --}}
            <div class="db-widget mb-4">
                <div class="db-widget-header db-widget-primary">
                    <i class="fa fa-chart-line"></i>
                    <span>{{ custom_trans('Visitors & page views per day', 'admin') }}</span>
                </div>
                <div class="db-widget-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="trafficChart" aria-label="Visitors and page views per day" role="img"></canvas>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-md-4 mb-4">
                {{-- Top pages --}}
                <div class="col-lg-8">
                    <div class="db-widget h-100">
                        <div class="db-widget-header db-widget-success">
                            <i class="fa fa-file-alt"></i>
                            <span>{{ custom_trans('Most visited pages', 'admin') }}</span>
                        </div>
                        <div class="db-widget-body p-0">
                            @if (count($report['top_pages']))
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ custom_trans('Page', 'admin') }}</th>
                                                <th class="text-end">{{ custom_trans('Views', 'admin') }}</th>
                                                <th class="text-end">{{ custom_trans('Visitors', 'admin') }}</th>
                                                <th class="text-end">{{ custom_trans('Avg. time', 'admin') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($report['top_pages'] as $page)
                                                <tr>
                                                    <td style="max-width: 420px;">
                                                        <div class="fw-semibold text-truncate" title="{{ $page['title'] }}">{{ $page['title'] ?: '(no title)' }}</div>
                                                        <a href="{{ url($page['path']) }}" target="_blank" rel="noopener"
                                                            class="small text-muted text-truncate d-block" dir="ltr">{{ urldecode($page['path']) }}</a>
                                                    </td>
                                                    <td class="text-end">{{ number_format($page['views']) }}</td>
                                                    <td class="text-end">{{ number_format($page['users']) }}</td>
                                                    <td class="text-end">{{ $formatDuration($page['avg_time']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="db-widget-empty">
                                    <i class="fa fa-file-alt"></i>
                                    <span>{{ custom_trans('No page views in this period yet', 'admin') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right now --}}
                <div class="col-lg-4">
                    <div class="db-widget h-100">
                        <div class="db-widget-header db-widget-warning">
                            <i class="fa fa-bolt"></i>
                            <span>{{ custom_trans('Pages being viewed now', 'admin') }}</span>
                        </div>
                        <div class="db-widget-body p-3" id="realtimePages">
                            @include('admin.site-analytics.partials.realtime-pages', ['pages' => $realtime['pages'] ?? []])
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-md-4 mb-4">
                {{-- Channels --}}
                <div class="col-lg-4">
                    <div class="db-widget h-100">
                        <div class="db-widget-header db-widget-info">
                            <i class="fa fa-route"></i>
                            <span>{{ custom_trans('Where visitors come from', 'admin') }}</span>
                        </div>
                        <div class="db-widget-body p-3">
                            @include('admin.site-analytics.partials.bars', [
                                'rows' => $report['channels'],
                                'icons' => $channelIcons,
                                'unit' => 'sessions',
                            ])
                        </div>
                    </div>
                </div>

                {{-- Countries --}}
                <div class="col-lg-4">
                    <div class="db-widget h-100">
                        <div class="db-widget-header db-widget-primary">
                            <i class="fa fa-globe"></i>
                            <span>{{ custom_trans('Countries', 'admin') }}</span>
                        </div>
                        <div class="db-widget-body p-3">
                            @include('admin.site-analytics.partials.bars', [
                                'rows' => $report['countries'],
                                'icons' => [],
                                'unit' => 'visitors',
                            ])
                        </div>
                    </div>
                </div>

                {{-- Devices --}}
                <div class="col-lg-4">
                    <div class="db-widget h-100">
                        <div class="db-widget-header db-widget-success">
                            <i class="fa fa-mobile-alt"></i>
                            <span>{{ custom_trans('Devices', 'admin') }}</span>
                        </div>
                        <div class="db-widget-body">
                            @if (count($report['devices']))
                                <div style="position: relative; height: 220px;">
                                    <canvas id="devicesChart" aria-label="Visitors by device" role="img"></canvas>
                                </div>
                            @else
                                <div class="db-widget-empty">
                                    <i class="fa fa-mobile-alt"></i>
                                    <span>{{ custom_trans('No data yet', 'admin') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-muted small mb-0">
                <i class="fa fa-info-circle me-1"></i>
                {{ custom_trans('Compared with the previous', 'admin') }} {{ $days }} {{ custom_trans('days', 'admin') }}.
                {{ custom_trans('Google Analytics can take 24-48 hours to process new data.', 'admin') }}
            </p>
        @endif
    </div>
@endsection

@if ($report)
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                const daily = @json($report['daily']);
                const devices = @json($report['devices']);
                const dark = document.documentElement.getAttribute('data-theme') === 'dark';
                const gridColor = dark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
                const textColor = dark ? '#c9ccd1' : '#6c757d';

                new Chart(document.getElementById('trafficChart'), {
                    type: 'line',
                    data: {
                        labels: daily.labels,
                        datasets: [{
                            label: @json(custom_trans('Page Views', 'admin')),
                            data: daily.views,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25,135,84,0.10)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: daily.labels.length > 31 ? 0 : 3,
                        }, {
                            label: @json(custom_trans('Visitors', 'admin')),
                            data: daily.users,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13,110,253,0.10)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: daily.labels.length > 31 ? 0 : 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { labels: { color: textColor } } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: textColor, maxTicksLimit: 10 } },
                            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } }
                        }
                    }
                });

                const devicesCanvas = document.getElementById('devicesChart');
                if (devicesCanvas && devices.length) {
                    new Chart(devicesCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: devices.map(d => d.label.charAt(0).toUpperCase() + d.label.slice(1)),
                            datasets: [{
                                data: devices.map(d => d.value),
                                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6f42c1', '#adb5bd'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: { legend: { position: 'bottom', labels: { color: textColor } } }
                        }
                    });
                }

                // Refresh "active right now" every minute (the server caches it for one minute too).
                const realtimeUrl = @json(route('admin.site-analytics.realtime'));
                const activeEl = document.getElementById('realtimeActiveUsers');
                const pagesEl = document.getElementById('realtimePages');
                const escapeHtml = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

                setInterval(function() {
                    if (document.hidden) {
                        return;
                    }
                    fetch(realtimeUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                        .then(r => r.ok ? r.json() : Promise.reject(r.status))
                        .then(function(data) {
                            activeEl.textContent = data.active_users;
                            if (!data.pages.length) {
                                pagesEl.innerHTML = '<div class="db-widget-empty"><i class="fa fa-bolt"></i><span>' +
                                    escapeHtml(@json(custom_trans('Nobody on the site right now', 'admin'))) + '</span></div>';
                                return;
                            }
                            pagesEl.innerHTML = data.pages.map(p =>
                                '<div class="d-flex justify-content-between align-items-center py-2 border-bottom">' +
                                '<span class="text-truncate me-2" title="' + escapeHtml(p.title) + '">' + escapeHtml(p.title || '(no title)') + '</span>' +
                                '<span class="badge bg-warning text-dark">' + p.users + '</span></div>'
                            ).join('');
                        })
                        .catch(function() {});
                }, 60000);
            })();
        </script>
    @endpush
@endif
