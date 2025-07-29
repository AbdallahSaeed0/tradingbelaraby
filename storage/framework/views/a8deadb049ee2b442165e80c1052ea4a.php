<?php $__env->startSection('title', $questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title"><?php echo e($questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply'); ?></h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.questions-answers.index')); ?>">Q&A Management</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="<?php echo e(route('admin.questions-answers.show', $questionsAnswer)); ?>">Question Details</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?php echo e($questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply'); ?></li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="<?php echo e(route('admin.questions-answers.show', $questionsAnswer)); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Question
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Question Preview -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Question Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="question-preview">
                            <h6 class="mb-3"><?php echo e($questionsAnswer->question_title); ?></h6>

                            <div class="question-meta mb-3">
                                <div class="meta-item">
                                    <strong>Asked by:</strong>
                                    <?php if($questionsAnswer->is_anonymous): ?>
                                        <span class="text-muted">Anonymous Student</span>
                                    <?php else: ?>
                                        <span><?php echo e($questionsAnswer->user->name ?? 'Unknown User'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="meta-item">
                                    <strong>Course:</strong>
                                    <span><?php echo e($questionsAnswer->course->name ?? 'N/A'); ?></span>
                                </div>
                                <div class="meta-item">
                                    <strong>Type:</strong>
                                    <span
                                        class="badge badge-light"><?php echo e(ucfirst(str_replace('_', ' ', $questionsAnswer->question_type))); ?></span>
                                </div>
                                <div class="meta-item">
                                    <strong>Priority:</strong>
                                    <?php switch($questionsAnswer->priority):
                                        case ('urgent'): ?>
                                            <span class="badge badge-danger">Urgent</span>
                                        <?php break; ?>

                                        <?php case ('high'): ?>
                                            <span class="badge badge-warning">High</span>
                                        <?php break; ?>

                                        <?php case ('normal'): ?>
                                            <span class="badge badge-info">Normal</span>
                                        <?php break; ?>

                                        <?php case ('low'): ?>
                                            <span class="badge badge-secondary">Low</span>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </div>
                                <div class="meta-item">
                                    <strong>Asked on:</strong>
                                    <span><?php echo e($questionsAnswer->formatted_question_date); ?></span>
                                </div>
                            </div>

                            <div class="question-content">
                                <strong>Question:</strong>
                                <div class="content-preview">
                                    <?php echo e(Str::limit(strip_tags($questionsAnswer->question_content), 200)); ?>

                                </div>
                            </div>

                            <?php if($questionsAnswer->lecture): ?>
                                <div class="lecture-info mt-3">
                                    <strong>Related Lecture:</strong>
                                    <div class="content-preview">
                                        <?php echo e($questionsAnswer->lecture->title); ?>

                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($questionsAnswer->section): ?>
                                <div class="section-info mt-2">
                                    <strong>Section:</strong>
                                    <div class="content-preview">
                                        <?php echo e($questionsAnswer->section->title); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Reply Guidelines -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Reply Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <div class="guidelines">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Be clear and concise in your response
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Provide specific examples when possible
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Use a professional and helpful tone
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Reference course materials if relevant
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Encourage further questions if needed
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><?php echo e($questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply'); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if($questionsAnswer->answer_content): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                You are editing an existing reply. The original reply will be replaced with your new
                                content.
                            </div>
                        <?php endif; ?>

                        <form
                            action="<?php echo e($questionsAnswer->answer_content ? route('admin.questions-answers.update_reply', $questionsAnswer) : route('admin.questions-answers.store_reply', $questionsAnswer)); ?>"
                            method="POST">
                            <?php echo csrf_field(); ?>
                            <?php if($questionsAnswer->answer_content): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="answer_content">Your Reply <span class="text-danger">*</span></label>
                                <textarea name="answer_content" id="answer_content" class="form-control" rows="12" required
                                    placeholder="Write your detailed reply here..."><?php echo e(old('answer_content', $questionsAnswer->answer_content)); ?></textarea>
                                <small class="form-text text-muted">
                                    Minimum 10 characters. You can use basic formatting like <strong>bold</strong>,
                                    <em>italic</em>, and line breaks.
                                </small>
                                <?php $__errorArgs = ['answer_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="form-group">
                                <label for="moderation_notes">Moderation Notes (Optional)</label>
                                <textarea name="moderation_notes" id="moderation_notes" class="form-control" rows="3"
                                    placeholder="Add any internal notes about this question or reply..."><?php echo e(old('moderation_notes', $questionsAnswer->moderation_notes)); ?></textarea>
                                <small class="form-text text-muted">
                                    These notes are only visible to administrators and will not be shown to students.
                                </small>
                                <?php $__errorArgs = ['moderation_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="mark_urgent" name="mark_urgent"
                                        value="1" <?php echo e($questionsAnswer->priority === 'urgent' ? 'checked' : ''); ?>>
                                    <label class="custom-control-label" for="mark_urgent">
                                        Mark as urgent priority
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    <?php echo e($questionsAnswer->answer_content ? 'Update Reply' : 'Submit Reply'); ?>

                                </button>
                                <a href="<?php echo e(route('admin.questions-answers.show', $questionsAnswer)); ?>"
                                    class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Reply Preview</h5>
                    </div>
                    <div class="card-body">
                        <div id="replyPreview" class="reply-preview">
                            <p class="text-muted">Start typing your reply above to see a preview here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const answerContent = document.getElementById('answer_content');
            const replyPreview = document.getElementById('replyPreview');

            function updatePreview() {
                const content = answerContent.value;
                if (content.trim() === '') {
                    replyPreview.innerHTML =
                        '<p class="text-muted">Start typing your reply above to see a preview here...</p>';
                } else {
                    // Simple formatting for preview
                    let formattedContent = content
                        .replace(/\n/g, '<br>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>');

                    replyPreview.innerHTML = formattedContent;
                }
            }

            answerContent.addEventListener('input', updatePreview);
            answerContent.addEventListener('keyup', updatePreview);

            // Initial preview
            updatePreview();

            // Character counter
            const charCounter = document.createElement('div');
            charCounter.className = 'text-muted mt-1';
            answerContent.parentNode.appendChild(charCounter);

            function updateCharCounter() {
                const count = answerContent.value.length;
                const min = 10;
                const color = count >= min ? 'text-success' : 'text-danger';
                charCounter.innerHTML = `<span class="${color}">${count} characters (minimum ${min})</span>`;
            }

            answerContent.addEventListener('input', updateCharCounter);
            updateCharCounter();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .question-preview {
            font-size: 14px;
        }

        .question-meta .meta-item {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .content-preview {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 3px;
            padding: 8px;
            margin-top: 5px;
            font-size: 13px;
            color: #6c757d;
        }

        .guidelines ul li {
            font-size: 13px;
            line-height: 1.4;
        }

        .guidelines ul li i {
            margin-right: 8px;
        }

        .reply-preview {
            min-height: 100px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            line-height: 1.6;
        }

        .form-actions {
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .form-actions .btn {
            margin-right: 10px;
        }

        #answer_content {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            resize: vertical;
        }

        .custom-control-label {
            font-size: 14px;
        }

        .alert {
            font-size: 14px;
        }

        .badge {
            font-size: 11px;
            padding: 4px 8px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\courses-laravel\resources\views/admin/questions-answers/reply.blade.php ENDPATH**/ ?>