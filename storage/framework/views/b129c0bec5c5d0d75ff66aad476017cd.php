<?php
    $infoSplit = \App\Models\InfoSplit::active()->first();
?>

<?php if($infoSplit): ?>
    <!-- Info Split Section -->
    <section class="info-split-section bg-light-eaf">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-6 mb-4 px-4 mb-lg-0">
                    <?php if($infoSplit->image): ?>
                        <img src="<?php echo e($infoSplit->image_url); ?>" alt="Info Split"
                            class="img-fluid rounded-4 shadow-sm w-100">
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3"><?php echo nl2br(e($infoSplit->getDisplayTitle())); ?></h2>
                    <p class="mb-4 text-blue-247 fs-11">
                        <?php echo e($infoSplit->getDisplayDescription()); ?>

                    </p>
                    <?php if($infoSplit->button_url && $infoSplit->button_text): ?>
                        <a href="<?php echo e($infoSplit->button_url); ?>" class="btn btn-primary px-4 py-3 rounded-3">
                            <?php echo e($infoSplit->getDisplayButtonText()); ?> &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/courses/info-split.blade.php ENDPATH**/ ?>