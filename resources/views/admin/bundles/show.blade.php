@extends('admin.layout')

@section('title', 'View Bundle')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">{{ $bundle->name }}</h1>
                        <p class="text-muted">Bundle Details</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Bundles
                        </a>
                        <a href="{{ route('admin.bundles.edit', $bundle) }}" class="btn btn-primary">
                            <i class="fa fa-edit me-2"></i>Edit Bundle
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Bundle Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Bundle Information</h5>
                    </div>
                    <div class="card-body">
                        @if($bundle->image)
                            <div class="mb-4">
                                <img src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}" class="img-fluid rounded">
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Name:</strong>
                                <p>{{ $bundle->name }}</p>
                            </div>
                            @if($bundle->name_ar)
                                <div class="col-md-6">
                                    <strong>Name (Arabic):</strong>
                                    <p dir="rtl">{{ $bundle->name_ar }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p>{{ $bundle->description }}</p>
                        </div>

                        @if($bundle->description_ar)
                            <div class="mb-3">
                                <strong>Description (Arabic):</strong>
                                <p dir="rtl">{{ $bundle->description_ar }}</p>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <p>
                                    @if($bundle->status === 'published')
                                        <span class="badge bg-success">Published</span>
                                    @elseif($bundle->status === 'draft')
                                        <span class="badge bg-warning">Draft</span>
                                    @else
                                        <span class="badge bg-secondary">Archived</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Featured:</strong>
                                <p>
                                    @if($bundle->is_featured)
                                        <span class="badge bg-primary">Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Created:</strong>
                                <p>{{ $bundle->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses in Bundle -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-book me-2"></i>Courses in Bundle ({{ $bundle->courses->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($bundle->courses->count() > 0)
                            <div class="list-group">
                                @foreach($bundle->courses as $course)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $course->image_url }}" alt="{{ $course->name }}" 
                                                class="me-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $course->name }}</h6>
                                                <p class="mb-1 text-muted">{{ Str::limit($course->description, 100) }}</p>
                                                <small class="text-muted">
                                                    <i class="fa fa-tag me-1"></i>{{ $course->formatted_price }}
                                                    <i class="fa fa-users ms-3 me-1"></i>{{ $course->enrolled_students }} students
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                                    View Course
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No courses in this bundle.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Pricing -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-dollar-sign me-2"></i>Pricing</h5>
                    </div>
                    <div class="card-body">
                        @if($bundle->original_price)
                            <div class="mb-3">
                                <strong>Original Price:</strong>
                                <p class="text-muted text-decoration-line-through">{{ $bundle->formatted_original_price }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Bundle Price:</strong>
                            <h4 class="text-primary">{{ $bundle->formatted_price }}</h4>
                        </div>

                        @if($bundle->discount_percentage)
                            <div class="mb-3">
                                <span class="badge bg-success">{{ $bundle->discount_percentage }}% OFF</span>
                            </div>
                        @endif

                        <hr>

                        <div class="mb-3">
                            <strong>Total Course Price:</strong>
                            <p>{{ number_format($bundle->total_course_price, 2) }} SAR</p>
                        </div>

                        <div class="mb-3">
                            <strong>Savings:</strong>
                            <p class="text-success">{{ number_format($bundle->savings_amount, 2) }} SAR</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-cog me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.bundles.edit', $bundle) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fa fa-edit me-2"></i>Edit Bundle
                        </a>
                        <form action="{{ route('admin.bundles.destroy', $bundle) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this bundle?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fa fa-trash me-2"></i>Delete Bundle
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

