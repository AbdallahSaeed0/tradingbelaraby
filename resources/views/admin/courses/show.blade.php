@extends('admin.layout')

@section('title', 'Course Details - ' . $course->name)

@section('content')
    @php
        $courseStatusBadgeClass = match ($course->status) {
            'published' => 'success',
            'draft' => 'warning text-dark',
            'archived' => 'secondary',
            default => 'secondary',
        };
    @endphp
    <div class="container-fluid py-4 admin-detail-page admin-course-detail-page"
        data-mobile-back-url="{{ route('admin.courses.index') }}"
        data-mobile-back-label="Courses">
        @include('admin.partials.detail-page-header', [
            'title' => $course->name,
            'subtitle' => ($course->category->name ?? 'Uncategorized') . ' · ' . ucfirst($course->status),
            'backUrl' => route('admin.courses.index'),
            'backLabel' => 'Courses',
            'primaryUrl' => route('admin.courses.edit', $course),
            'primaryLabel' => 'Edit Course',
        ])

        <nav class="admin-detail-section-nav d-lg-none" aria-label="Course sections">
            <a href="#detail-section-content" class="admin-detail-section-nav__link">Content</a>
            <a href="#detail-section-info" class="admin-detail-section-nav__link">Info</a>
            <a href="#detail-section-actions" class="admin-detail-section-nav__link">Actions</a>
        </nav>

        <div class="admin-course-detail-mobile-actions d-lg-none">
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-edit me-1"></i>Edit Course
            </a>
            <a href="{{ route('admin.courses.analytics', $course) }}" class="btn btn-outline-info btn-sm">
                <i class="fa fa-chart-bar me-1"></i>Analytics
            </a>
            <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-outline-success btn-sm">
                <i class="fa fa-user-plus me-1"></i>Enrollments
            </a>
        </div>

        {{-- Mobile hero --}}
        <div class="admin-course-detail-hero d-lg-none mb-3">
            @if ($course->image)
                <img src="{{ $course->image_url }}" alt="{{ $course->name }}" class="admin-course-detail-hero__img">
            @else
                <div class="admin-course-detail-hero__placeholder">
                    <i class="fa fa-book fa-2x"></i>
                </div>
            @endif
            <div class="admin-course-detail-hero__chips admin-course-hero__chips">
                <span class="admin-course-chip admin-course-chip--category">{{ $course->category->name ?? 'Uncategorized' }}</span>
                <span class="admin-course-chip admin-course-chip--{{ $course->status }}">{{ ucfirst($course->status) }}</span>
                @if ($course->is_featured)
                    <span class="admin-course-chip admin-course-chip--featured">Featured</span>
                @endif
                @if ($course->price == 0)
                    <span class="admin-course-chip admin-course-chip--free">Free</span>
                @else
                    <span class="admin-course-chip admin-course-chip--price">{{ number_format($course->price, 2) }} SAR</span>
                @endif
            </div>
            @if ($course->description)
                <p class="admin-course-detail-hero__desc">{{ Str::limit(strip_tags($course->description), 160) }}</p>
            @endif
        </div>

        {{-- Desktop hero --}}
        <div class="admin-course-hero d-none d-lg-block mb-4">
            <div class="admin-course-hero__inner">
                <div class="admin-course-hero__media">
                    @if ($course->image)
                        <img src="{{ $course->image_url }}" alt="{{ $course->name }}" class="admin-course-hero__thumb">
                    @else
                        <div class="admin-course-hero__thumb admin-course-hero__thumb--placeholder">
                            <i class="fa fa-book fa-2x"></i>
                        </div>
                    @endif
                </div>
                <div class="admin-course-hero__body">
                    <h1 class="admin-course-hero__title">{{ $course->name }}</h1>
                    @if ($course->description)
                        <p class="admin-course-hero__desc">{{ Str::limit(strip_tags($course->description), 160) }}</p>
                    @endif
                    <div class="admin-course-hero__chips">
                        <span class="admin-course-chip admin-course-chip--category">{{ $course->category->name ?? 'Uncategorized' }}</span>
                        <span class="admin-course-chip admin-course-chip--{{ $course->status }}">{{ ucfirst($course->status) }}</span>
                        @if ($course->is_featured)
                            <span class="admin-course-chip admin-course-chip--featured">Featured</span>
                        @endif
                        @if ($course->price == 0)
                            <span class="admin-course-chip admin-course-chip--free">Free</span>
                        @else
                            <span class="admin-course-chip admin-course-chip--price">{{ number_format($course->price, 2) }} SAR</span>
                        @endif
                    </div>
                </div>
                <div class="admin-course-hero__actions admin-form-inline-actions">
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-light btn-sm">
                        <i class="fa fa-edit me-1"></i>Edit Course
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-light btn-sm admin-detail-back-url">
                        <i class="fa fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 admin-course-stats">
            <div class="col-4 col-md-2">
                <div class="admin-course-stat">
                    <div class="admin-course-stat__icon is-primary"><i class="fa fa-list"></i></div>
                    <div class="admin-course-stat__value">{{ $stats['total_sections'] }}</div>
                    <div class="admin-course-stat__label">Sections</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="admin-course-stat">
                    <div class="admin-course-stat__icon is-success"><i class="fa fa-play-circle"></i></div>
                    <div class="admin-course-stat__value">{{ $stats['total_lectures'] }}</div>
                    <div class="admin-course-stat__label">Lectures</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="admin-course-stat">
                    <div class="admin-course-stat__icon is-warning"><i class="fa fa-question-circle"></i></div>
                    <div class="admin-course-stat__value">{{ $stats['total_quizzes'] }}</div>
                    <div class="admin-course-stat__label">Quizzes</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="admin-course-stat">
                    <div class="admin-course-stat__icon is-info"><i class="fa fa-tasks"></i></div>
                    <div class="admin-course-stat__value">{{ $stats['total_homework'] }}</div>
                    <div class="admin-course-stat__label">Homework</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="admin-course-stat">
                    <div class="admin-course-stat__icon is-danger"><i class="fa fa-video"></i></div>
                    <div class="admin-course-stat__value">{{ $stats['total_live_classes'] }}</div>
                    <div class="admin-course-stat__label">Live Classes</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="admin-course-stat">
                    <div class="admin-course-stat__icon is-secondary"><i class="fa fa-users"></i></div>
                    <div class="admin-course-stat__value">{{ $stats['total_enrollments'] ?? 0 }}</div>
                    <div class="admin-course-stat__label">Students</div>
                </div>
            </div>
        </div>

        <div class="row admin-detail-main-row">
            <!-- Course Content -->
            <div class="col-lg-8 order-lg-2">
                <!-- Course Sections -->
                <div class="card mb-4 admin-course-panel" id="detail-section-content">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-list me-2"></i>Course Content</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                            <i class="fa fa-plus me-1"></i>Add Section
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @forelse($course->sections as $section)
                            <div class="admin-course-section">
                                <div class="admin-course-section__head">
                                    <div class="min-w-0">
                                        <h6 class="admin-course-section__title">{{ $section->title }}</h6>
                                        @if ($section->description)
                                            <div class="admin-course-section__desc">{{ $section->description }}</div>
                                        @endif
                                    </div>
                                    <div class="admin-course-section__meta">
                                        <span class="admin-course-section__count">{{ $section->lectures->count() }} lectures</span>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#addLectureModal"
                                                data-section-id="{{ $section->id }}" title="Add lecture">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" title="Edit section">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Delete section">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-course-section__body">
                                    @forelse($section->lectures as $lecture)
                                        <div class="admin-course-lecture">
                                            <div class="admin-course-lecture__main">
                                                <span class="admin-course-lecture__play"><i class="fa fa-play"></i></span>
                                                <div class="min-w-0">
                                                    <div class="admin-course-lecture__title">{{ $lecture->title }}</div>
                                                    <div class="admin-course-lecture__meta">
                                                        <span class="admin-course-lecture__type">{{ ucfirst($lecture->content_type) }}</span>
                                                        @if ($lecture->duration_minutes)
                                                            <span class="admin-course-lecture__duration">{{ $lecture->duration_minutes }} min</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="admin-course-lecture__actions btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary btn-sm" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="admin-course-lecture admin-course-lecture--empty">
                                            <i class="fa fa-plus-circle me-2"></i>No lectures yet. Add your first lecture.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fa fa-book fa-3x mb-3"></i>
                                <h5>No sections created yet</h5>
                                <p>Start building your course by adding sections and lectures.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                    <i class="fa fa-plus me-2"></i>Add First Section
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- What You'll Learn -->
                @if ($course->what_to_learn && count($course->what_to_learn) > 0)
                    <div class="card mb-4 admin-course-panel">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-graduation-cap me-2"></i>What Students Will Learn</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($course->what_to_learn as $item)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-check-circle text-success me-2"></i>
                                            <span>{{ $item }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 order-lg-1 admin-detail-sidebar">
                <!-- Course Info -->
                <div class="card mb-4 admin-course-panel" id="detail-section-info">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Course Information</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="admin-course-info-grid">
                            <div class="admin-course-info-item admin-course-info-item--full">
                                <span class="admin-course-info-item__label">Instructor(s)</span>
                                <span class="admin-course-info-item__value">
                                    @if ($course->instructors->count() > 0)
                                        @foreach ($course->instructors as $instructor)
                                            <span class="d-flex align-items-center mb-1">
                                                @if ($instructor->avatar)
                                                    <img src="{{ asset('storage/' . $instructor->avatar) }}" class="rounded-circle me-2" width="22" height="22" alt="">
                                                @else
                                                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:22px;height:22px;font-size:0.65rem">{{ strtoupper(substr($instructor->name, 0, 2)) }}</span>
                                                @endif
                                                {{ $instructor->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </span>
                            </div>
                            <div class="admin-course-info-item">
                                <span class="admin-course-info-item__label">Duration</span>
                                <span class="admin-course-info-item__value">{{ $course->duration ?? 'Not set' }}</span>
                            </div>
                            <div class="admin-course-info-item">
                                <span class="admin-course-info-item__label">Price</span>
                                <span class="admin-course-info-item__value">
                                    @if ($course->price == 0)
                                        <span class="text-success fw-semibold">Free</span>
                                    @else
                                        {{ number_format($course->price, 2) }} SAR
                                    @endif
                                </span>
                            </div>
                            <div class="admin-course-info-item">
                                <span class="admin-course-info-item__label">Status</span>
                                <span class="admin-course-info-item__value">
                                    <span class="badge bg-{{ $courseStatusBadgeClass }}">{{ ucfirst($course->status) }}</span>
                                </span>
                            </div>
                            <div class="admin-course-info-item">
                                <span class="admin-course-info-item__label">Students</span>
                                <span class="admin-course-info-item__value">{{ $stats['total_enrollments'] ?? 0 }}</span>
                            </div>
                            <div class="admin-course-info-item">
                                <span class="admin-course-info-item__label">Created</span>
                                <span class="admin-course-info-item__value">{{ $course->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="admin-course-info-item">
                                <span class="admin-course-info-item__label">Updated</span>
                                <span class="admin-course-info-item__value">{{ $course->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4 admin-course-panel admin-form-inline-actions" id="detail-section-actions">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="admin-course-actions">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-primary">
                                <i class="fa fa-edit me-2"></i>Edit Course
                            </a>
                            <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-outline-success">
                                <i class="fa fa-user-plus me-2"></i>View Enrollments
                            </a>
                            <a href="{{ route('admin.courses.analytics', $course) }}" class="btn btn-outline-info">
                                <i class="fa fa-chart-bar me-2"></i>View Analytics
                            </a>
                            <a href="{{ route('admin.courses.duplicate', $course) }}" class="btn btn-outline-warning">
                                <i class="fa fa-copy me-2"></i>Duplicate Course
                            </a>
                            <hr class="admin-course-actions__divider">
                            <button class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="fa fa-trash me-2"></i>Delete Course
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card admin-course-panel">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-clock me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        @forelse($recentEnrollments as $enrollment)
                            <div class="admin-course-activity-item">
                                <span class="admin-course-activity-item__icon"><i class="fa fa-user-plus"></i></span>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="admin-course-activity-item__text">{{ $enrollment->user->name ?? 'Unknown User' }} enrolled</div>
                                    <div class="admin-course-activity-item__time">{{ $enrollment->created_at->diffForHumans() }}</div>
                                </div>
                                @if ($enrollment->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($enrollment->status == 'active')
                                    <span class="badge bg-primary">Active</span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fa fa-clock fa-2x mb-2"></i>
                                <p class="mb-0">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Section Modal -->
    <div class="modal fade" id="addSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.courses.sections.store', $course) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="sectionTitle" class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="sectionTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="sectionDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="sectionDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="sectionOrder" class="form-label">Order</label>
                            <input type="number" class="form-control" id="sectionOrder" name="order"
                                value="{{ $course->sections->count() + 1 }}" min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Lecture Modal -->
    <div class="modal fade" id="addLectureModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Lecture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addLectureForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="lectureTitle" class="form-label">Lecture Title</label>
                            <input type="text" class="form-control" id="lectureTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="lectureDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="lectureDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lectureType" class="form-label">Content Type</label>
                                <select class="form-select" id="lectureType" name="content_type">
                                    <option value="video">Video</option>
                                    <option value="document">Document</option>
                                    <option value="text">Text Content</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lectureDuration" class="form-label">Duration (minutes)</label>
                                <input type="number" class="form-control" id="lectureDuration" name="duration_minutes"
                                    min="1">
                            </div>
                        </div>
                        <div class="mb-3" id="videoUrlInput">
                            <label for="videoUrl" class="form-label">Video URL</label>
                            <input type="url" class="form-control" id="videoUrl" name="video_url"
                                placeholder="YouTube, Vimeo, etc.">
                        </div>
                        <div class="mb-3 d-none-initially" id="fileUploadInput">
                            <label for="lectureFile" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="lectureFile" name="file"
                                accept="video/*,audio/*,.pdf,.doc,.docx,.ppt,.pptx">
                        </div>
                        <div class="mb-3 d-none-initially" id="textContentInput">
                            <label for="textContent" class="form-label">Text Content</label>
                            <textarea class="form-control" id="textContent" name="content_text" rows="6"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Lecture</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Handle lecture type switching
        document.getElementById('lectureType').addEventListener('change', function() {
            const videoUrlInput = document.getElementById('videoUrlInput');
            const fileUploadInput = document.getElementById('fileUploadInput');
            const textContentInput = document.getElementById('textContentInput');

            // Hide all inputs
            videoUrlInput.style.display = 'none';
            fileUploadInput.style.display = 'none';
            textContentInput.style.display = 'none';

            // Show relevant input
            switch (this.value) {
                case 'video':
                    videoUrlInput.style.display = 'block';
                    break;
                case 'document':
                    fileUploadInput.style.display = 'block';
                    break;
                case 'text':
                    textContentInput.style.display = 'block';
                    break;
            }
        });

        // Handle add lecture modal
        document.addEventListener('DOMContentLoaded', function() {
            const addLectureModal = document.getElementById('addLectureModal');
            const addLectureForm = document.getElementById('addLectureForm');

            addLectureModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const sectionId = button.getAttribute('data-section-id');
                addLectureForm.action = `/admin/courses/sections/${sectionId}/lectures`;
            });
        });

        // Confirm delete function
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.courses.destroy', $course) }}';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush

