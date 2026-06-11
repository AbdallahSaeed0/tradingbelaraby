@extends('layouts.app')

@section('title', (isset($category) ? \App\Helpers\TranslationHelper::getLocalizedContent($category->name,
    $category->name_ar) : 'Category') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')

    {{-- ===== BANNER ===== --}}
    <section class="category-banner position-relative d-flex align-items-end justify-content-center">
        @if ($category->image)
            <img src="{{ asset('storage/' . $category->image) }}"
                alt="{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}"
                class="category-banner-bg" width="1920" height="400">
        @else
            <div class="category-banner--default-fill" aria-hidden="true"></div>
        @endif

        <div class="banner-overlay-gradient"></div>

        <div class="container position-relative z-3 pb-5">
            {{-- Glassmorphism breadcrumb pill --}}
            <div class="d-flex justify-content-center mb-4 banner-animate-in" style="--delay:.05s">
                <span class="banner-glass-pill">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none opacity-75 hover-opacity-full">
                        <i class="fas fa-home me-1"></i>{{ custom_trans('home', 'front') }}
                    </a>
                    <span class="mx-2 opacity-40">/</span>
                    <span class="text-white opacity-90">{{ custom_trans('category_detail', 'front') }}</span>
                </span>
            </div>

            {{-- Title --}}
            <div class="text-center banner-animate-in" style="--delay:.15s">
                <h1 class="banner-title">
                    {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                </h1>
                @php
                    $localizedDescription = \App\Helpers\TranslationHelper::getLocalizedContent(
                        $category->description,
                        $category->description_ar,
                    );
                @endphp
                @if ($localizedDescription)
                    <p class="banner-desc">{{ $localizedDescription }}</p>
                @endif
            </div>

            {{-- Stats bar --}}
            <div class="banner-stats-bar banner-animate-in" style="--delay:.25s">
                <div class="banner-stat">
                    <span class="banner-stat-num">{{ $courses->total() }}</span>
                    <span class="banner-stat-lbl">{{ custom_trans('courses', 'front') }}</span>
                </div>
                @if (($category->total_enrolled_students ?? 0) > 0)
                    <div class="banner-stat-sep"></div>
                    <div class="banner-stat">
                        <span class="banner-stat-num">{{ number_format($category->total_enrolled_students) }}</span>
                        <span class="banner-stat-lbl">{{ custom_trans('students', 'front') }}</span>
                    </div>
                @endif
                @if (($category->average_rating ?? 0) > 0)
                    <div class="banner-stat-sep"></div>
                    <div class="banner-stat">
                        <i class="fas fa-star text-warning me-1" style="font-size:.85rem"></i>
                        <span class="banner-stat-num">{{ number_format($category->average_rating, 1) }}</span>
                        <span class="banner-stat-lbl">{{ custom_trans('rating', 'front') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ===== BREADCRUMB ===== --}}
    <nav class="breadcrumb-bar py-3" aria-label="breadcrumb">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>{{ custom_trans('home', 'front') }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('categories.index') }}" class="text-decoration-none">
                        {{ custom_trans('categories', 'front') }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                </li>
            </ol>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT ===== --}}
    <section class="category-courses-section py-5">
        <div class="container">

            {{-- Active filter chips --}}
            <div id="activeFilterChips" class="active-filters-bar mb-3 d-none">
                <span class="active-filters-label">
                    <i class="fas fa-filter me-1"></i>{{ custom_trans('filters', 'front') }}:
                </span>
                <div class="active-chip-list" id="filterChipsList"></div>
                <button type="button" class="chip-clear-all" data-clear-filters>
                    <i class="fas fa-times me-1"></i>{{ custom_trans('clear_filters', 'front') }}
                </button>
            </div>

            <div class="row g-4">

                {{-- ===== SIDEBAR ===== --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="filter-sidebar-sticky">
                        @include('pages.partials.category-filters', ['idPrefix' => 'desktop'])
                    </div>
                </div>

                {{-- ===== COURSES AREA ===== --}}
                <div class="col-lg-9">

                    {{-- Mobile filter bar --}}
                    <div class="d-lg-none mb-4">
                        <div class="mobile-filter-trigger d-flex gap-2">
                            <button class="btn btn-outline-primary flex-grow-1" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#mobileFilterOffcanvas"
                                aria-controls="mobileFilterOffcanvas">
                                <i class="fas fa-sliders-h me-2"></i>
                                {{ custom_trans('filters', 'front') }}
                                <span class="ms-2 badge bg-primary rounded-pill d-none" id="mobileFilterCount">0</span>
                            </button>
                            <button class="btn btn-outline-secondary" type="button"
                                data-bs-toggle="collapse" data-bs-target="#mobileSortOptions"
                                aria-expanded="false" aria-controls="mobileSortOptions">
                                <i class="fas fa-sort-amount-down me-1"></i>{{ custom_trans('sort', 'front') }}
                            </button>
                        </div>
                        <div class="collapse mt-3" id="mobileSortOptions">
                            <select class="form-select sort-dropdown">
                                <option value="newest">{{ custom_trans('newest', 'front') }}</option>
                                <option value="popular">{{ custom_trans('popular', 'front') }}</option>
                                <option value="rating">{{ custom_trans('highest_rated', 'front') }}</option>
                                <option value="price_low">{{ custom_trans('price_low_to_high', 'front') }}</option>
                                <option value="price_high">{{ custom_trans('price_high_to_low', 'front') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Courses header --}}
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div>
                            <h4 class="fw-bold mb-1">
                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                {{ custom_trans('courses', 'front') }}
                            </h4>
                            <p class="text-muted mb-0" style="font-size:.9rem">
                                <span id="visibleCoursesCount">{{ $courses->total() }}</span>
                                {{ custom_trans('courses_found', 'front') }}
                            </p>
                        </div>
                        <div class="d-none d-md-flex align-items-center gap-2">
                            <select class="form-select sort-dropdown" style="width:auto;min-width:150px">
                                <option value="newest">{{ custom_trans('newest', 'front') }}</option>
                                <option value="popular">{{ custom_trans('popular', 'front') }}</option>
                                <option value="rating">{{ custom_trans('highest_rated', 'front') }}</option>
                                <option value="price_low">{{ custom_trans('price_low_to_high', 'front') }}</option>
                                <option value="price_high">{{ custom_trans('price_high_to_low', 'front') }}</option>
                            </select>
                            <div class="view-toggle-group" role="group" aria-label="{{ custom_trans('View', 'front') }}">
                                <button class="btn" id="listViewBtn" type="button" title="{{ custom_trans('list', 'front') }}">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button class="btn active" id="gridViewBtn" type="button" title="{{ custom_trans('grid', 'front') }}">
                                    <i class="fas fa-th-large"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ===== COURSES GRID ===== --}}
                    <div class="row g-4" id="coursesList">
                        @forelse($courses as $course)
                            @php
                                $avgRating    = $course->average_rating ?? 0;
                                $lessonsCount = $course->total_lessons_count ?? 0;
                                $isFree       = $course->price <= 0;
                                $isEnrolled   = auth()->check() && auth()->user()->hasCourseAccess($course->id);
                                $inWishlist   = auth()->check() && auth()->user()->hasInWishlist($course);
                                $enrolledCnt  = $course->enrolled_students ?? 0;
                            @endphp

                            <div class="col-12 col-md-6 col-lg-4 course-card-col"
                                data-pricetype="{{ $isFree ? 'free' : 'paid' }}"
                                data-rating="{{ number_format($avgRating, 1) }}">

                                <div class="course-card h-100">

                                    {{-- IMAGE --}}
                                    <div class="course-card-img-wrap">
                                        <a href="{{ route('courses.show', $course->id) }}" class="d-block h-100">
                                            @if ($course->image)
                                                <img src="{{ optimized_image_url($course->image, 360, 180) }}"
                                                    alt="{{ $course->localized_name }}"
                                                    class="course-card-img" width="360" height="180" loading="lazy"
                                                    srcset="{{ optimized_image_url($course->image, 360, 180) }} 360w, {{ optimized_image_url($course->image, 720, 360) }} 720w"
                                                    sizes="(max-width: 768px) 100vw, 360px">
                                            @else
                                                <img src="{{ $course->image_url }}"
                                                    alt="{{ $course->localized_name }}"
                                                    class="course-card-img" width="360" height="180" loading="lazy">
                                            @endif
                                        </a>

                                        {{-- Hover overlay --}}
                                        <div class="course-img-overlay">
                                            <a href="{{ route('courses.show', $course->id) }}" class="overlay-view-btn">
                                                <i class="fas fa-eye me-2"></i>{{ custom_trans('view_details', 'front') }}
                                            </a>
                                        </div>

                                        {{-- Featured badge --}}
                                        @if ($course->is_featured)
                                            <span class="cc-badge cc-badge-featured">
                                                <i class="fas fa-crown me-1"></i>{{ custom_trans('featured', 'front') }}
                                            </span>
                                        @endif

                                        {{-- Wishlist (floating on image) --}}
                                        @auth
                                            <button class="wishlist-btn cc-wishlist-btn"
                                                data-course-id="{{ $course->id }}"
                                                data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}"
                                                aria-label="Wishlist">
                                                <i class="fas fa-heart {{ $inWishlist ? 'text-danger' : '' }}"></i>
                                            </button>
                                        @endauth

                                        {{-- Price --}}
                                        <div class="cc-price-wrap">
                                            @if ($isFree)
                                                <span class="cc-price cc-price-free">{{ custom_trans('free', 'front') }}</span>
                                            @else
                                                @if ($course->original_price > $course->price)
                                                    <span class="cc-price cc-price-original">{{ number_format($course->original_price, 0) }}</span>
                                                @endif
                                                <span class="cc-price cc-price-paid">{{ number_format($course->price, 0) }} SAR</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- BODY --}}
                                    <div class="course-card-body">

                                        {{-- Title --}}
                                        <h5 class="cc-title">
                                            <a href="{{ route('courses.show', $course->id) }}">
                                                {{ $course->localized_name }}
                                            </a>
                                        </h5>

                                        {{-- Rating --}}
                                        <div class="cc-rating">
                                            @if ($avgRating > 0)
                                                <div class="cc-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fa{{ $i <= $avgRating ? 's' : 'r' }} fa-star"></i>
                                                    @endfor
                                                </div>
                                                <span class="cc-rating-val">{{ number_format($avgRating, 1) }}</span>
                                            @else
                                                <span class="cc-new-badge">
                                                    <i class="fas fa-certificate me-1"></i>{{ custom_trans('new', 'front') }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Meta --}}
                                        <div class="cc-meta">
                                            <span class="cc-meta-item">
                                                <i class="fas fa-book-open"></i>
                                                {{ $lessonsCount }} {{ custom_trans('lessons', 'front') }}
                                            </span>
                                            @if ($enrolledCnt > 0)
                                                <span class="cc-meta-item">
                                                    <i class="fas fa-users"></i>
                                                    {{ number_format($enrolledCnt) }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Actions --}}
                                        <div class="cc-actions">
                                            @auth
                                                @if ($isEnrolled)
                                                    <a href="{{ route('courses.learn', $course->id) }}"
                                                        class="cc-btn cc-btn-primary w-100">
                                                        <i class="fas fa-graduation-cap me-2"></i>
                                                        {{ custom_trans('go_to_course', 'front') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('courses.show', $course->id) }}"
                                                        class="cc-btn cc-btn-outline">
                                                        {{ custom_trans('view_details', 'front') }}
                                                    </a>
                                                    <button class="cc-btn cc-btn-primary category-enroll-btn"
                                                        data-course-id="{{ $course->id }}"
                                                        data-enroll-type="{{ $isFree ? 'free' : 'paid' }}">
                                                        <i class="fas fa-{{ $isFree ? 'graduation-cap' : 'shopping-cart' }} me-1"></i>
                                                        <span>{{ custom_trans($isFree ? 'enroll_now' : 'add_to_cart', 'front') }}</span>
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('courses.show', $course->id) }}"
                                                    class="cc-btn cc-btn-outline">
                                                    {{ custom_trans('view_details', 'front') }}
                                                </a>
                                                <a href="{{ route('login') }}" class="cc-btn cc-btn-primary">
                                                    <i class="fas fa-graduation-cap me-1"></i>
                                                    <span>{{ custom_trans('enroll_now', 'front') }}</span>
                                                </a>
                                            @endauth
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-courses-state">
                                    <div class="empty-icon-wrap">
                                        <i class="fas fa-search empty-icon"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2">{{ custom_trans('no_courses_found', 'front') }}</h4>
                                    <p class="text-muted mb-4">{{ custom_trans('no_courses_in_category', 'front') }}</p>
                                    <a href="{{ route('categories.index') }}" class="btn btn-primary px-4 py-2">
                                        {{ custom_trans('browse_all_categories', 'front') }}
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if ($courses->hasPages())
                        <div class="row mt-5">
                            <div class="col-12">
                                <nav aria-label="Courses pagination">
                                    {{ $courses->links() }}
                                </nav>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Mobile filter offcanvas --}}
        <div class="offcanvas offcanvas-end mobile-filter-offcanvas" tabindex="-1" id="mobileFilterOffcanvas"
            aria-labelledby="mobileFilterOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title fw-bold" id="mobileFilterOffcanvasLabel">
                    <i class="fas fa-sliders-h me-2"></i>{{ custom_trans('filters', 'front') }}
                </h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="offcanvas-body">
                @include('pages.partials.category-filters', [
                    'idPrefix' => 'mobile',
                    'isOffcanvas' => true,
                ])
            </div>
        </div>
    </section>

    {{-- Cart success modal --}}
    <div class="modal fade" id="categoryCartSuccessModal" tabindex="-1"
        aria-labelledby="categoryCartSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:linear-gradient(135deg,#28a745,#20c997);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0" id="categoryCartSuccessModalLabel">
                            {{ custom_trans('Course added to cart', 'front') }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted mb-0">
                        {{ custom_trans('You can continue shopping or proceed to checkout to complete your purchase.', 'front') }}
                    </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        {{ custom_trans('Continue shopping', 'front') }}
                    </button>
                    <a href="{{ route('checkout.index') }}" class="btn btn-orange fw-bold px-4">
                        {{ custom_trans('Go to checkout', 'front') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/categories.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/categories.css') }}">
    @endpush
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── View Toggle ────────────────────────────────────────────────────────
    const listBtn   = document.getElementById('listViewBtn');
    const gridBtn   = document.getElementById('gridViewBtn');
    const coursesList = document.getElementById('coursesList');

    listBtn?.addEventListener('click', () => {
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        document.querySelectorAll('.course-card-col').forEach(c => {
            c.className = 'col-12 course-card-col';
            c.dataset.pricetype && c.classList.remove('d-none');
            c.style.display = '';
        });
        coursesList.classList.add('list-view-active');
    });

    gridBtn?.addEventListener('click', () => {
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        document.querySelectorAll('.course-card-col').forEach(c => {
            c.className = 'col-12 col-md-6 col-lg-4 course-card-col';
            c.dataset.pricetype && c.classList.remove('d-none');
            c.style.display = '';
        });
        coursesList.classList.remove('list-view-active');
        applyFilters(); // re-apply hidden state after class reset
    });

    // ─── Filters ─────────────────────────────────────────────────────────────
    const allFilters   = document.querySelectorAll('.price-filter, .rating-filter');
    const clearButtons = document.querySelectorAll('[data-clear-filters]');
    const applyButtons = document.querySelectorAll('[data-apply-filters]');

    allFilters.forEach(f => f.addEventListener('change', () => {
        applyFilters();
        updateChips();
        updateFilterBadge();
    }));

    clearButtons.forEach(btn => btn.addEventListener('click', () => {
        clearAllFilters();
        const offcanvas = btn.closest('.offcanvas');
        if (offcanvas) bootstrap.Offcanvas.getInstance(offcanvas)?.hide();
    }));

    applyButtons.forEach(btn => btn.addEventListener('click', () => {
        applyFilters();
        updateChips();
        updateFilterBadge();
        const offcanvas = btn.closest('.offcanvas');
        if (offcanvas) bootstrap.Offcanvas.getInstance(offcanvas)?.hide();
    }));

    function applyFilters() {
        const priceVals  = Array.from(document.querySelectorAll('.price-filter:checked')).map(c => c.value);
        const ratingVals = Array.from(document.querySelectorAll('.rating-filter:checked')).map(c => parseFloat(c.value));

        let visible = 0;
        document.querySelectorAll('.course-card-col').forEach(card => {
            let show = true;

            if (priceVals.length > 0) {
                const pt = card.dataset.pricetype;
                if (priceVals.includes('free') && priceVals.includes('paid')) {
                    // both checked — show all
                } else if (priceVals.includes('free') && pt !== 'free') {
                    show = false;
                } else if (priceVals.includes('paid') && pt !== 'paid') {
                    show = false;
                }
            }

            if (ratingVals.length > 0 && show) {
                const r = parseFloat(card.dataset.rating || '0');
                if (!ratingVals.some(min => r >= min)) show = false;
            }

            if (show) {
                card.classList.remove('d-none');
                visible++;
            } else {
                card.classList.add('d-none');
            }
        });

        const countEl = document.getElementById('visibleCoursesCount');
        if (countEl) countEl.textContent = visible;
    }

    function clearAllFilters() {
        document.querySelectorAll('.price-filter, .rating-filter').forEach(c => c.checked = false);
        document.querySelectorAll('.course-card-col').forEach(c => c.classList.remove('d-none'));
        const countEl = document.getElementById('visibleCoursesCount');
        if (countEl) countEl.textContent = document.querySelectorAll('.course-card-col').length;
        updateChips();
        updateFilterBadge();
    }

    function updateFilterBadge() {
        const total = document.querySelectorAll('.price-filter:checked, .rating-filter:checked').length;
        const badge = document.getElementById('mobileFilterCount');
        if (!badge) return;
        if (total > 0) {
            badge.textContent = total;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function updateChips() {
        const bar       = document.getElementById('activeFilterChips');
        const chipsList = document.getElementById('filterChipsList');
        if (!bar || !chipsList) return;

        chipsList.innerHTML = '';
        const checked = document.querySelectorAll('.price-filter:checked, .rating-filter:checked');

        if (checked.length === 0) {
            bar.classList.add('d-none');
            return;
        }
        bar.classList.remove('d-none');

        checked.forEach(input => {
            const label = document.querySelector(`label[for="${input.id}"]`);
            const text  = label ? label.textContent.trim().replace(/\s+/g, ' ') : input.value;
            const chip  = document.createElement('span');
            chip.className = 'active-chip';
            chip.innerHTML = `${text} <button class="chip-remove" aria-label="Remove filter">&times;</button>`;
            chip.querySelector('.chip-remove').addEventListener('click', () => {
                input.checked = false;
                applyFilters();
                updateChips();
                updateFilterBadge();
            });
            chipsList.appendChild(chip);
        });
    }

    // ─── Enroll ──────────────────────────────────────────────────────────────
    document.querySelectorAll('.category-enroll-btn').forEach(button => {
        button.addEventListener('click', function () {
            const courseId   = this.dataset.courseId;
            const enrollType = this.dataset.enrollType || 'free';
            const original   = this.innerHTML;
            const csrf       = document.querySelector('meta[name="csrf-token"]');

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ custom_trans('processing', 'front') }}';

            if (!csrf) {
                toastr.error('CSRF token not found.');
                this.disabled = false;
                this.innerHTML = original;
                return;
            }

            const reset = () => { this.disabled = false; this.innerHTML = original; };

            if (enrollType === 'paid') {
                fetch(`/cart/add/${courseId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf.getAttribute('content') },
                    body: JSON.stringify({})
                })
                .then(r => {
                    if (r.status === 401) { window.location.href = '/login'; return; }
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    return r.json();
                })
                .then(data => {
                    if (data?.success) {
                        const modal = document.getElementById('categoryCartSuccessModal');
                        if (modal && typeof bootstrap !== 'undefined') new bootstrap.Modal(modal).show();
                        else toastr.success(data.message || '{{ custom_trans('Course added to cart successfully.', 'front') }}');
                    } else if (data) {
                        toastr.info(data.message || '{{ custom_trans('Unable to add course to cart.', 'front') }}');
                    }
                    reset();
                })
                .catch(() => { toastr.error('{{ custom_trans('An unexpected error occurred. Please try again later.', 'front') }}'); reset(); });
                return;
            }

            fetch(`/courses/${courseId}/enroll`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf.getAttribute('content') },
                body: JSON.stringify({})
            })
            .then(r => {
                if (r.status === 401) { window.location.href = '/login'; return; }
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (data?.success) {
                    this.className = 'cc-btn cc-btn-primary';
                    this.innerHTML = '<i class="fas fa-check me-1"></i>{{ custom_trans('enrolled', 'front') }}';
                    this.disabled = true;
                    toastr.success(data.message || '{{ custom_trans('Successfully enrolled in course!', 'front') }}');
                    setTimeout(() => {
                        const link = document.createElement('a');
                        link.href = `/courses/${courseId}/learn`;
                        link.className = 'cc-btn cc-btn-primary';
                        link.innerHTML = '<i class="fas fa-graduation-cap me-1"></i>{{ custom_trans('go_to_course', 'front') }}';
                        this.parentNode.replaceChild(link, this);
                    }, 2000);
                } else if (data) {
                    toastr.error(data.message || 'Error');
                    reset();
                }
            })
            .catch(() => { toastr.error('An error occurred.'); reset(); });
        });
    });

});
</script>
@endpush
