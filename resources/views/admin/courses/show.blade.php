@extends('admin.layout')

@section('title', 'Course Details - ' . $course->name)

@section('content')
    <div class="container-fluid py-4">
        <!-- Course Header -->
        <div class="course-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        @if ($course->image)
                            <img src="{{ $course->image_url }}" alt="Course" class="course-thumbnail me-4">
                        @else
                            <div
                                class="course-thumbnail me-4 bg-white bg-opacity-20 d-flex align-items-center justify-content-center">
                                <i class="fa fa-book fa-3x text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h1 class="h2 mb-2">{{ $course->name }}</h1>
                            <p class="mb-2 opacity-75">{{ Str::limit($course->description, 150) }}</p>
                            <div class="d-flex align-items-center">
                                <span
                                    class="badge bg-white text-dark me-2">{{ $course->category->name ?? 'Uncategorized' }}</span>
                                <span class="badge bg-white bg-opacity-20 me-2">{{ ucfirst($course->status) }}</span>
                                @if ($course->is_featured)
                                    <span class="badge bg-warning text-dark me-2">Featured</span>
                                @endif
                                @if ($course->price == 0)
                                    <span class="badge bg-success">Free</span>
                                @else
                                    <span class="badge bg-info">{{ number_format($course->price, 2) }} SAR</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-light">
                            <i class="fa fa-edit me-1"></i>Edit Course
                        </a>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-light">
                            <i class="fa fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <i class="fa fa-list fa-2x text-primary mb-2"></i>
                    <h3 class="h4 mb-0">{{ $stats['total_sections'] }}</h3>
                    <small class="text-muted">Sections</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <i class="fa fa-play-circle fa-2x text-success mb-2"></i>
                    <h3 class="h4 mb-0">{{ $stats['total_lectures'] }}</h3>
                    <small class="text-muted">Lectures</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <i class="fa fa-question-circle fa-2x text-warning mb-2"></i>
                    <h3 class="h4 mb-0">{{ $stats['total_quizzes'] }}</h3>
                    <small class="text-muted">Quizzes</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <i class="fa fa-tasks fa-2x text-info mb-2"></i>
                    <h3 class="h4 mb-0">{{ $stats['total_homework'] }}</h3>
                    <small class="text-muted">Homework</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <i class="fa fa-video fa-2x text-danger mb-2"></i>
                    <h3 class="h4 mb-0">{{ $stats['total_live_classes'] }}</h3>
                    <small class="text-muted">Live Classes</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <i class="fa fa-users fa-2x text-secondary mb-2"></i>
                    <h3 class="h4 mb-0">{{ $stats['total_enrollments'] ?? 0 }}</h3>
                    <small class="text-muted">Students</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Course Content -->
            <div class="col-lg-8">
                <!-- Course Sections -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-list me-2"></i>Course Content</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                            <i class="fa fa-plus me-1"></i>Add Section
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @forelse($course->sections as $section)
                            <div class="section-card">
                                <div class="section-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $section->title }}</h6>
                                            <small class="text-muted">{{ $section->description }}</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-secondary">{{ $section->lectures->count() }}
                                                lectures</span>
                                            <div class="btn-group btn-group-sm ms-2">
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                                    data-bs-target="#addLectureModal"
                                                    data-section-id="{{ $section->id }}">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content">
                                    @forelse($section->lectures as $lecture)
                                        <div class="lecture-item">
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <i class="fa fa-play-circle me-3 text-primary"></i>
                                                <div>
                                                    <div class="fw-medium">{{ $lecture->title }}</div>
                                                    <div class="d-flex align-items-center mt-1">
                                                        <span class="badge bg-light text-dark content-type-badge me-2">
                                                            {{ ucfirst($lecture->content_type) }}
                                                        </span>
                                                        @if ($lecture->duration_minutes)
                                                            <small class="text-muted">{{ $lecture->duration_minutes }}
                                                                min</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="lecture-item text-center text-muted">
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
                    <div class="card mb-4">
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
            <div class="col-lg-4">
                <!-- Course Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Course Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label text-muted small">Instructor(s)</label>
                                <div class="fw-medium">
                                    @if ($course->instructors->count() > 0)
                                        @foreach ($course->instructors as $instructor)
                                            <div class="d-flex align-items-center mb-1">
                                                @if ($instructor->avatar)
                                                    <img src="{{ asset('storage/' . $instructor->avatar) }}"
                                                        class="rounded-circle me-2" width="20" height="20">
                                                @else
                                                    <div
                                                        class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 w-20 h-20 fs-9px">
                                                        {{ strtoupper(substr($instructor->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <span>{{ $instructor->name }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Duration</label>
                                <div class="fw-medium">{{ $course->duration ?? 'Not set' }}</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Price</label>
                                <div class="fw-medium">
                                    @if ($course->price == 0)
                                        <span class="text-success">Free</span>
                                    @else
                                        {{ number_format($course->price, 2) }} SAR
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Status</label>
                                <div>
                                    @php
                                        $statusClasses = [
                                            'published' => 'success',
                                            'draft' => 'warning',
                                            'archived' => 'secondary',
                                        ];
                                        $statusClass = $statusClasses[$course->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($course->status) }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Created</label>
                                <div class="fw-medium">{{ $course->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Updated</label>
                                <div class="fw-medium">{{ $course->updated_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-primary">
                                <i class="fa fa-edit me-2"></i>Edit Course
                            </a>
                            <button class="btn btn-outline-success" data-bs-toggle="modal"
                                data-bs-target="#enrollStudentsModal">
                                <i class="fa fa-user-plus me-2"></i>Enroll Students
                            </button>
                            <a href="{{ route('admin.courses.analytics', $course) }}" class="btn btn-outline-info">
                                <i class="fa fa-chart-bar me-2"></i>View Analytics
                            </a>
                            <button class="btn btn-outline-warning">
                                <i class="fa fa-copy me-2"></i>Duplicate Course
                            </button>
                            <hr>
                            <button class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="fa fa-trash me-2"></i>Delete Course
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-clock me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($recentEnrollments as $enrollment)
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-user-plus text-success me-2"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">{{ $enrollment->user->name ?? 'Unknown User' }}
                                                enrolled</div>
                                            <small
                                                class="text-muted">{{ $enrollment->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if ($enrollment->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($enrollment->status == 'active')
                                            <span class="badge bg-primary">Active</span>
                                        @endif
                                    </div>
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

