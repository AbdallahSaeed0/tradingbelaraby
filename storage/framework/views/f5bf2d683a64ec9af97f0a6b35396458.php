<?php
    // Fetch active instructors from the database
    $instructors = \App\Models\Admin::whereHas('adminType', function ($query) {
        $query->where('name', 'instructor');
    })
        ->where('is_active', true)
        ->get();
?>

<!-- Instructor Section -->
<section class="instructor-section position-relative py-5 bg-light">
    <!-- Background image on the left behind the cards -->
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-03.png" alt="an-img-01"
        class="courses-bg-img d-none d-md-block">
    <div class="container position-relative" style="z-index:2;">
        <!-- Slider controls -->
        <div class="d-flex justify-content-between mb-3">
            <div class="text-start mb-4">
                <span class="text-warning fw-bold mb-2 d-block" style="font-size:1.1rem;">
                    <i class="fas fa-graduation-cap"></i> <?php echo e(__('Instructor')); ?>

                </span>
                <h2 class="fw-bold mb-3" style="font-size:2.5rem;"> <?php echo e(__('Instructor')); ?></h2>
            </div>
            <div class="buts d-flex align-items-center">
                <button class="btn btn-danger me-2 px-4 py-2 rounded-3 swiper-button-prev"></button>
                <button class="btn btn-danger px-4 py-2 rounded-3 swiper-button-next"></button>
            </div>
        </div>

        <?php if($instructors->count() > 0): ?>
            <!-- Swiper -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="swiper-slide">
                            <div class="course-card-custom">
                                <div class="course-img-wrap">
                                    <img src="<?php echo e($instructor->avatar_url); ?>" class="course-img"
                                        alt="<?php echo e($instructor->name); ?>">
                                    <span class="badge badge-green"><?php echo e(__('Instructor')); ?></span>
                                    <span class="price-badge">
                                        <span class="discounted"><?php echo e($instructor->courses->count()); ?></span>
                                        <span class="original"><?php echo e(__('Courses')); ?></span>
                                    </span>
                                    <img src="<?php echo e($instructor->avatar_url); ?>" class="author-avatar"
                                        alt="<?php echo e($instructor->name); ?>">
                                    <div class="course-hover-icons">
                                        <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                        <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                    </div>
                                </div>
                                <div class="course-card-body">
                                    <h5 class="course-title"><?php echo e($instructor->name); ?></h5>
                                    <p class="course-desc"><?php echo e($instructor->email); ?></p>
                                    <?php if($instructor->phone): ?>
                                        <p class="course-desc"><?php echo e($instructor->phone); ?></p>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('instructor.show', $instructor->id)); ?>"
                                        class="read-more"><?php echo e(__('View Profile')); ?> &rarr;</a>
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                        class="book-icon" alt="book">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Fallback content when no instructors are available -->
            <div class="text-center py-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/course/man-filming-with-professional-camera.jpg"
                                    class="course-img" alt="Photography">
                                <span class="badge badge-green"><?php echo e(__('Bestseller')); ?></span>
                                <span class="price-badge">
                                    <span class="discounted">345.99₹</span>
                                    <span class="original">1037.99₹</span>
                                </span>
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="author-avatar"
                                    alt="author">
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title"><?php echo e(__('Photography')); ?></h5>
                                <p class="course-desc">
                                    <?php echo e(__('This is an all-encompassing guide for making an independent feature le...')); ?>

                                </p>
                                <a href="#" class="read-more"><?php echo e(__('Read More')); ?> &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/course/beautiful-indian-young-hindu-woman-model-traditional-indian-costume-yellow-saree%20(1).jpg"
                                    class="course-img" alt="Designing">
                                <span class="badge badge-yellow"><?php echo e(__('Trending')); ?></span>
                                <span class="price-badge">
                                    <span class="discounted">1556.99₹</span>
                                    <span class="original">3114.00₹</span>
                                </span>
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" class="author-avatar"
                                    alt="author">
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title"><?php echo e(__('Designing')); ?></h5>
                                <p class="course-desc">
                                    <?php echo e(__('Details of a fashion design course may include: Fundamentals of fashion...')); ?>

                                </p>
                                <a href="#" class="read-more"><?php echo e(__('Read More')); ?> &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/course/couress-img-3.jpg"
                                    class="course-img" alt="IT & Software">
                                <span class="badge badge-green"><?php echo e(__('Bestseller')); ?></span>
                                <span class="price-badge">
                                    <span class="discounted">1037.99₹</span>
                                    <span class="original">1730.00₹</span>
                                </span>
                                <img src="https://randomuser.me/api/portraits/men/45.jpg" class="author-avatar"
                                    alt="author">
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title"><?php echo e(__('IT & Software')); ?></h5>
                                <p class="course-desc">
                                    <?php echo e(__('Artificial Intelligence is finally here and most of us are already act...')); ?>

                                </p>
                                <a href="#" class="read-more"><?php echo e(__('Read More')); ?> &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/courses/instructor-section.blade.php ENDPATH**/ ?>