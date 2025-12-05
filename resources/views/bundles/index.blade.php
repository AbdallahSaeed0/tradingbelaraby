@extends('layouts.app')

@section('title', custom_trans('Course Bundles', 'front') . ' - ' .
    (\App\Models\MainContentSettings::getActive()?->site_name ?? custom_trans('Site Name', 'front')))

@section('content')
    <!-- Banner Section -->
    <section class="course-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="course-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="course-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative py-5 z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">{{ custom_trans('Course Bundles', 'front') }}</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="course-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home', 'front') }}"
                        class="text-dark text-decoration-none hover-primary">{{ custom_trans('home', 'front') }}</a>
                    &nbsp;|&nbsp;
                    {{ custom_trans('Bundles', 'front') }}
                </span>
            </div>
        </div>
    </section>

    <!-- Bundles Section -->
    <section class="bundles-section py-5 bg-light">
        <div class="container">
            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" action="{{ route('bundles.index') }}" id="bundleSearchForm">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="search" id="bundleSearchInput"
                                    placeholder="{{ custom_trans('Search bundles...', 'front') }}" 
                                    value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bundles Grid -->
            @if($bundles->count() > 0)
                <div class="row g-4">
                    @foreach($bundles as $bundle)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="position-relative">
                                    <img src="{{ $bundle->image_url }}" class="card-img-top" alt="{{ $bundle->localized_name }}" 
                                        style="height: 200px; object-fit: cover;">
                                    @if($bundle->is_featured)
                                        <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark">
                                            {{ custom_trans('Featured', 'front') }}
                                        </span>
                                    @endif
                                    @if($bundle->discount_percentage)
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                                            -{{ $bundle->discount_percentage }}%
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $bundle->localized_name }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($bundle->localized_description, 100) }}</p>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fa fa-book me-1"></i>{{ $bundle->courses->count() }} {{ custom_trans('courses', 'front') }}
                                        </small>
                                    </div>

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h4 class="text-primary mb-0">{{ $bundle->formatted_price }}</h4>
                                                @if($bundle->original_price)
                                                    <small class="text-muted text-decoration-line-through">
                                                        {{ $bundle->formatted_original_price }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('bundles.show', $bundle->slug) }}" class="btn btn-primary w-100">
                                            {{ custom_trans('View Bundle', 'front') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-5">
                    {{ $bundles->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ custom_trans('No bundles found', 'front') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('bundleSearchInput');
        const searchForm = document.getElementById('bundleSearchForm');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                // Wait 500ms after user stops typing before submitting
                searchTimeout = setTimeout(function() {
                    searchForm.submit();
                }, 500);
            });
        }
    });
</script>
@endpush

