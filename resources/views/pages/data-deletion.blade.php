@extends('layouts.app')

@php
    $currentLang = app()->getLocale();
    $pageTitle = $currentLang == 'ar' ? 'حذف البيانات' : 'Data Deletion';
@endphp
@section('title', $pageTitle . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Page Banner -->
    <section class="contact-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="contact-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="contact-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">
                {{ $pageTitle }}
            </h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="contact-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">
                        {{ custom_trans('home', 'front') }}
                    </a> &nbsp;|&nbsp;
                    {{ $pageTitle }}
                </span>
            </div>
        </div>
    </section>

    <!-- Data Deletion Content Section -->
    <section class="terms-content-section py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <div class="terms-content">
                                @if ($currentLang == 'ar')
                                    <div dir="rtl" class="text-end">
                                        <p class="mb-0">
                                            إذا كنت ترغب في حذف حسابك وبياناتك من منصة تداول بالعربي، يرجى إرسال بريد إلكتروني إلى
                                            <a href="mailto:support@tradingbelaraby.com">support@tradingbelaraby.com</a>
                                            بعنوان "طلب حذف البيانات"، وسيتم تنفيذ الطلب خلال 7 أيام عمل.
                                        </p>
                                    </div>
                                @else
                                    <div>
                                        <p class="mb-0">
                                            If you wish to delete your account and associated data from Trading Bel Araby, please send an email to
                                            <a href="mailto:support@tradingbelaraby.com">support@tradingbelaraby.com</a>
                                            with the subject "Data Deletion Request".
                                            We will process your request within 7 business days.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary px-5 py-2">
                            <i class="fas fa-arrow-left me-2"></i>
                            {{ custom_trans('back_to_home', 'front') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/academy-policy.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/academy-policy.css') }}">
    @endpush
@endif
