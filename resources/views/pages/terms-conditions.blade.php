@extends('layouts.app')

@php
    $currentLang = app()->getLocale();
    $pageTitle = isset($termsConditions) 
        ? ($currentLang == 'ar' ? ($termsConditions->title_ar ?? 'الشروط والأحكام') : ($termsConditions->title ?? 'Terms and Conditions'))
        : ($currentLang == 'ar' ? 'الشروط والأحكام' : 'Terms and Conditions');
@endphp
@section('title', $pageTitle . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    @php
        $currentLang = app()->getLocale();
        $pageTitle = $currentLang == 'ar' ? ($termsConditions->title_ar ?? 'الشروط والأحكام') : ($termsConditions->title ?? 'Terms and Conditions');
    @endphp

    <!-- Page Banner -->
    <section class="contact-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="contact-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="contact-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">
                {{ $currentLang == 'ar' ? ($termsConditions->title_ar ?? 'الشروط والأحكام') : ($termsConditions->title ?? 'Terms and Conditions') }}
            </h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="contact-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">
                        {{ custom_trans('home', 'front') }}
                    </a> &nbsp;|&nbsp;
                    {{ $currentLang == 'ar' ? ($termsConditions->title_ar ?? 'الشروط والأحكام') : ($termsConditions->title ?? 'Terms and Conditions') }}
                </span>
            </div>
        </div>
    </section>

    <!-- Terms and Conditions Content Section -->
    <section class="terms-content-section py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <div class="terms-content">
                                @if ($currentLang == 'ar')
                                    <div dir="rtl" class="text-end">
                                        @if($termsConditions->title_ar)
                                            <h2 class="mb-4">{{ $termsConditions->title_ar }}</h2>
                                        @endif
                                        {!! $termsConditions->description_ar !!}
                                    </div>
                                @else
                                    <div>
                                        @if($termsConditions->title)
                                            <h2 class="mb-4">{{ $termsConditions->title }}</h2>
                                        @endif
                                        {!! $termsConditions->description !!}
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
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/terms-conditions.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/terms-conditions.css') }}">
    @endpush
@endif
