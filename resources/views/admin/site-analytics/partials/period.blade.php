{{--
    Period picker: presets + custom date range. Expects $period (App\Services\Analytics\Period)
    and $keep (query parameters to carry over, e.g. view / search / filters).
--}}
@php
    $keep = array_filter($keep ?? [], fn ($v) => $v !== null && $v !== '' && $v !== []);
    $flatKeep = [];
    foreach ($keep as $name => $value) {
        if (is_array($value)) {
            foreach ($value as $sub => $v) {
                $flatKeep["{$name}[{$sub}]"] = $v;
            }
        } else {
            $flatKeep[$name] = $value;
        }
    }
@endphp
<div class="d-flex flex-wrap align-items-center gap-2">
    <div class="btn-group btn-group-sm" role="group" aria-label="Period">
        @foreach (\App\Services\Analytics\Period::PRESETS as $preset)
            <a href="{{ request()->url() . '?' . http_build_query($keep + ['period' => $preset]) }}"
                class="btn {{ $period->key === (string) $preset ? 'btn-light' : 'btn-outline-light' }}">
                {{ $preset }} {{ custom_trans('days', 'admin') }}
            </a>
        @endforeach
        <button type="button" class="btn {{ $period->key === 'custom' ? 'btn-light' : 'btn-outline-light' }}"
            data-bs-toggle="collapse" data-bs-target="#customPeriod" aria-expanded="{{ $period->key === 'custom' ? 'true' : 'false' }}">
            <i class="fa fa-calendar-alt me-1"></i>{{ custom_trans('Custom', 'admin') }}
        </button>
    </div>
    <a href="{{ request()->fullUrlWithQuery(['refresh' => 1]) }}" class="btn btn-sm btn-outline-light"
        title="{{ custom_trans('Refresh data', 'admin') }}"><i class="fa fa-sync-alt"></i></a>
</div>
<div class="collapse {{ $period->key === 'custom' ? 'show' : '' }} mt-2" id="customPeriod">
    <form method="GET" action="{{ request()->url() }}" class="d-flex flex-wrap align-items-end gap-2">
        @foreach ($flatKeep as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
        <input type="hidden" name="period" value="custom">
        <div>
            <label class="form-label small mb-0 text-white-50" for="periodStart">{{ custom_trans('From', 'admin') }}</label>
            <input type="date" class="form-control form-control-sm" id="periodStart" name="start"
                value="{{ $period->startDate() }}" min="2015-08-14" max="{{ now()->format('Y-m-d') }}" required>
        </div>
        <div>
            <label class="form-label small mb-0 text-white-50" for="periodEnd">{{ custom_trans('To', 'admin') }}</label>
            <input type="date" class="form-control form-control-sm" id="periodEnd" name="end"
                value="{{ $period->endDate() }}" min="2015-08-14" max="{{ now()->format('Y-m-d') }}" required>
        </div>
        <button class="btn btn-sm btn-light" type="submit">{{ custom_trans('Apply', 'admin') }}</button>
    </form>
</div>
