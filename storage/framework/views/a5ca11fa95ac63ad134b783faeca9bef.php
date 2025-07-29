<?php $__env->startSection('title', $category->name . ' - E-Class'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Category Banner -->
    <section class="category-banner position-relative d-flex align-items-center justify-content-center">
        <?php if($category->image): ?>
            <img src="<?php echo e(asset('storage/' . $category->image)); ?>" alt="<?php echo e($category->name); ?>"
                class="category-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <?php else: ?>
            <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png"
                alt="Banner" class="category-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <?php endif; ?>
        <div class="category-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-3 fw-bold text-white mb-3"><?php echo e($category->name); ?></h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="category-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <?php echo e(custom_trans('category_detail')); ?>

                </span>
            </div>
            <?php if($category->description): ?>
                <p class="text-white lead mb-0"><?php echo e($category->description); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Breadcrumb -->
    <section class="py-3 bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"
                            class="text-decoration-none"><?php echo e(custom_trans('home')); ?></a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('categories.index')); ?>"
                            class="text-decoration-none"><?php echo e(custom_trans('categories')); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo e($category->name); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Category Courses Section -->
    <section class="category-courses-section py-5 bg-white">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <div class="filter-section p-3 rounded-3 shadow-sm bg-white mb-4">
                        <h5 class="fw-bold mb-3"><?php echo e(custom_trans('filters')); ?></h5>

                        <!-- Price Filter -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2"><?php echo e(custom_trans('price')); ?></h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input price-filter" type="checkbox" id="priceFree" value="free">
                                <label class="form-check-label" for="priceFree"><?php echo e(custom_trans('free')); ?></label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input price-filter" type="checkbox" id="pricePaid" value="paid">
                                <label class="form-check-label" for="pricePaid"><?php echo e(custom_trans('paid')); ?></label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2"><?php echo e(custom_trans('rating')); ?></h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" id="rating5" value="5">
                                <label class="form-check-label" for="rating5">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    (5 <?php echo e(custom_trans('stars')); ?>)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" id="rating4" value="4">
                                <label class="form-check-label" for="rating4">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    & up (4+ <?php echo e(custom_trans('stars')); ?>)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" id="rating3" value="3">
                                <label class="form-check-label" for="rating3">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    & up (3+ <?php echo e(custom_trans('stars')); ?>)
                                </label>
                            </div>
                        </div>

                        <!-- Level filter removed as requested -->

                        <!-- Clear Filters -->
                        <button class="btn btn-outline-secondary btn-sm w-100" id="clearFilters">
                            <?php echo e(custom_trans('clear_filters')); ?>

                        </button>
                    </div>

                    <!-- Category stats removed as requested -->
                </div>

                <!-- Courses List/Grid -->
                <div class="col-lg-9">
                    <!-- Header with Sort Options -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-1"><?php echo e($category->name); ?> <?php echo e(custom_trans('courses')); ?></h4>
                            <p class="text-muted mb-0"><?php echo e($courses->total()); ?> <?php echo e(custom_trans('courses_found')); ?></p>
                        </div>
                        <div class="d-flex gap-2">
                            <select class="form-select sort-dropdown" style="max-width:150px;">
                                <option value="newest"><?php echo e(custom_trans('newest')); ?></option>
                                <option value="popular"><?php echo e(custom_trans('popular')); ?></option>
                                <option value="rating"><?php echo e(custom_trans('highest_rated')); ?></option>
                                <option value="price_low"><?php echo e(custom_trans('price_low_to_high')); ?></option>
                                <option value="price_high"><?php echo e(custom_trans('price_high_to_low')); ?></option>
                            </select>
                            <div class="btn-group" role="group" aria-label="View toggle">
                                <button class="btn btn-outline-primary active" id="listViewBtn" type="button">
                                    <i class="fas fa-list me-1"></i>
                                </button>
                                <button class="btn btn-outline-primary" id="gridViewBtn" type="button">
                                    <i class="fas fa-th me-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Grid -->
                    <div class="row g-4" id="coursesList">
                        <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="col-12 course-card-col">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="position-relative">
                                        <img src="<?php echo e($course->image_url); ?>" class="card-img-top"
                                            alt="<?php echo e($course->name); ?>" style="height: 200px; object-fit: cover;">
                                        <?php if($course->is_featured): ?>
                                            <span class="badge bg-success position-absolute top-0 start-0 m-2">
                                                <?php echo e(custom_trans('featured')); ?>

                                            </span>
                                        <?php endif; ?>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <?php if($course->price > 0): ?>
                                                <span
                                                    class="badge bg-info text-white"><?php echo e(number_format($course->price, 2)); ?>₹</span>
                                                <?php if($course->original_price > $course->price): ?>
                                                    <small class="text-decoration-line-through text-muted d-block">
                                                        <?php echo e(number_format($course->original_price, 2)); ?>₹
                                                    </small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-success"><?php echo e(custom_trans('free')); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title fw-bold mb-0 flex-grow-1"><?php echo e($course->name); ?></h5>
                                            <div class="d-flex align-items-center gap-2 ms-2">
                                                <?php if(auth()->guard()->check()): ?>
                                                    <button class="btn btn-outline-danger btn-sm wishlist-btn"
                                                        data-course-id="<?php echo e($course->id); ?>"
                                                        data-in-wishlist="<?php echo e(auth()->user()->hasInWishlist($course) ? 'true' : 'false'); ?>">
                                                        <i
                                                            class="fas fa-heart <?php echo e(auth()->user()->hasInWishlist($course) ? 'text-danger' : ''); ?>"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <div class="d-flex align-items-center">
                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                        <?php if($i <= $course->average_rating): ?>
                                                            <i class="fas fa-star text-warning small"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star text-warning small"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                    <small
                                                        class="ms-1 text-muted"><?php echo e(number_format($course->average_rating, 1)); ?></small>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="card-text text-muted mb-3 flex-grow-1">
                                            <?php echo e(Str::limit($course->description, 100)); ?>

                                        </p>

                                        <!-- Course Meta -->
                                        <div class="course-meta d-flex align-items-center mb-3">
                                            <div class="d-flex align-items-center me-3">
                                                <i class="fas fa-user text-muted me-1"></i>
                                                <small
                                                    class="text-muted"><?php echo e($course->instructor->name ?? 'Instructor'); ?></small>
                                            </div>
                                            <div class="d-flex align-items-center me-3">
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                <small class="text-muted"><?php echo e($course->duration ?? 'N/A'); ?></small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users text-muted me-1"></i>
                                                <small class="text-muted"><?php echo e($course->enrolled_students ?? 0); ?>

                                                    <?php echo e(custom_trans('students')); ?></small>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="<?php echo e(route('courses.show', $course->id)); ?>"
                                                class="btn btn-primary btn-sm">
                                                <?php echo e(custom_trans('view_details')); ?>

                                            </a>
                                            <?php if(auth()->guard()->check()): ?>
                                                <?php if(auth()->user()->enrollments()->where('course_id', $course->id)->exists()): ?>
                                                    <a href="<?php echo e(route('courses.learn', $course->id)); ?>"
                                                        class="btn btn-success btn-sm">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        <?php echo e(custom_trans('go_to_course')); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-success btn-sm enroll-btn"
                                                        data-course-id="<?php echo e($course->id); ?>">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        <?php echo e(custom_trans('enroll_now')); ?>

                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('login')); ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-graduation-cap me-1"></i>
                                                    <?php echo e(custom_trans('enroll_now')); ?>

                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12 text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                    <h4 class="fw-bold text-muted mb-2"><?php echo e(custom_trans('no_courses_found')); ?></h4>
                                    <p class="text-muted"><?php echo e(custom_trans('no_courses_in_category')); ?></p>
                                    <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-primary">
                                        <?php echo e(custom_trans('browse_all_categories')); ?>

                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if($courses->hasPages()): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <nav aria-label="Courses pagination">
                                    <?php echo e($courses->links()); ?>

                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/pages/categories.css')); ?>">
    <style>
        .category-banner {
            min-height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .category-banner-bg {
            object-fit: cover;
            opacity: 0.3;
        }

        .category-banner-overlay {
            background: rgba(0, 0, 0, 0.5);
        }

        .filter-section {
            border: 1px solid #f0f0f0;
        }

        .course-card-list {
            transition: all 0.3s ease;
        }

        .course-card-list:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .empty-state {
            padding: 3rem 1rem;
        }

        .price-badge .discounted {
            font-weight: bold;
            color: #28a745;
        }

        .price-badge .original {
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // View toggle functionality
        const listBtn = document.getElementById('listViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');
        const coursesList = document.getElementById('coursesList');

        listBtn?.addEventListener('click', () => {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');

            // Update course card columns for list view
            const courseCards = document.querySelectorAll('.course-card-col');
            courseCards.forEach(card => {
                card.className = 'col-12 course-card-col';
                card.classList.remove('d-none'); // Ensure course is visible
            });
        });

        gridBtn?.addEventListener('click', () => {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');

            // Update course card columns for grid view
            const courseCards = document.querySelectorAll('.course-card-col');
            courseCards.forEach(card => {
                card.className = 'col-12 col-md-6 col-lg-4 course-card-col';
                card.classList.remove('d-none'); // Ensure course is visible
            });
        });

        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filters = document.querySelectorAll('.price-filter, .rating-filter');
            const clearBtn = document.getElementById('clearFilters');

            // Ensure all courses are visible on page load
            const courseCards = document.querySelectorAll('.course-card-col');
            courseCards.forEach(card => {
                card.classList.remove('d-none');
            });

            filters.forEach(filter => {
                filter.addEventListener('change', applyFilters);
            });

            clearBtn?.addEventListener('click', clearAllFilters);
        });

        function applyFilters() {
            // Get all active filters
            const activeFilters = {
                price: Array.from(document.querySelectorAll('.price-filter:checked')).map(cb => cb.value),
                rating: Array.from(document.querySelectorAll('.rating-filter:checked')).map(cb => cb.value)
            };

            // Apply filters to course cards
            const courseCards = document.querySelectorAll('.course-card-col');

            courseCards.forEach(card => {
                let show = true;

                // Apply price filter
                if (activeFilters.price.length > 0) {
                    const priceElement = card.querySelector('.position-absolute .badge');
                    if (priceElement) {
                        const price = priceElement.textContent.trim();
                        const isFree = price === '<?php echo e(custom_trans('free')); ?>';
                        const isPaid = price !== '<?php echo e(custom_trans('free')); ?>';

                        if (activeFilters.price.includes('free') && !isFree) show = false;
                        if (activeFilters.price.includes('paid') && !isPaid) show = false;
                    }
                }

                // Apply rating filter
                if (activeFilters.rating.length > 0) {
                    const ratingElement = card.querySelector('.d-flex.align-items-center small');
                    if (ratingElement) {
                        const rating = parseFloat(ratingElement.textContent);
                        const hasValidRating = activeFilters.rating.some(r => rating >= parseInt(r));
                        if (!hasValidRating) show = false;
                    }
                }

                // Level filter removed as requested

                if (show) {
                    card.classList.remove('d-none');
                } else {
                    card.classList.add('d-none');
                }
            });
        }

        function clearAllFilters() {
            document.querySelectorAll('.price-filter, .rating-filter').forEach(cb => {
                cb.checked = false;
            });

            document.querySelectorAll('.course-card-col').forEach(card => {
                card.classList.remove('d-none');
            });
        }

        // Wishlist functionality
        document.addEventListener('DOMContentLoaded', function() {
            const wishlistButtons = document.querySelectorAll('.wishlist-btn');

            wishlistButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const inWishlist = this.dataset.inWishlist === 'true';
                    const icon = this.querySelector('i');

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        toastr.error('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    // Send AJAX request
                    fetch(`/wishlist/${courseId}/toggle`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                if (response.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                // Update button state
                                this.dataset.inWishlist = data.inWishlist;
                                if (data.inWishlist) {
                                    icon.classList.add('text-danger');
                                    toastr.success(data.message || 'Course added to wishlist!');
                                } else {
                                    icon.classList.remove('text-danger');
                                    toastr.info(data.message ||
                                        'Course removed from wishlist!');
                                }
                            } else if (data) {
                                toastr.error(data.message || 'An error occurred');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred. Please try again.');
                        });
                });
            });

            // Enrollment functionality
            const enrollButtons = document.querySelectorAll('.enroll-btn');

            enrollButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const originalText = this.innerHTML;

                    // Disable button and show loading
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enrolling...';

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        toastr.error('CSRF token not found. Please refresh the page.');
                        this.disabled = false;
                        this.innerHTML = originalText;
                        return;
                    }

                    // Send AJAX request to enroll
                    fetch(`/courses/${courseId}/enroll`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                if (response.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                // Replace button with "Go to Course" link
                                const goToCourseLink = document.createElement('a');
                                goToCourseLink.href = `/courses/${courseId}/learn`;
                                goToCourseLink.className = 'btn btn-success btn-sm';
                                goToCourseLink.innerHTML =
                                    '<i class="fas fa-graduation-cap me-1"></i><?php echo e(custom_trans('go_to_course')); ?>';

                                this.parentNode.replaceChild(goToCourseLink, this);

                                toastr.success(data.message ||
                                    'Successfully enrolled in course!');
                            } else if (data) {
                                toastr.error(data.message || 'An error occurred');
                                this.disabled = false;
                                this.innerHTML = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred. Please try again.');
                            this.disabled = false;
                            this.innerHTML = originalText;
                        });
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\courses-laravel\resources\views/pages/category-show.blade.php ENDPATH**/ ?>