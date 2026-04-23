@extends('layouts.app')

@section('title', 'Post-registration demo')

@section('content')
    <section class="contact-banner position-relative d-flex align-items-center justify-content-center">
        <img src="{{ asset('images/breadcrumb-bg.png') }}" alt=""
            class="contact-banner-bg position-absolute w-100 h-100 top-0 start-0" width="1920" height="400">
        <div class="contact-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-5 fw-bold text-white mb-3">Post-registration experience (demo)</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="contact-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">{{ custom_trans('home', 'front') }}</a>
                    &nbsp;|&nbsp;
                    <span class="text-muted">Demo</span>
                </span>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <p class="lead text-muted mb-4">
                        This page uses the same site header and footer as the rest of the platform. After registration,
                        users can open <strong>Charts</strong> from the account menu (when signed in) or via the button below —
                        no course enrollment is required for this preview.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="{{ route('demo.charts') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-chart-line me-2"></i>Open charts preview
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">
                            {{ custom_trans('register', 'front') }}
                        </a>
                    </div>
                    <div class="alert alert-info border-0 shadow-sm mb-0" role="status">
                        <i class="fas fa-info-circle me-2"></i>
                        The charts screen is a <strong>static, non-interactive</strong> visual mock for presentations
                        (for example when requesting a TradingView data or widget API). It is not live market software.
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
