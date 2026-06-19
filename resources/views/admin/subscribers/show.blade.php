@extends('admin.layout')

@section('title', 'Subscriber Details')

@section('content')
    <div class="container-fluid py-4 admin-detail-page">
        @php
            $deleteAction = '<form action="' . route('admin.subscribers.destroy', $subscriber) . '" method="POST" class="d-inline">'
                . csrf_field() . method_field('DELETE')
                . '<button type="submit" class="btn btn-outline-danger" onclick="return confirm(\'Are you sure you want to delete this subscriber?\')">'
                . '<i class="fa fa-trash me-2"></i>Delete</button></form>';
        @endphp
        @include('admin.partials.detail-page-header', [
            'title' => $subscriber->name,
            'subtitle' => 'Subscriber · ' . $subscriber->email,
            'backUrl' => route('admin.subscribers.index'),
            'backLabel' => 'Subscribers',
            'primaryUrl' => null,
            'extraActions' => $deleteAction,
        ])

        <div class="row admin-detail-main-row">
            <div class="col-lg-4 order-lg-2 mb-4">
                <div class="card shadow-sm mb-4" id="detail-section-meta">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-info-circle me-2"></i>Registration Info</h5>
                    </div>
                    <div class="card-body admin-detail-grid">
                        <div class="admin-detail-field">
                            <strong>Language</strong>
                            <span class="admin-detail-value">
                                <span class="badge {{ $subscriber->language === 'ar' ? 'bg-warning text-dark' : 'bg-primary' }}">
                                    {{ $subscriber->language === 'ar' ? 'العربية (Arabic)' : 'English' }}
                                </span>
                            </span>
                        </div>
                        <div class="admin-detail-field">
                            <strong>Registration Date</strong>
                            <span class="admin-detail-value">
                                {{ $subscriber->created_at->format('M d, Y') }}
                                <small class="text-muted d-block">{{ $subscriber->created_at->format('H:i A') }}</small>
                            </span>
                        </div>
                        <div class="admin-detail-field">
                            <strong>Time Since Registration</strong>
                            <span class="admin-detail-value">{{ $subscriber->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm" id="detail-section-actions">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="mailto:{{ $subscriber->email }}" class="btn btn-outline-primary">
                                <i class="fa fa-envelope me-2"></i>Send Email
                            </a>
                            <a href="tel:{{ $subscriber->phone }}" class="btn btn-outline-success">
                                <i class="fa fa-phone me-2"></i>Call Phone
                            </a>
                            @if ($subscriber->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $subscriber->whatsapp_number) }}"
                                    target="_blank" class="btn btn-outline-success">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 order-lg-1">
                <div class="card shadow-sm" id="detail-section-info">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-user me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body admin-detail-grid">
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Full Name</strong>
                                <span class="admin-detail-value">{{ $subscriber->name }}</span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Email Address</strong>
                                <span class="admin-detail-value">
                                    <a href="mailto:{{ $subscriber->email }}" class="text-decoration-none">{{ $subscriber->email }}</a>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Phone Number</strong>
                                <span class="admin-detail-value">
                                    <a href="tel:{{ $subscriber->phone }}" class="text-decoration-none">{{ $subscriber->phone }}</a>
                                </span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>WhatsApp Number</strong>
                                <span class="admin-detail-value">
                                    @if ($subscriber->whatsapp_number)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $subscriber->whatsapp_number) }}"
                                            target="_blank" class="text-decoration-none text-success">
                                            <i class="fab fa-whatsapp me-1"></i>{{ $subscriber->whatsapp_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Country</strong>
                                <span class="admin-detail-value"><span class="badge bg-info">{{ $subscriber->country }}</span></span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Years of Experience</strong>
                                <span class="admin-detail-value"><span class="badge bg-secondary">{{ $subscriber->years_of_experience }} Years</span></span>
                            </div>
                        </div>
                        @if ($subscriber->notes)
                            <div class="admin-detail-field">
                                <strong>Notes</strong>
                                <span class="admin-detail-value">{{ $subscriber->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
