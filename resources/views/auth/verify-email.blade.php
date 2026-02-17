@extends('layouts.app')

@section('title', custom_trans('verify_email_title', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-560">
        <div class="card p-4 shadow-sm">
            <h2 class="text-center mb-4">{{ custom_trans('verify_email_title', 'front') }}</h2>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <p class="text-center">
                    {{ custom_trans('verify_email_message', 'front') }}
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success" role="alert">
                    {{ custom_trans('verify_email_link_sent', 'front') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    {{ custom_trans('resend_verification_email', 'front') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link w-100 text-decoration-none">
                    {{ custom_trans('logout', 'front') }}
                </button>
            </form>
        </div>
    </div>
@endsection
