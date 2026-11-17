@extends('layouts.app')

@section('title', custom_trans('You are enrolled', 'front') . ' - ' . $course->name)

@php
    $langCode = \App\Helpers\TranslationHelper::getFrontendLanguage()->code ?? 'en';
    $tzLabel = \App\Models\LiveClass::wallTimezone() === 'Asia/Riyadh'
        ? custom_trans('Riyadh time', 'front')
        : \App\Models\LiveClass::wallTimezone();
    $whatsappText = custom_trans('Hi, I just joined', 'front') . ' "' . $course->name . '"';
@endphp

@section('content')
    <div class="container py-5" style="max-width: 640px;">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ custom_trans('Close', 'front') }}"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="mb-3" style="font-size: 3rem; line-height: 1;" aria-hidden="true">✅</div>
                <h1 class="h3 fw-bold mb-2">{{ custom_trans('You are enrolled!', 'front') }}</h1>
                <p class="text-muted mb-4">{{ $course->name }}</p>

                @if ($nextClass && $startsAt)
                    <div class="rounded-4 p-4 mb-4 text-start" style="background: rgba(255, 140, 0, .08); border: 1px solid rgba(255, 140, 0, .25);">
                        <div class="small text-muted mb-1">{{ custom_trans('Next session', 'front') }}</div>
                        <div class="fw-bold fs-5 mb-1">
                            {{ $startsAt->copy()->locale($langCode)->translatedFormat('l j F') }}
                            · {{ $startsAt->copy()->locale($langCode)->translatedFormat('g:i A') }}
                        </div>
                        <div class="small text-muted mb-3">
                            {{ $tzLabel }} · {{ $nextClass->duration_minutes ?: 60 }} {{ custom_trans('minutes', 'front') }}
                            · {{ $startsAt->copy()->locale($langCode)->diffForHumans() }}
                        </div>
                        <div class="fw-semibold mb-3">{{ $nextClass->localized_name }}</div>

                        @if ($canJoinNow)
                            <a href="{{ $nextClass->link }}" target="_blank" rel="noopener"
                                class="btn btn-success w-100 fw-bold mb-2">
                                <i class="fas fa-video me-2"></i>{{ custom_trans('Join the session now', 'front') }}
                            </a>
                        @else
                            <p class="small mb-3">
                                <i class="fas fa-bell me-1"></i>
                                {{ custom_trans('The join link will be sent to you one hour before the session.', 'front') }}
                                {{ custom_trans('We will also remind you one day before.', 'front') }}
                            </p>
                        @endif

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ $nextClass->googleCalendarUrl() }}" target="_blank" rel="noopener"
                                class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="fab fa-google me-1"></i>{{ custom_trans('Add to Google Calendar', 'front') }}
                            </a>
                            <a href="{{ route('live-classes.calendar', $nextClass) }}"
                                class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="far fa-calendar-plus me-1"></i>{{ custom_trans('Apple / Outlook calendar', 'front') }}
                            </a>
                        </div>
                    </div>
                @endif

                @if ($needsPhone)
                    <form method="POST" action="{{ route('courses.enrolled.whatsapp', $course) }}"
                        class="text-start border rounded-4 p-3 mb-4">
                        @csrf
                        <div class="fw-semibold mb-2">
                            <i class="fab fa-whatsapp text-success me-1"></i>{{ custom_trans('Get session reminders on WhatsApp', 'front') }}
                        </div>
                        @include('partials.intl-phone-input', [
                            'label' => null,
                            'defaultCountry' => $defaultCountry ?? 'SA',
                        ])
                        <button type="submit" class="btn btn-success w-100">{{ custom_trans('Save my number', 'front') }}</button>
                    </form>
                @endif

                @unless ($isVerified)
                    <div class="text-start rounded-4 p-3 mb-3" style="background: rgba(255, 193, 7, .12); border: 1px solid rgba(255, 193, 7, .45);">
                        <div class="fw-bold mb-1">
                            <i class="fas fa-envelope me-1"></i>{{ custom_trans('Verify your email to open the course', 'front') }}
                        </div>
                        <div class="small mb-3">
                            {{ custom_trans('We sent a verification link to', 'front') }}
                            <strong dir="ltr">{{ auth()->user()->email }}</strong>.
                            {{ custom_trans('Check your inbox (and spam folder) and click the link.', 'front') }}
                        </div>
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm fw-bold">
                                {{ custom_trans('resend_verification_email', 'front') }}
                            </button>
                        </form>
                    </div>
                @endunless

                <div class="d-grid gap-2">
                    @if ($isVerified)
                        <a href="{{ route('courses.learn', $course) }}" class="btn btn-orange fw-bold">
                            <i class="fas fa-play me-2"></i>{{ custom_trans('Go to the course', 'front') }}
                        </a>
                    @endif
                    @if ($whatsappPhone)
                        <a href="https://wa.me/{{ $whatsappPhone }}?text={{ rawurlencode($whatsappText) }}" target="_blank"
                            rel="noopener" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>{{ custom_trans('Questions? Message us on WhatsApp', 'front') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
