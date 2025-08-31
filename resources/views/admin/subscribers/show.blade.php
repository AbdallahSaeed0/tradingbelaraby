@extends('admin.layout')

@section('title', 'Subscriber Details')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Subscriber Details</h1>
                <p class="text-muted">View subscriber information</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
                <form action="{{ route('admin.subscribers.destroy', $subscriber) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger"
                        onclick="return confirm('Are you sure you want to delete this subscriber?')">
                        <i class="fa fa-trash me-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-user me-2"></i>Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Full Name</label>
                                <div class="fw-bold">{{ $subscriber->name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Email Address</label>
                                <div>
                                    <a href="mailto:{{ $subscriber->email }}" class="text-decoration-none">
                                        {{ $subscriber->email }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Phone Number</label>
                                <div>
                                    <a href="tel:{{ $subscriber->phone }}" class="text-decoration-none">
                                        {{ $subscriber->phone }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">WhatsApp Number</label>
                                <div>
                                    @if($subscriber->whatsapp_number)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $subscriber->whatsapp_number) }}"
                                           target="_blank" class="text-decoration-none text-success">
                                            <i class="fab fa-whatsapp me-1"></i>
                                            {{ $subscriber->whatsapp_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Country</label>
                                <div>
                                    <span class="badge bg-info fs-6">{{ $subscriber->country }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Years of Experience</label>
                                <div>
                                    <span class="badge bg-secondary fs-6">{{ $subscriber->years_of_experience }} Years</span>
                                </div>
                            </div>
                        </div>

                        @if($subscriber->notes)
                            <div class="mt-4">
                                <label class="form-label text-muted">Notes</label>
                                <div class="p-3 bg-light rounded">
                                    {{ $subscriber->notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Registration Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-info-circle me-2"></i>Registration Info
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Language</label>
                            <div>
                                <span class="badge {{ $subscriber->language === 'ar' ? 'bg-warning' : 'bg-primary' }} fs-6">
                                    {{ $subscriber->language === 'ar' ? 'العربية (Arabic)' : 'English' }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Registration Date</label>
                            <div class="fw-bold">{{ $subscriber->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $subscriber->created_at->format('H:i A') }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Time Since Registration</label>
                            <div>{{ $subscriber->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="mailto:{{ $subscriber->email }}" class="btn btn-outline-primary">
                                <i class="fa fa-envelope me-2"></i>Send Email
                            </a>
                            <a href="tel:{{ $subscriber->phone }}" class="btn btn-outline-success">
                                <i class="fa fa-phone me-2"></i>Call Phone
                            </a>
                            @if($subscriber->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $subscriber->whatsapp_number) }}"
                                   target="_blank" class="btn btn-outline-success">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
