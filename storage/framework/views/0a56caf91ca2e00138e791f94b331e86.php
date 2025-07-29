<?php $__env->startSection('title', $course->name . ' - E-Class'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/pages/course-detail.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Banner Section -->
    <section class="course-banner position-relative d-flex align-items-center justify-content-center"
        style="min-height: 340px;">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png"
            alt="Banner" class="course-banner-bg position-absolute w-100 h-100 top-0 start-0"
            style="object-fit:cover; z-index:1;">
        <div class="course-banner-overlay position-absolute w-100 h-100 top-0 start-0"
            style="background:rgba(24,49,63,0.65); z-index:2;"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">Course Detail</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="course-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">Home &nbsp;|&nbsp;
                    Course Details</span>
            </div>
        </div>
    </section>

    <!-- Course Detail Section -->
    <section class="course-detail-section py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Left Side -->
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4"><?php echo e($course->name); ?></h2>
                    <img src="<?php echo e($course->image_url); ?>" class="img-fluid rounded-4 mb-4" alt="<?php echo e($course->name); ?>">
                    <div class="what-learn-box p-4 rounded-4 border">
                        <h5 class="fw-bold mb-3">What learn</h5>
                        <?php if(!empty($course->what_to_learn) && is_array($course->what_to_learn)): ?>
                            <div class="row g-3">
                                <?php $__currentLoopData = array_chunk($course->what_to_learn, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(!empty($item)): ?>
                                                <div class="d-flex align-items-start mb-2">
                                                    <span class="learn-check-icon me-2"><i class="fa fa-check"></i></span>
                                                    <span><?php echo e($item); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p class="mb-0">Not Found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Meetings Requirements & Description -->
                    <div class="mt-5">
                        <h4 class="fw-bold mb-3">Meetings Requirements</h4>
                        <?php if(!empty($course->requirements)): ?>
                            <div class="mb-3 text-muted" style="font-size:1rem;"><?php echo e($course->requirements); ?></div>
                        <?php else: ?>
                            <div class="mb-3 text-muted" style="font-size:1rem;">
                                <i class="fas fa-info-circle me-2"></i>Not Found
                            </div>
                        <?php endif; ?>
                        <h4 class="fw-bold mb-3">Description</h4>
                        <?php if(!empty($course->description)): ?>
                            <div class="mb-4">
                                <?php echo nl2br(e($course->description)); ?>

                            </div>
                        <?php else: ?>
                            <div class="mb-4 text-muted">
                                <i class="fas fa-info-circle me-2"></i>Not Found
                            </div>
                        <?php endif; ?>
                        <!-- FAQ Section -->
                        <h4 class="fw-bold mb-4">Frequently Asked Questions</h4>
                        <?php if(!empty($course->faq_course) && is_array($course->faq_course)): ?>
                            <section class="faq-section">
                                <div class="container px-0">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="accordion faq-accordion" id="accordionExample">
                                                <?php $__currentLoopData = $course->faq_course; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if(!empty($faq['question']) && !empty($faq['answer'])): ?>
                                                        <div class="accordion-item mb-3">
                                                            <h2 class="accordion-header">
                                                                <button
                                                                    class="accordion-button <?php echo e($index === 0 ? '' : 'collapsed'); ?> faq-accordion-header"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse<?php echo e($index); ?>"
                                                                    <?php echo e($index === 0 ? 'aria-expanded="true"' : ''); ?>

                                                                    aria-controls="collapse<?php echo e($index); ?>">
                                                                    <span class="fw-bold"><?php echo e($faq['question']); ?></span>
                                                                    <span class="ms-auto faq-chevron"><i
                                                                            class="fa-solid fa-chevron-down"></i></span>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse<?php echo e($index); ?>"
                                                                class="accordion-collapse collapse <?php echo e($index === 0 ? 'show' : ''); ?>"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body faq-accordion-body">
                                                                    <?php echo e($faq['answer']); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-question-circle fa-2x mb-2"></i>
                                <p class="mb-0">Not Found</p>
                            </div>
                        <?php endif; ?>
                        <!-- About Instructor Section -->
                        <section class="about-instructor-section mt-5">
                            <h4 class="fw-bold mb-4">About Instructor</h4>
                            <?php if($course->instructor): ?>
                                <div class="row align-items-center g-4">
                                    <div class="col-auto">
                                        <?php if($course->instructor->avatar): ?>
                                            <img src="<?php echo e(asset('storage/' . $course->instructor->avatar)); ?>"
                                                alt="<?php echo e($course->instructor->name); ?>"
                                                class="rounded-circle instructor-img">
                                        <?php else: ?>
                                            <div
                                                class="rounded-circle instructor-img bg-primary text-white d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user fa-2x"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold fs-5 mb-1 text-primary">
                                            <?php echo e($course->instructor->name ?? 'Not Found'); ?></div>
                                        <div class="text-muted mb-2 instructor-subtitle">
                                            <?php echo e($course->instructor->title ?? 'Not Found'); ?></div>
                                        <div class="text-muted instructor-desc">
                                            <?php echo e($course->instructor->bio ?? 'Not Found'); ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-user-tie fa-2x mb-2"></i>
                                    <p class="mb-0">Not Found</p>
                                </div>
                            <?php endif; ?>
                        </section>
                        <!-- Student Feedback Section -->
                        <section class="student-feedback-section mt-5">
                            <h4 class="fw-bold mb-4">Student Feedback</h4>
                            <?php if($course->ratings && $course->ratings->count() > 0): ?>
                                <div class="row align-items-center g-4">
                                    <div class="col-auto text-center">
                                        <div class="display-4 fw-bold mb-0 feedback-rating">
                                            <?php echo e(number_format($course->average_rating ?? 0, 1)); ?></div>
                                        <div class="text-muted feedback-rating-label">Course Rating</div>
                                    </div>
                                    <div class="col">
                                        <?php for($i = 5; $i >= 1; $i--): ?>
                                            <?php
                                                $ratingCount = $course->ratings->where('rating', $i)->count();
                                                $percentage =
                                                    $course->ratings->count() > 0
                                                        ? ($ratingCount / $course->ratings->count()) * 100
                                                        : 0;
                                            ?>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="progress flex-grow-1 me-2 feedback-progress">
                                                    <div class="progress-bar bg-dark" role="progressbar"
                                                        style="width: <?php echo e($percentage); ?>%;"
                                                        aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <span
                                                    class="ms-2 text-muted feedback-percent"><?php echo e(round($percentage)); ?>%</span>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-star fa-2x mb-2"></i>
                                    <p class="mb-0">Not Found</p>
                                </div>
                            <?php endif; ?>
                        </section>
                        <hr class="my-5">
                        <!-- Reviews Section -->
                        <section class="reviews-section mb-5">
                            <h4 class="fw-bold mb-4">Reviews</h4>
                            <div class="review-stars-list mb-3">
                                <?php $categories = ['learn', 'price', 'value']; ?>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row align-items-center mb-2 gx-3">
                                        <div class="col-auto review-label text-capitalize"><?php echo e($cat); ?></div>
                                        <div class="col review-stars">
                                            <div class="star-rating" data-category="<?php echo e($cat); ?>">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fa fa-star star-item"
                                                        data-rating="<?php echo e($i); ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="mb-3">
                                <label for="reviewText" class="form-label">Write review:</label>
                                <textarea class="form-control review-textarea" id="reviewText" rows="3" placeholder=""></textarea>
                            </div>
                            <button class="btn btn-orange px-5 py-2 fw-bold review-submit-btn"
                                type="button">Submit</button>
                        </section>
                    </div>
                </div>
                <!-- Right Side -->
                <div class="col-lg-4">
                    <div class="course-features-box rounded-4 shadow-sm bg-white mb-4">
                        <div class="course-features-header p-3 rounded-top-4 text-white fw-bold"
                            style="background:#156481;">Course Features</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <span class="fs-5 text-orange fw-bold">
                                    <?php if($course->price > 0): ?>
                                        ₹<?php echo e(number_format($course->price, 2)); ?>

                                        <?php if($course->original_price > $course->price): ?>
                                            <span
                                                class="fs-6 text-decoration-line-through text-muted ms-2">₹<?php echo e(number_format($course->original_price, 2)); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo e(custom_trans('free')); ?>

                                    <?php endif; ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-home me-2 text-orange"></i> <span
                                    class="fw-bold"><?php echo e(custom_trans('instructor')); ?>:</span>
                                <span class="ms-auto"><?php echo e($course->instructor->name ?? 'Not Found'); ?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-book me-2 text-orange"></i> <span
                                    class="fw-bold"><?php echo e(custom_trans('lectures')); ?>:</span>
                                <span
                                    class="ms-auto"><?php echo e($course->sections->sum(function ($section) {return $section->lectures->count();})); ?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-clock me-2 text-orange"></i> <span
                                    class="fw-bold"><?php echo e(custom_trans('duration')); ?>:</span>
                                <span class="ms-auto"><?php echo e($course->duration ?? 'Not Found'); ?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-user me-2 text-orange"></i> <span
                                    class="fw-bold"><?php echo e(custom_trans('enrolled')); ?>:</span>
                                <span class="ms-auto"><?php echo e($course->enrolled_students ?? 0); ?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-globe me-2 text-orange"></i> <span
                                    class="fw-bold"><?php echo e(custom_trans('language')); ?>:</span>
                                <span class="ms-auto"><?php echo e($course->language ?? 'Not Found'); ?></span>
                            </li>
                        </ul>
                        <div class="p-3">
                            <?php if(auth()->guard()->check()): ?>
                                <?php if(auth()->user()->enrollments()->where('course_id', $course->id)->exists()): ?>
                                    <a href="<?php echo e(route('courses.learn', $course->id)); ?>"
                                        class="btn btn-orange w-100 fw-bold mb-3"><?php echo e(custom_trans('go_to_course')); ?></a>
                                <?php else: ?>
                                    <button class="btn btn-orange w-100 fw-bold mb-3 enroll-btn"
                                        data-course-id="<?php echo e($course->id); ?>">
                                        <i class="fas fa-graduation-cap me-2"></i><?php echo e(custom_trans('enroll_now')); ?>

                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" class="btn btn-orange w-100 fw-bold mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i><?php echo e(custom_trans('enroll_now')); ?>

                                </a>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between">
                                <button class="icon-btn"><i class="fa fa-calendar-alt"></i></button>
                                <button class="icon-btn"><i class="fa fa-share-alt"></i></button>
                                <?php if(auth()->guard()->check()): ?>
                                    <!-- Wishlist Button -->
                                    <button class="icon-btn wishlist-btn" data-course-id="<?php echo e($course->id); ?>"
                                        data-in-wishlist="<?php echo e(auth()->user()->hasInWishlist($course) ? 'true' : 'false'); ?>">
                                        <i
                                            class="fa fa-heart <?php echo e(auth()->user()->hasInWishlist($course) ? 'text-danger' : ''); ?>"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="icon-btn" onclick="window.location.href='<?php echo e(route('login')); ?>'">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="icon-btn"><i class="fa fa-flag"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Interactive Star Rating System (copied from HTML)
        document.addEventListener('DOMContentLoaded', function() {
            const starRatings = document.querySelectorAll('.star-rating');
            const ratings = {}; // Store ratings for each category

            starRatings.forEach(rating => {
                const category = rating.getAttribute('data-category');
                const stars = rating.querySelectorAll('.star-item');

                // Initialize rating for this category
                ratings[category] = 0;

                stars.forEach((star, index) => {
                    const starRating = index + 1;

                    // Mouse enter event
                    star.addEventListener('mouseenter', function() {
                        highlightStars(stars, starRating);
                        showRatingTooltip(rating, starRating);
                    });

                    // Mouse leave event
                    star.addEventListener('mouseleave', function() {
                        resetStars(stars);
                        updateSelectedStars(stars, ratings[category]);
                        hideRatingTooltip(rating);
                    });

                    // Click event
                    star.addEventListener('click', function() {
                        ratings[category] = starRating;

                        // Add click animation
                        star.classList.add('star-clicked');
                        setTimeout(() => {
                            star.classList.remove('star-clicked');
                        }, 400);

                        updateSelectedStars(stars, starRating);
                        showRatingFeedback(rating, category, starRating);

                        // Store rating in localStorage
                        localStorage.setItem(`rating_${category}`, starRating);
                    });
                });

                // Load saved rating from localStorage
                const savedRating = localStorage.getItem(`rating_${category}`);
                if (savedRating) {
                    ratings[category] = parseInt(savedRating);
                    updateSelectedStars(stars, ratings[category]);
                }
            });

            function highlightStars(stars, rating) {
                stars.forEach((star, index) => {
                    star.classList.remove('star-hover');
                    if (index < rating) {
                        star.classList.add('star-hover');
                    }
                });
            }

            function resetStars(stars) {
                stars.forEach(star => {
                    star.classList.remove('star-hover');
                });
            }

            function updateSelectedStars(stars, rating) {
                stars.forEach((star, index) => {
                    star.classList.remove('star-selected', 'star-filled');
                    if (index < rating) {
                        star.classList.add('star-selected');
                    }
                });
            }

            function showRatingTooltip(ratingContainer, rating) {
                // Remove existing tooltip
                const existingTooltip = ratingContainer.querySelector('.rating-tooltip');
                if (existingTooltip) {
                    existingTooltip.remove();
                }

                // Create new tooltip
                const tooltip = document.createElement('span');
                tooltip.className = 'rating-tooltip';
                tooltip.textContent = `${rating} star${rating > 1 ? 's' : ''}`;
                tooltip.style.cssText = `
                    position: absolute;
                    background: #333;
                    color: #fff;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 0.8rem;
                    top: -30px;
                    left: 50%;
                    transform: translateX(-50%);
                    white-space: nowrap;
                    z-index: 1000;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;

                ratingContainer.style.position = 'relative';
                ratingContainer.appendChild(tooltip);

                // Show tooltip with animation
                setTimeout(() => {
                    tooltip.style.opacity = '1';
                }, 10);
            }

            function hideRatingTooltip(ratingContainer) {
                const tooltip = ratingContainer.querySelector('.rating-tooltip');
                if (tooltip) {
                    tooltip.style.opacity = '0';
                    setTimeout(() => {
                        tooltip.remove();
                    }, 300);
                }
            }

            function showRatingFeedback(ratingContainer, category, rating) {
                // Remove existing feedback
                const existingFeedback = ratingContainer.parentNode.querySelector('.rating-display');
                if (existingFeedback) {
                    existingFeedback.remove();
                }

                // Create feedback display
                const feedback = document.createElement('span');
                feedback.className = 'rating-display show';
                feedback.textContent = `${rating}/5`;

                ratingContainer.parentNode.appendChild(feedback);

                // Show success message
                showToast(`${category.charAt(0).toUpperCase() + category.slice(1)} rated: ${rating} stars`,
                    'success');
            }

            function showToast(message, type = 'success') {
                // Remove existing toasts
                const existingToasts = document.querySelectorAll('.rating-toast');
                existingToasts.forEach(toast => toast.remove());

                const toast = document.createElement('div');
                toast.className =
                    `rating-toast alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
                toast.style.cssText = `
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 250px;
                    opacity: 0;
                    transform: translateX(100%);
                    transition: all 0.3s ease;
                `;
                toast.innerHTML = `
                    <i class="fa fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    ${message}
                `;

                document.body.appendChild(toast);

                // Show toast
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateX(0)';
                }, 10);

                // Hide toast after 3 seconds
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\courses-laravel\resources\views/courses/detail.blade.php ENDPATH**/ ?>