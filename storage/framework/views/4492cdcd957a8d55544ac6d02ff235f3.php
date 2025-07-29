<?php $__env->startSection('title', 'Q&A Management'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .qa-stat-card {
            border: 1px solid #dee2e6;
            transition: box-shadow 0.2s, transform 0.2s;
            border-radius: 0.5rem;
        }

        .qa-stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .qa-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .qa-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .qa-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            margin-right: 0.25rem;
        }

        .qa-table th,
        .qa-table td {
            vertical-align: middle;
            font-size: 15px;
        }

        .qa-table th {
            background: #f8f9fa;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .qa-question-title {
            font-weight: 600;
            font-size: 1rem;
            color: #212529;
        }

        .qa-question-snippet {
            color: #6c757d;
            font-size: 0.95rem;
        }

        .qa-badge-priority-urgent {
            background: #dc3545;
            color: #fff;
        }

        .qa-badge-priority-high {
            background: #ffc107;
            color: #212529;
        }

        .qa-badge-priority-normal {
            background: #17a2b8;
            color: #fff;
        }

        .qa-badge-priority-low {
            background: #6c757d;
            color: #fff;
        }

        .qa-badge-status-pending {
            background: #ffc107;
            color: #212529;
        }

        .qa-badge-status-answered {
            background: #28a745;
            color: #fff;
        }

        .qa-badge-status-closed {
            background: #6c757d;
            color: #fff;
        }

        .qa-badge-status-flagged {
            background: #dc3545;
            color: #fff;
        }

        .qa-badge-type {
            background: #f1f3f4;
            color: #333;
        }

        .qa-table .dropdown-menu {
            min-width: 160px;
        }

        .qa-table .dropdown-item i {
            width: 18px;
        }

        .qa-table .dropdown-item {
            font-size: 14px;
        }

        .qa-table .badge {
            font-size: 12px;
        }

        .qa-table .user-info {
            font-size: 13px;
        }

        .qa-table .question-info {
            min-width: 220px;
        }

        .qa-table .qa-actions {
            min-width: 120px;
        }

        .qa-table .qa-badge {
            margin-right: 2px;
        }

        .qa-table .qa-badge:last-child {
            margin-right: 0;
        }

        .qa-table .qa-badge-type {
            margin-top: 2px;
        }

        .qa-table .qa-badge-status-pending {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.5);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Q&A Management</h1>
                    <p class="text-muted">Moderate, reply, and manage all course questions and answers</p>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.questions-answers.analytics')); ?>" class="btn btn-outline-primary me-2">
                        <i class="fas fa-chart-bar me-1"></i> Analytics
                    </a>
                    <a href="<?php echo e(route('admin.questions-answers.export')); ?>" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-primary text-white">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Questions</h6>
                        <h4 class="fw-bold mb-0"><?php echo e($stats['total']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-warning text-dark">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Pending</h6>
                        <h4 class="fw-bold mb-0"><?php echo e($stats['pending']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-success text-white">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Answered</h6>
                        <h4 class="fw-bold mb-0"><?php echo e($stats['answered']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-danger text-white">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Urgent</h6>
                        <h4 class="fw-bold mb-0"><?php echo e($stats['urgent']); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters/Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('admin.questions-answers.index')); ?>" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>"
                                    placeholder="Search questions, answers, users...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"
                                        <?php echo e(request('status') == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="course_id">
                                <option value="">All Courses</option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($course->id); ?>"
                                        <?php echo e(request('course_id') == $course->id ? 'selected' : ''); ?>><?php echo e($course->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="question_type">
                                <option value="">All Types</option>
                                <?php $__currentLoopData = $questionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"
                                        <?php echo e(request('question_type') == $value ? 'selected' : ''); ?>><?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="priority">
                                <option value="">All Priorities</option>
                                <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"
                                        <?php echo e(request('priority') == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Questions Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Questions & Answers</h5>
                <div class="d-flex align-items-center">
                    <label class="me-2 mb-0">Sort by:</label>
                    <select class="form-select form-select-sm w-auto" onchange="updateSort(this.value)">
                        <option value="latest" <?php echo e(request('sort') == 'latest' ? 'selected' : ''); ?>>Latest</option>
                        <option value="oldest" <?php echo e(request('sort') == 'oldest' ? 'selected' : ''); ?>>Oldest</option>
                        <option value="priority" <?php echo e(request('sort') == 'priority' ? 'selected' : ''); ?>>Priority</option>
                        <option value="views" <?php echo e(request('sort') == 'views' ? 'selected' : ''); ?>>Most Viewed</option>
                        <option value="votes" <?php echo e(request('sort') == 'votes' ? 'selected' : ''); ?>>Most Voted</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover qa-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:32px"><input type="checkbox" id="selectAll"
                                        onchange="toggleSelectAll()"></th>
                                <th>Question</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Type</th>
                                <th>Views</th>
                                <th>Votes</th>
                                <th>Created</th>
                                <th class="qa-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><input type="checkbox" class="question-checkbox" value="<?php echo e($question->id); ?>"
                                            onchange="updateSelection()"></td>
                                    <td class="question-info">
                                        <div class="qa-question-title mb-1">
                                            <a href="<?php echo e(route('admin.questions-answers.show', $question)); ?>"
                                                class="text-dark">
                                                <?php echo e(Str::limit($question->question_title, 50)); ?>

                                            </a>
                                        </div>
                                        <div class="qa-question-snippet">
                                            <?php echo e(Str::limit(strip_tags($question->question_content), 80)); ?>

                                        </div>
                                        <?php if($question->is_anonymous): ?>
                                            <span class="badge bg-secondary qa-badge">Anonymous</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($question->is_anonymous): ?>
                                            <span class="text-muted">Anonymous</span>
                                        <?php else: ?>
                                            <div class="user-info">
                                                <span class="fw-bold"><?php echo e($question->user->name ?? 'Unknown'); ?></span><br>
                                                <small class="text-muted"><?php echo e($question->user->email ?? ''); ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold"><?php echo e($question->course->name ?? 'N/A'); ?></span>
                                        <?php if($question->lecture): ?>
                                            <br><small class="text-muted"><?php echo e($question->lecture->title); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="qa-badge qa-badge-status-<?php echo e($question->status); ?>">
                                            <?php echo e(ucfirst($question->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="qa-badge qa-badge-priority-<?php echo e($question->priority); ?>">
                                            <?php echo e(ucfirst($question->priority)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="qa-badge qa-badge-type">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $question->question_type))); ?>

                                        </span>
                                    </td>
                                    <td><span class="text-muted"><?php echo e($question->views_count); ?></span></td>
                                    <td><span
                                            class="text-muted"><?php echo e($question->helpful_votes); ?>/<?php echo e($question->total_votes); ?></span>
                                    </td>
                                    <td>
                                        <?php if($question->created_at): ?>
                                            <small
                                                class="text-muted"><?php echo e($question->created_at->format('M d, Y')); ?></small><br>
                                            <small class="text-muted"><?php echo e($question->created_at->format('g:i A')); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">N/A</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="qa-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="<?php echo e(route('admin.questions-answers.show', $question)); ?>">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                                <?php if($question->status === 'pending'): ?>
                                                    <a class="dropdown-item"
                                                        href="<?php echo e(route('admin.questions-answers.reply', $question)); ?>">
                                                        <i class="fas fa-reply"></i> Reply
                                                    </a>
                                                <?php endif; ?>
                                                <?php if($question->status === 'answered'): ?>
                                                    <a class="dropdown-item"
                                                        href="<?php echo e(route('admin.questions-answers.reply', $question)); ?>">
                                                        <i class="fas fa-edit"></i> Edit Reply
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(!$question->is_public): ?>
                                                    <form
                                                        action="<?php echo e(route('admin.questions-answers.approve', $question)); ?>"
                                                        method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if($question->status !== 'closed'): ?>
                                                    <form action="<?php echo e(route('admin.questions-answers.close', $question)); ?>"
                                                        method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-lock"></i> Close
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form
                                                        action="<?php echo e(route('admin.questions-answers.reopen', $question)); ?>"
                                                        method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-unlock"></i> Reopen
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <div class="dropdown-divider"></div>
                                                <form action="<?php echo e(route('admin.questions-answers.reject', $question)); ?>"
                                                    method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-flag"></i> Flag/Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                            <h5>No questions found</h5>
                                            <p class="text-muted">No questions match your current filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <?php if($questions->hasPages()): ?>
                    <div class="d-flex justify-content-center p-3">
                        <?php echo e($questions->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change
            document.querySelector('select[name="status"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.querySelector('select[name="course_id"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.querySelector('select[name="question_type"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.querySelector('select[name="priority"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // Search with debounce
            let searchTimeout;
            document.querySelector('input[name="search"]').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        });

        function updateSort(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort', value);
            window.location = url;
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\courses-laravel\resources\views/admin/questions-answers/index.blade.php ENDPATH**/ ?>