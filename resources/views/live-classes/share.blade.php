@extends('layouts.app')

{{-- Shareable live session page (/live/{id}). $state: guest | waiting | open | ended | cancelled --}}

@section('title', $liveClass->localized_name . ' - ' . $course->localized_name)

@php
    $langCode = \App\Helpers\TranslationHelper::getFrontendLanguage()->code ?? 'en';
    $tzLabel = \App\Models\LiveClass::wallTimezone() === 'Asia/Riyadh'
        ? custom_trans('Riyadh time', 'front')
        : \App\Models\LiveClass::wallTimezone();
@endphp

@section('content')
    <div class="container py-5" style="max-width: 640px;">
        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="mb-3" style="font-size: 2.6rem; line-height: 1;" aria-hidden="true">🎥</div>
                <div class="text-muted small mb-1">{{ $course->localized_name }}</div>
                <h1 class="h4 fw-bold mb-4">{{ $liveClass->localized_name }}</h1>

                @if ($startsAt)
                    <div class="rounded-4 p-4 mb-4" style="background: rgba(255, 140, 0, .08); border: 1px solid rgba(255, 140, 0, .25);">
                        <div class="fw-bold fs-5 mb-1">
                            {{ $startsAt->copy()->locale($langCode)->translatedFormat('l j F') }}
                            · {{ $startsAt->copy()->locale($langCode)->translatedFormat('g:i A') }}
                        </div>
                        <div class="small text-muted">
                            {{ $tzLabel }} · {{ $liveClass->duration_minutes ?: 60 }} {{ custom_trans('minutes', 'front') }}
                            @if ($state !== 'ended' && $state !== 'cancelled')
                                · {{ $startsAt->copy()->locale($langCode)->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                @endif

                @if ($state === 'guest')
                    <p class="mb-3">{{ custom_trans('Sign up to join this live session.', 'front') }}</p>
                    <a href="{{ route('register', ['enroll' => $course->id]) }}" data-quick-signup
                        class="btn btn-orange w-100 fw-bold mb-2">
                        <i class="fas fa-video me-2"></i>{{ custom_trans('Join the session', 'front') }}
                    </a>
                    <a href="{{ route('login', ['enroll' => $course->id]) }}" class="small">
                        {{ custom_trans('Already have an account?', 'front') }} {{ custom_trans('Sign in', 'front') }}
                    </a>
                @elseif ($state === 'waiting')
                    <p class="mb-3">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        {{ custom_trans('You are registered for this session.', 'front') }}
                        {{ custom_trans('Open this same link at session time and it will take you straight in.', 'front') }}
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a href="{{ $liveClass->googleCalendarUrl() }}" target="_blank" rel="noopener"
                            class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="fab fa-google me-1"></i>{{ custom_trans('Add to Google Calendar', 'front') }}
                        </a>
                        <a href="{{ route('live-classes.calendar', $liveClass) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="far fa-calendar-plus me-1"></i>{{ custom_trans('Apple / Outlook calendar', 'front') }}
                        </a>
                    </div>
                @elseif ($state === 'open')
                    {{-- Session time but no meeting link set yet --}}
                    <div class="alert alert-warning mb-3">{{ custom_trans('The join link is not ready yet. Please refresh this page in a few minutes.', 'front') }}</div>
                @elseif ($state === 'ended')
                    <div class="alert alert-secondary mb-3">{{ custom_trans('This session has ended.', 'front') }}</div>
                @elseif ($state === 'cancelled')
                    <div class="alert alert-secondary mb-3">{{ custom_trans('This session was cancelled.', 'front') }}</div>
                @endif

                @if ($state !== 'guest')
                    <a href="{{ route('courses.learn', $course) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-play me-2"></i>{{ custom_trans('Go to the course', 'front') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if ($state === 'guest')
        @include('courses.partials.quick-signup-modal', ['course' => $course])
    @endif
@endsection
