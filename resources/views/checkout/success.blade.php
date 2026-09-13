@extends('layouts.app')

@section('title', 'Order Successful - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Success Section -->
    <section class="success-section py-5 bg-gradient-primary text-white text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="success-icon-wrap mx-auto mb-4">
                        <i class="fas fa-check success-check"></i>
                    </div>
                    <h1 class="fw-bold mb-3">{{ custom_trans('order_successful', 'front') }}</h1>
                    <p class="lead mb-0 opacity-75">{{ custom_trans('thank_you_for_your_purchase', 'front') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Details -->
    <section class="order-details-section py-5" style="margin-top:-2.5rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="order-details bg-white rounded-3 shadow-sm p-4 p-md-5 border border-light">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
                            <h4 class="fw-bold mb-0">{{ custom_trans('order_details', 'front') }}</h4>
                            <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 fs-6">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ custom_trans('completed', 'front') }}
                            </span>
                        </div>

                        <!-- Order Information -->
                        <div class="row mb-2 gy-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">{{ custom_trans('order_number', 'front') }}</small>
                                <span class="fw-bold">{{ $order->order_number }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">{{ custom_trans('order_date', 'front') }}</small>
                                <span class="fw-bold">{{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">{{ custom_trans('payment_method', 'front') }}</small>
                                <span class="badge bg-primary-subtle text-primary-emphasis">
                                    {{ $order->payment_method === 'free' ? custom_trans('free_enrollment', 'front') : $order->payment_method_label }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ custom_trans('total_amount', 'front') }}</span>
                            <span class="fw-bold fs-4 text-primary">SAR {{ number_format($order->total, 2) }}</span>
                        </div>

                        <hr class="my-4">

                        <!-- Enrolled Courses -->
                        <h5 class="fw-bold mb-3">{{ custom_trans('enrolled_courses', 'front') }}</h5>
                        @foreach ($order->orderItems as $item)
                            <div class="course-item p-3 mb-2 bg-light rounded-3">
                                <div class="row align-items-center">
                                    <div class="col-3 col-md-2">
                                        @if ($item->course->image ?? null)
                                        <img src="{{ optimized_image_url($item->course->image, 120, 80) }}" alt="{{ $item->course->localized_name }}"
                                            class="img-fluid rounded img-h-60" width="120" height="80">
                                    @else
                                        <img src="{{ $item->course->image_url }}" alt="{{ $item->course->localized_name }}"
                                            class="img-fluid rounded img-h-60" width="120" height="80">
                                    @endif
                                    </div>
                                    <div class="col-6 col-md-7">
                                        <h6 class="fw-bold mb-1">{{ $item->course->localized_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            {{ $item->course->instructor->name ?? custom_trans('Unknown Instructor', 'front') }}</p>
                                    </div>
                                    <div class="col-3 text-end">
                                        @if ($item->price > 0)
                                            <span class="fw-bold text-primary">SAR {{ number_format($item->price, 2) }}</span>
                                        @else
                                            <span class="badge bg-success">{{ custom_trans('free', 'front') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <hr class="my-4">

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('student.dashboard') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    {{ custom_trans('go_to_dashboard', 'front') }}
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search me-2"></i>
                                    {{ custom_trans('browse_more_courses', 'front') }}
                                </a>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mt-4 p-3 bg-light rounded-3">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-lightbulb text-warning me-1"></i>
                                {{ custom_trans('what_happens_next', 'front') }}
                            </h6>
                            <ul class="mb-0 ps-4">
                                <li>{{ custom_trans('access_courses_immediately', 'front') }}</li>
                                <li>{{ custom_trans('start_learning_right_away', 'front') }}</li>
                                <li>{{ custom_trans('track_progress_dashboard', 'front') }}</li>
                                <li>{{ custom_trans('receive_certificate_completion', 'front') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .success-icon-wrap {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: success-pop 0.5s ease-out;
        }

        .success-check {
            font-size: 2.5rem;
            color: #fff;
        }

        @keyframes success-pop {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
    </style>
@endpush

