@php
    $prefix     = $idPrefix ?? 'filter';
    $isOffcanvas = $isOffcanvas ?? false;
@endphp

<div class="filter-panel">

    {{-- Header --}}
    <div class="filter-panel-header">
        <span class="filter-panel-title">
            <i class="fas fa-sliders-h me-2"></i>{{ custom_trans('filters', 'front') }}
        </span>
        <button type="button" class="filter-clear-top-btn" data-clear-filters>
            {{ custom_trans('clear_filters', 'front') }}
        </button>
    </div>

    {{-- Price filter --}}
    <div class="filter-group">
        <button class="filter-group-toggle collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#{{ $prefix }}-priceGroup"
            aria-expanded="true" aria-controls="{{ $prefix }}-priceGroup">
            <span>{{ custom_trans('price', 'front') }}</span>
            <i class="fas fa-chevron-down toggle-icon"></i>
        </button>
        <div class="collapse show" id="{{ $prefix }}-priceGroup">
            <div class="filter-group-body">

                <label class="filter-option" for="{{ $prefix }}-price-free">
                    <input class="filter-option-input price-filter" type="checkbox"
                        id="{{ $prefix }}-price-free" value="free">
                    <span class="filter-custom-check"></span>
                    <span class="filter-option-label">{{ custom_trans('free', 'front') }}</span>
                </label>

                <label class="filter-option" for="{{ $prefix }}-price-paid">
                    <input class="filter-option-input price-filter" type="checkbox"
                        id="{{ $prefix }}-price-paid" value="paid">
                    <span class="filter-custom-check"></span>
                    <span class="filter-option-label">{{ custom_trans('paid', 'front') }}</span>
                </label>

            </div>
        </div>
    </div>

    {{-- Rating filter --}}
    <div class="filter-group">
        <button class="filter-group-toggle collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#{{ $prefix }}-ratingGroup"
            aria-expanded="true" aria-controls="{{ $prefix }}-ratingGroup">
            <span>{{ custom_trans('rating', 'front') }}</span>
            <i class="fas fa-chevron-down toggle-icon"></i>
        </button>
        <div class="collapse show" id="{{ $prefix }}-ratingGroup">
            <div class="filter-group-body">

                @foreach ([5, 4, 3] as $stars)
                    <label class="filter-option filter-rating-row" for="{{ $prefix }}-rating-{{ $stars }}">
                        <input class="filter-option-input rating-filter" type="checkbox"
                            id="{{ $prefix }}-rating-{{ $stars }}" value="{{ $stars }}">
                        <span class="filter-custom-check"></span>
                        <span class="filter-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $stars ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </span>
                        <span class="filter-rating-label">
                            @if ($stars < 5) {{ $stars }}+ @else {{ $stars }} @endif
                            {{ custom_trans('stars', 'front') }}
                        </span>
                    </label>
                @endforeach

            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="filter-actions {{ $isOffcanvas ? 'd-flex gap-2' : '' }}">
        <button type="button"
            class="filter-btn-clear {{ $isOffcanvas ? 'flex-fill' : 'w-100 mb-2' }}"
            data-clear-filters>
            <i class="fas fa-undo me-2"></i>{{ custom_trans('clear_filters', 'front') }}
        </button>
        @if ($isOffcanvas)
            <button type="button" class="filter-btn-apply flex-fill" data-apply-filters>
                <i class="fas fa-check me-2"></i>{{ custom_trans('apply_filters', 'front') }}
            </button>
        @endif
    </div>

</div>
