@extends('layouts.app')

{{--
    In-site Zoom room. $mode:
    - 'sdk':    Zoom Meeting SDK (component view). Asks live.zoom-join for a short-lived signature,
                meeting number and passcode (enrolled students only, only around session time).
    - 'iframe': no SDK keys. Zoom's own browser client in an iframe; its address comes from the
                live.zoom-frame redirect, so the Zoom link is not written in this page.
--}}

@section('title', $liveClass->localized_name . ' - ' . $course->localized_name)

@php
    $zoomBase = 'https://source.zoom.us/' . $zoomVersion;
    $roomTexts = [
        'loading' => custom_trans('Connecting to the session...', 'front'),
        'failed' => custom_trans('The session could not start in the browser.', 'front'),
    ];
@endphp

@push('styles')
    <style>
        .live-room { max-width: 1100px; }
        .live-room__stage { position: relative; min-height: 460px; background: #0f1d2a; border-radius: 16px; overflow: hidden; }
        .live-room__status { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; color: #fff; text-align: center; padding: 24px; z-index: 2; }
        .live-room__status[hidden] { display: none; }
        #zoomMeetingRoot { width: 100%; min-height: 460px; }
        .live-room__frame { display: block; width: 100%; height: 75vh; min-height: 520px; border: 0; background: #fff; }
        @media (max-width: 576px) {
            .live-room__stage, #zoomMeetingRoot { min-height: 340px; }
        }
    </style>
@endpush

@section('content')
    <div class="container live-room py-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <div class="text-muted small">{{ $course->localized_name }}</div>
                <h1 class="h5 fw-bold mb-0">{{ $liveClass->localized_name }}</h1>
            </div>
            <a href="{{ route('courses.learn', ['course' => $course, 'live' => $liveClass->id]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-right me-1"></i>{{ custom_trans('Back to the course', 'front') }}
            </a>
        </div>

        <div class="live-room__stage">
            @if ($mode === 'iframe')
                <iframe class="live-room__frame" src="{{ route('live.zoom-frame', $liveClass) }}"
                    allow="camera; microphone; fullscreen; display-capture; autoplay; clipboard-write"
                    allowfullscreen title="{{ $liveClass->localized_name }}"></iframe>
            @else
            <div id="zoomMeetingRoot"></div>

            <div class="live-room__status" id="roomStatus" @if ($mode === 'iframe') hidden @endif>
                <div class="spinner-border" role="status" aria-hidden="true"></div>
                <div id="roomStatusText">{{ $roomTexts['loading'] }}</div>
            </div>

            <div class="live-room__status" id="roomError" hidden>
                <i class="fas fa-video-slash fa-2x"></i>
                <div id="roomErrorText">{{ $roomTexts['failed'] }}</div>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="btn btn-light btn-sm" onclick="window.location.reload()">
                        <i class="fas fa-rotate-right me-1"></i>{{ custom_trans('Try again', 'front') }}
                    </button>
                    {{-- Server-side hand-off: the Zoom link is not in this page --}}
                    <a href="{{ route('live.open-in-zoom', $liveClass) }}" class="btn btn-orange btn-sm fw-bold">
                        <i class="fas fa-video me-1"></i>{{ custom_trans('Open in the Zoom app', 'front') }}
                    </a>
                </div>
            </div>
            @endif
        </div>

        @if ($mode === 'iframe')
            <div class="text-center mt-2">
                <a href="{{ route('live.open-in-zoom', $liveClass) }}" class="small">
                    <i class="fas fa-video me-1"></i>{{ custom_trans('Open in the Zoom app', 'front') }}
                </a>
            </div>
        @endif

        <p class="text-muted small mt-3 mb-0">
            <i class="fas fa-circle-info me-1"></i>
            {{ custom_trans('Allow microphone and camera access if your browser asks. On some phones the Zoom app works better.', 'front') }}
        </p>
    </div>
@endsection

@if ($mode === 'sdk')
@push('scripts')
    <script src="{{ $zoomBase }}/lib/vendor/react.min.js"></script>
    <script src="{{ $zoomBase }}/lib/vendor/react-dom.min.js"></script>
    <script src="{{ $zoomBase }}/lib/vendor/redux.min.js"></script>
    <script src="{{ $zoomBase }}/lib/vendor/redux-thunk.min.js"></script>
    <script src="{{ $zoomBase }}/lib/vendor/lodash.min.js"></script>
    <script src="{{ $zoomBase }}/zoom-meeting-embedded-{{ $zoomVersion }}.min.js"></script>
    <script>
        (function () {
            const joinUrl = @json(route('live.zoom-join', $liveClass));
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const root = document.getElementById('zoomMeetingRoot');
            const statusBox = document.getElementById('roomStatus');
            const errorBox = document.getElementById('roomError');
            const errorText = document.getElementById('roomErrorText');

            function fail(message) {
                statusBox.hidden = true;
                errorBox.hidden = false;
                if (message) errorText.textContent = message;
            }

            async function start() {
                if (typeof ZoomMtgEmbedded === 'undefined') {
                    fail();
                    return;
                }

                let data;
                try {
                    const response = await fetch(joinUrl, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    data = await response.json();
                    if (!response.ok || !data.success) {
                        fail(data && data.message);
                        return;
                    }
                } catch (e) {
                    fail();
                    return;
                }

                try {
                    const width = Math.min(root.clientWidth, 1100);
                    const height = Math.max(Math.round(width * 9 / 16), 320);
                    const client = ZoomMtgEmbedded.createClient();

                    await client.init({
                        zoomAppRoot: root,
                        language: 'en-US',
                        patchJsMedia: true,
                        leaveOnPageUnload: true,
                        customize: {
                            video: { isResizable: true, viewSizes: { default: { width: width, height: height } } },
                        },
                    });

                    await client.join({
                        sdkKey: data.sdkKey,
                        signature: data.signature,
                        meetingNumber: data.meetingNumber,
                        password: data.password,
                        userName: data.userName,
                        userEmail: data.userEmail,
                    });

                    statusBox.hidden = true;
                } catch (e) {
                    console.error('Zoom join failed', e);
                    fail(e && (e.reason || e.message) ? (e.reason || e.message) : null);
                }
            }

            start();
        })();
    </script>
@endpush
@endif
