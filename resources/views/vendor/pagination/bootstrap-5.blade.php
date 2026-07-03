@if ($paginator->hasPages())
<nav class="admin-pagination-nav" aria-label="Pagination">

    {{-- ── Mobile: prev / next only ── --}}
    <div class="d-flex justify-content-between d-sm-none admin-pagination-mobile">
        @if ($paginator->onFirstPage())
            <span class="admin-pg-btn admin-pg-btn--disabled">
                <i class="fa fa-chevron-left"></i> @lang('pagination.previous')
            </span>
        @else
            <a class="admin-pg-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <i class="fa fa-chevron-left"></i> @lang('pagination.previous')
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a class="admin-pg-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">
                @lang('pagination.next') <i class="fa fa-chevron-right"></i>
            </a>
        @else
            <span class="admin-pg-btn admin-pg-btn--disabled">
                @lang('pagination.next') <i class="fa fa-chevron-right"></i>
            </span>
        @endif
    </div>

    {{-- ── Desktop: full pagination ── --}}
    <div class="d-none d-sm-flex align-items-center justify-content-between admin-pagination-desktop">

        {{-- Results summary --}}
        <p class="admin-pg-summary">
            Showing
            <span class="admin-pg-highlight">{{ $paginator->firstItem() }}</span>
            –
            <span class="admin-pg-highlight">{{ $paginator->lastItem() }}</span>
            of
            <span class="admin-pg-highlight">{{ $paginator->total() }}</span>
            results
        </p>

        {{-- Page buttons --}}
        <ul class="admin-pg-list">

            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <li class="admin-pg-item admin-pg-item--disabled" aria-disabled="true">
                    <span class="admin-pg-link admin-pg-link--nav" aria-hidden="true">
                        <i class="fa fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="admin-pg-item">
                    <a class="admin-pg-link admin-pg-link--nav" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Number buttons --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="admin-pg-item admin-pg-item--dots" aria-disabled="true">
                        <span class="admin-pg-link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="admin-pg-item admin-pg-item--active" aria-current="page">
                                <span class="admin-pg-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="admin-pg-item">
                                <a class="admin-pg-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="admin-pg-item">
                    <a class="admin-pg-link admin-pg-link--nav" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="admin-pg-item admin-pg-item--disabled" aria-disabled="true">
                    <span class="admin-pg-link admin-pg-link--nav" aria-hidden="true">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                </li>
            @endif

        </ul>
    </div>

</nav>
@endif
