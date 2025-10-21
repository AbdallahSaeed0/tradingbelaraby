@extends('layouts.app')

@section('title', 'Order Successful - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Success Section -->
    <section class="success-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <i class="fas fa-check-circle success-icon"></i>
                    <h1 class="fw-bold mb-3">{{ custom_trans('order_successful') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('thank_you_for_your_purchase') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Details -->
    <section class="order-details-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="order-details">
                        <h4 class="fw-bold mb-4">{{ custom_trans('order_details') }}</h4>

                        <!-- Order Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>{{ custom_trans('order_number') }}:</strong> {{ $order->order_number }}</p>
                                <p><strong>{{ custom_trans('order_date') }}:</strong>
                                    {{ $order->created_at->format('M d, Y') }}</p>
                                <p><strong>{{ custom_trans('payment_method') }}:</strong>
                                    @if ($order->payment_method === 'free')
                                        <span class="badge bg-success">{{ custom_trans('free_enrollment') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ custom_trans('credit_card') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ custom_trans('total_amount') }}:</strong>
                                    ₹{{ number_format($order->total, 2) }}</p>
                                <p><strong>{{ custom_trans('status') }}:</strong>
                                    <span class="badge bg-success">{{ custom_trans('completed') }}</span>
                                </p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Enrolled Courses -->
                        <h5 class="fw-bold mb-3">{{ custom_trans('enrolled_courses') }}</h5>
                        @foreach ($order->orderItems as $item)
                            <div class="course-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="{{ $item->course->image_url }}" alt="{{ $item->course->name }}"
                                            class="img-fluid rounded img-h-60">
                                    </div>
                                    <div class="col-md-7">
                                        <h6 class="fw-bold mb-1">{{ $item->course->name }}</h6>
                                        <p class="text-muted mb-0">
                                            {{ $item->course->instructor->name ?? 'Unknown Instructor' }}</p>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        @if ($item->price > 0)
                                            <span class="fw-bold text-primary">₹{{ number_format($item->price, 2) }}</span>
                                        @else
                                            <span class="badge bg-success">{{ custom_trans('free') }}</span>
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
                                    {{ custom_trans('go_to_dashboard') }}
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search me-2"></i>
                                    {{ custom_trans('browse_more_courses') }}
                                </a>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2">{{ custom_trans('what_happens_next') }}</h6>
                            <ul class="mb-0">
                                <li>{{ custom_trans('access_courses_immediately') }}</li>
                                <li>{{ custom_trans('start_learning_right_away') }}</li>
                                <li>{{ custom_trans('track_progress_dashboard') }}</li>
                                <li>{{ custom_trans('receive_certificate_completion') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

