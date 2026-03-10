@extends('layouts.app')

@php
    $currentLang = app()->getLocale();
    $pageTitle = $currentLang == 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy';
@endphp
@section('title', $pageTitle . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Page Banner -->
    <section class="contact-banner position-relative d-flex align-items-center justify-content-center">
        <img src="{{ asset('images/breadcrumb-bg.png') }}" alt="Banner"
            class="contact-banner-bg position-absolute w-100 h-100 top-0 start-0" width="1920" height="400">
        <div class="contact-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">{{ $pageTitle }}</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="contact-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">{{ custom_trans('home', 'front') }}</a> &nbsp;|&nbsp;
                    {{ $pageTitle }}
                </span>
            </div>
        </div>
    </section>

    <!-- Privacy Policy Content (stub - add full content via Admin > Terms & Conditions with slug privacy-policy) -->
    <section class="terms-content-section py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            @if ($currentLang == 'ar')
                                <div dir="rtl" class="text-end">
                                    <h2 class="mb-4">سياسة الخصوصية</h2>
                                    <p>أكاديمية تداول بالعربي تحترم خصوصيتك. تستخدم هذه المنصة بياناتك وفق سياسة الخصوصية المعمول بها. للإدارة الكاملة لمحتوى هذه الصفحة، أضف صفحة بسلوك «privacy-policy» من إعدادات الشروط والأحكام في لوحة التحكم.</p>
                                </div>
                            @else
                                <div>
                                    <h2 class="mb-4">Privacy Policy</h2>
                                    <p>Tadawul Bel Araby Academy respects your privacy. This platform uses your data in accordance with our privacy policy. To manage this page content fully, add a page with slug «privacy-policy» from Terms & Conditions settings in the admin panel.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
