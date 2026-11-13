{{-- Public-site pagination (the admin panel keeps bootstrap-5.blade.php). Set as the default view for
     non-admin requests in AppServiceProvider. Shows first, last and up to 5 pages around the current one. --}}
@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        if ($last <= 7) {
            $pages = range(1, $last);
        } else {
            $from = max(2, min($current - 2, $last - 5));
            $to = min($last - 1, max($current + 2, 6));
            $pages = array_values(array_unique(array_merge([1], range($from, $to), [$last])));
        }
    @endphp
    <nav class="site-pager" aria-label="{{ custom_trans('Pagination', 'front') }}">
        @if ($paginator->onFirstPage())
            <span class="site-pager-btn is-disabled" aria-hidden="true"><i class="fas fa-chevron-left site-pager-arrow"></i></span>
        @else
            <a class="site-pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ custom_trans('Previous', 'front') }}">
                <i class="fas fa-chevron-left site-pager-arrow"></i>
            </a>
        @endif

        @foreach ($pages as $k => $page)
            @if ($k > 0 && $page - $pages[$k - 1] > 1)
                <span class="site-pager-gap" aria-hidden="true">…</span>
            @endif
            @if ($page == $current)
                <span class="site-pager-btn is-current" aria-current="page">{{ $page }}</span>
            @else
                <a class="site-pager-btn" href="{{ $paginator->url($page) }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="site-pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ custom_trans('Next', 'front') }}">
                <i class="fas fa-chevron-right site-pager-arrow"></i>
            </a>
        @else
            <span class="site-pager-btn is-disabled" aria-hidden="true"><i class="fas fa-chevron-right site-pager-arrow"></i></span>
        @endif
    </nav>
@endif
