@php
    $prefix = $idPrefix ?? 'filter';
    $isOffcanvas = $isOffcanvas ?? false;
@endphp

<div class="filter-section p-3 rounded-3 shadow-sm bg-white">
    <h5 class="fw-bold mb-3">{{ custom_trans('filters', 'front') }}</h5>

    <div class="mb-4">
        <h6 class="fw-semibold mb-2">{{ custom_trans('price', 'front') }}</h6>
        <div class="form-check mb-2">
            <input class="form-check-input price-filter" type="checkbox" id="{{ $prefix }}-price-free"
                value="free">
            <label class="form-check-label" for="{{ $prefix }}-price-free">
                {{ custom_trans('free', 'front') }}
            </label>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input price-filter" type="checkbox" id="{{ $prefix }}-price-paid"
                value="paid">
            <label class="form-check-label" for="{{ $prefix }}-price-paid">
                {{ custom_trans('paid', 'front') }}
            </label>
        </div>
    </div>

    <div class="mb-4">
        <h6 class="fw-semibold mb-2">{{ custom_trans('rating', 'front') }}</h6>
        <div class="form-check mb-2">
            <input class="form-check-input rating-filter" type="checkbox" id="{{ $prefix }}-rating-5"
                value="5">
            <label class="form-check-label" for="{{ $prefix }}-rating-5">
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                (5 {{ custom_trans('stars', 'front') }})
            </label>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input rating-filter" type="checkbox" id="{{ $prefix }}-rating-4"
                value="4">
            <label class="form-check-label" for="{{ $prefix }}-rating-4">
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="far fa-star text-warning"></i>
                &nbsp;(4+ {{ custom_trans('stars', 'front') }})
            </label>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input rating-filter" type="checkbox" id="{{ $prefix }}-rating-3"
                value="3">
            <label class="form-check-label" for="{{ $prefix }}-rating-3">
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="far fa-star text-warning"></i>
                <i class="far fa-star text-warning"></i>
                &nbsp;(3+ {{ custom_trans('stars', 'front') }})
            </label>
        </div>
    </div>

    <div class="{{ $isOffcanvas ? 'd-flex gap-2 mt-3' : 'mt-3' }}">
        <button type="button" class="btn btn-outline-secondary {{ $isOffcanvas ? 'flex-fill' : 'w-100' }}"
            data-clear-filters>
            <i class="fas fa-undo me-2"></i>{{ custom_trans('clear_filters', 'front') }}
        </button>
        @if ($isOffcanvas)
            <button type="button" class="btn btn-primary flex-fill" data-apply-filters>
                <i class="fas fa-filter me-2"></i>{{ custom_trans('apply_filters', 'front') }}
            </button>
        @endif
    </div>
</div>

