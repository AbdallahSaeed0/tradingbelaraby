<!-- Subscription Bundles Section -->
@php
    // Only show bundles, not courses
    $allBundles = collect();
    
    // Add bundles only
    if (isset($subscriptionBundles) && $subscriptionBundles->count() > 0) {
        foreach ($subscriptionBundles as $bundle) {
            $allBundles->push((object)[
                'type' => 'bundle',
                'id' => $bundle->id,
                'slug' => $bundle->slug,
                'name' => $bundle->localized_name,
                'description' => $bundle->localized_description,
                'image_url' => $bundle->image_url,
                'price' => $bundle->price,
                'formatted_price' => $bundle->formatted_price,
                'original_price' => $bundle->original_price,
                'formatted_original_price' => $bundle->formatted_original_price,
                'is_featured' => $bundle->is_featured,
                'is_discounted' => $bundle->is_discounted,
                'courses_count' => $bundle->courses_count,
            ]);
        }
    }
@endphp

@if ($allBundles->count() > 0)
    <section class="courses-section position-relative py-5 bg-white">
        <!-- Background image on the left behind the cards -->
        <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-03.png" alt="an-img-01"
            class="courses-bg-img d-none d-md-block">
        <div class="container position-relative z-2">
            <!-- Slider controls -->
            <div class="d-flex justify-content-between mb-3">
                <div class="mb-4">
                    <span class="text-warning fw-bold mb-2 d-block fs-11">
                        <i class="fas fa-box-open"></i> {{ custom_trans('Bundle Deals', 'front') }}
                    </span>
                    <h2 class="fw-bold mb-3 fs-25">{{ custom_trans('Subscription Bundles', 'front') }}</h2>
                </div>
                @php
                    $direction = \App\Helpers\TranslationHelper::getFrontendLanguage()->direction ?? 'ltr';
                @endphp
                <div class="buts d-flex align-items-center">
                    @if ($direction === 'rtl')
                        <button class="btn btn-danger me-2 px-4 py-2 rounded-3 swiper-button-prev swiper-button-prev-bundles">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button class="btn btn-danger px-4 py-2 rounded-3 swiper-button-next swiper-button-next-bundles">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    @else
                        <button class="btn btn-danger me-2 px-4 py-2 rounded-3 swiper-button-prev swiper-button-prev-bundles">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <button class="btn btn-danger px-4 py-2 rounded-3 swiper-button-next swiper-button-next-bundles">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    @endif
                </div>
            </div>
            <!-- Swiper -->
            <div class="swiper subscriptionBundlesSwiper">
                <div class="swiper-wrapper">
                    @foreach ($allBundles as $item)
                        <div class="swiper-slide">
                            <div class="course-card-custom">
                                <div class="course-img-wrap">
                                    <img src="{{ $item->image_url }}" class="course-img"
                                        alt="{{ $item->name }}">

                                    @if ($item->is_featured)
                                        <span class="badge badge-green">{{ custom_trans('Featured', 'front') }}</span>
                                    @endif

                                    @if ($item->type === 'bundle')
                                        <span class="badge bg-primary" style="position: absolute; top: 10px; left: 10px; z-index: 2;">
                                            <i class="fa fa-box me-1"></i>{{ custom_trans('Bundle', 'front') }}
                                        </span>
                                    @endif

                                    @if ($item->is_discounted)
                                        <span class="price-badge">
                                            <span class="discounted">{{ $item->formatted_price }}</span>
                                            <span class="original">{{ $item->formatted_original_price }}</span>
                                        </span>
                                    @else
                                        <span class="price-badge">
                                            <span class="discounted">{{ $item->formatted_price }}</span>
                                        </span>
                                    @endif

                                    <div class="author-avatar d-flex align-items-center justify-content-center bg-primary text-white">
                                        <i class="fa fa-box"></i>
                                    </div>

                                    <div class="course-hover-icons">
                                        @auth
                                            <button class="icon-btn bundle-wishlist-btn" data-bundle-id="{{ $item->id }}">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        @else
                                            <button class="icon-btn" onclick="window.location.href='{{ route('login', 'front') }}'">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        @endauth
                                        <button class="icon-btn">
                                            <i class="fa-regular fa-bell"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="course-card-body">
                                    <h5 class="course-title">
                                        <a href="{{ route('bundles.show', $item->slug) }}"
                                            class="text-decoration-none text-dark">
                                            <i class="fa fa-box me-1"></i>{{ $item->name }}
                                        </a>
                                    </h5>
                                    <p class="course-desc">{{ Str::limit($item->description, 80) }}</p>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fa fa-book me-1"></i>{{ $item->courses_count }} {{ custom_trans('courses', 'front') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('bundles.show', $item->slug) }}"
                                        class="read-more">{{ custom_trans('Read More', 'front') }}
                                        &rarr;</a>
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                        class="book-icon" alt="book">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <script>
        // Initialize Subscription Bundles Swiper
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.subscriptionBundlesSwiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: {{ $allBundles->count() > 3 ? 'true' : 'false' }},
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.swiper-button-next-bundles',
                    prevEl: '.swiper-button-prev-bundles',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                    1200: {
                        slidesPerView: 4,
                    }
                }
            });
        });
    </script>
@endif
