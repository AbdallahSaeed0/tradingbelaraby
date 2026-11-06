{{-- Tabs across all Site Analytics pages. Expects $reports, $active ('overview' or a report key), $periodQuery. --}}
<nav class="mb-4" aria-label="{{ custom_trans('Analytics reports', 'admin') }}">
    <ul class="nav nav-pills flex-wrap gap-1">
        <li class="nav-item">
            <a class="nav-link {{ $active === 'overview' ? 'active' : 'bg-white border' }}"
                href="{{ route('admin.site-analytics.index', isset($periodQuery['period']) && in_array((int) $periodQuery['period'], [7, 28, 90], true) ? ['days' => $periodQuery['period']] : []) }}">
                <i class="fa fa-th-large me-1"></i>{{ custom_trans('Overview', 'admin') }}
            </a>
        </li>
        @foreach ($reports as $key => $r)
            <li class="nav-item">
                <a class="nav-link {{ $active === $key ? 'active' : 'bg-white border' }}"
                    href="{{ route('admin.site-analytics.report', ['reportKey' => $key] + $periodQuery) }}">
                    <i class="fa {{ $r['icon'] }} me-1"></i>{{ custom_trans($r['title'], 'admin') }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
