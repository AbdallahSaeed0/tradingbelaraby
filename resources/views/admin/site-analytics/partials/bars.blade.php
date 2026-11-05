@php $max = collect($rows)->max('value') ?: 1; @endphp
@forelse ($rows as $row)
    <div class="mb-3">
        <div class="d-flex justify-content-between small mb-1">
            <span>
                @if (!empty($icons[$row['label']]))
                    <i class="fa {{ $icons[$row['label']] }} text-muted me-1"></i>
                @endif
                {{ $row['label'] === '(not set)' ? custom_trans('Unknown', 'admin') : $row['label'] }}
            </span>
            <span class="fw-semibold">{{ number_format($row['value']) }}</span>
        </div>
        <div class="progress" style="height: 6px;" role="progressbar"
            aria-label="{{ $row['label'] }}: {{ $row['value'] }} {{ $unit }}">
            <div class="progress-bar" style="width: {{ round($row['value'] / $max * 100) }}%"></div>
        </div>
    </div>
@empty
    <div class="db-widget-empty">
        <i class="fa fa-chart-bar"></i>
        <span>{{ custom_trans('No data yet', 'admin') }}</span>
    </div>
@endforelse
