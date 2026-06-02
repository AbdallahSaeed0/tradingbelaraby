@extends('layouts.app')

@section('title', custom_trans('Order Pending – Bank Transfer', 'front'))

@section('content')
    <!-- Hero Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <div class="container">
            <div class="row justify-content-center text-center text-white">
                <div class="col-lg-8">
                    <div class="mb-3" style="font-size: 4rem;">⏳</div>
                    <h1 class="fw-bold mb-3">{{ custom_trans('Order Received!', 'front') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('Your bank transfer order is pending confirmation', 'front') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section class="py-5" style="background: #fffbeb;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">

                    <!-- What happens next card -->
                    <div class="card border-warning shadow-sm mb-4">
                        <div class="card-header bg-warning bg-opacity-25 border-warning">
                            <h5 class="mb-0 fw-bold text-warning-emphasis">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ custom_trans('What happens next?', 'front') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                    style="width:40px;height:40px;">
                                    <span class="fw-bold text-warning">1</span>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-0">{{ custom_trans('Transfer Received', 'front') }}</p>
                                    <small class="text-muted">{{ custom_trans('Your transaction reference number has been saved with your order.', 'front') }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                    style="width:40px;height:40px;">
                                    <span class="fw-bold text-warning">2</span>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-0">{{ custom_trans('Admin Verification', 'front') }}</p>
                                    <small class="text-muted">{{ custom_trans('Our team will verify your bank transfer within 1 business day.', 'front') }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="rounded-circle bg-success bg-opacity-25 d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                    style="width:40px;height:40px;">
                                    <span class="fw-bold text-success">3</span>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-0">{{ custom_trans('Course Access Activated', 'front') }}</p>
                                    <small class="text-muted">{{ custom_trans('Once confirmed, you will get full access to your courses.', 'front') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fas fa-receipt me-2 text-primary"></i>{{ custom_trans('Order Details', 'front') }}</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ custom_trans('Order Number', 'front') }}</span>
                                <span class="fw-semibold">{{ $order->order_number }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ custom_trans('Total', 'front') }}</span>
                                <span class="fw-bold text-primary">SAR {{ number_format($order->total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ custom_trans('Payment Method', 'front') }}</span>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-university me-1"></i>{{ custom_trans('Bank Transfer', 'front') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ custom_trans('Status', 'front') }}</span>
                                <span class="badge bg-warning text-dark">{{ custom_trans('Pending Confirmation', 'front') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('student.my-courses') }}" class="btn btn-primary">
                            <i class="fas fa-graduation-cap me-2"></i>{{ custom_trans('My Courses', 'front') }}
                        </a>
                        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-search me-2"></i>{{ custom_trans('Browse More Courses', 'front') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
