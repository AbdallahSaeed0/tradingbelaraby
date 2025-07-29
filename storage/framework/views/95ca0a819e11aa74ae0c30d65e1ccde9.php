<!-- Blog & News Section -->
<section class="blog-section py-5 bg-blog-news">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-warning fw-bold d-block mb-2 fs-11">
                <i class="fas fa-graduation-cap"></i> Our Blog
            </span>
            <h2 class="fw-bold mb-3">Latest Blog & News</h2>
        </div>
        <div class="row justify-content-center g-4">
            <?php
                $blogs = \App\Models\Blog::latest()->take(3)->get();
            ?>
            <?php if($blogs->count() > 0): ?>
                <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4 d-flex">
                        <div class="blog-card bg-white rounded-4 shadow-sm w-100 d-flex flex-column">
                            <div class="blog-img-wrap position-relative overflow-hidden rounded-top-4">
                                <img src="<?php echo e($blog->image_url ?? asset('images/placeholder.jpg')); ?>"
                                    class="blog-img w-100" alt="<?php echo e($blog->title); ?>">
                                <span class="badge blog-date-badge position-absolute top-0 start-0 m-3 px-3 py-2"><i
                                        class="fa fa-calendar me-2"></i><?php echo e($blog->created_at->format('M d, Y')); ?></span>
                            </div>
                            <div class="p-4 flex-grow-1 d-flex flex-column">
                                <div class="mb-2 text-muted small"><i class="fa fa-user me-1"></i> By
                                    <?php echo e($blog->author ?? 'Admin'); ?></div>
                                <h5 class="fw-bold mb-2"><?php echo e($blog->title); ?></h5>
                                <p class="mb-3 text-muted flex-grow-1">
                                    <?php echo e(Str::limit($blog->content ?? 'Not Found', 100)); ?></p>
                                <a href="<?php echo e(route('blog.show', $blog->slug)); ?>"
                                    class="text-primary fw-bold mt-auto">Read More &rarr;</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="text-muted py-4">
                        <i class="fas fa-newspaper fa-2x mb-2"></i>
                        <p class="mb-0">Not Found</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/courses/blog-news.blade.php ENDPATH**/ ?>