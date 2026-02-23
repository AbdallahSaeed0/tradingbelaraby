@extends('admin.layout')

@section('title', custom_trans('Social Login Providers', 'admin'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a></li>
                            <li class="breadcrumb-item active">{{ custom_trans('Social Login Providers', 'admin') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ custom_trans('Social Login Providers', 'admin') }}</h4>
                    <p class="text-muted mb-0">{{ custom_trans('Manage Google and Twitter (X) login credentials and redirect URIs. Secrets are stored on the server only.', 'admin') }}</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="google-tab" data-bs-toggle="tab" data-bs-target="#google-pane" type="button" role="tab">Google</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="twitter-tab" data-bs-toggle="tab" data-bs-target="#twitter-pane" type="button" role="tab">Twitter (X)</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Google tab --}}
                            <div class="tab-pane fade show active" id="google-pane" role="tabpanel">
                                <form action="{{ route('admin.settings.social-login.update-google') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enabled" id="google-enabled" value="1" {{ ($google->enabled ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="google-enabled">{{ custom_trans('Enable Google login', 'admin') }}</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="google-client_id" class="form-label">{{ custom_trans('Client ID (Web)', 'admin') }}</label>
                                        <input type="text" class="form-control" id="google-client_id" name="client_id" value="{{ old('client_id', $google->client_id) }}" placeholder="xxx.apps.googleusercontent.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="google-client_secret" class="form-label">{{ custom_trans('Client Secret', 'admin') }} <span class="text-muted small">({{ custom_trans('Update only to change; leave blank to keep current', 'admin') }})</span></label>
                                        <input type="password" class="form-control" id="google-client_secret" name="client_secret" value="" placeholder="{{ $google->client_secret ? '••••••••••••' : '' }}" autocomplete="new-password">
                                        @if($google->client_secret)
                                            <div class="form-text">{{ custom_trans('Currently set. Enter a new value only to replace.', 'admin') }}</div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ custom_trans('Redirect URI', 'admin') }}</label>
                                        <input type="text" class="form-control bg-light" value="{{ old('redirect_uri', $google->redirect_uri ?? $defaultRedirectGoogle) }}" name="redirect_uri" id="google-redirect_uri" placeholder="{{ $defaultRedirectGoogle }}">
                                        <div class="form-text">{{ custom_trans('Default (if empty):', 'admin') }} <code>{{ $defaultRedirectGoogle }}</code>. {{ custom_trans('Must match the URI configured in Google Cloud Console.', 'admin') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="google-android_client_id" class="form-label">{{ custom_trans('Android Client ID', 'admin') }} <span class="text-muted small">({{ custom_trans('Optional, for app API verification', 'admin') }})</span></label>
                                        <input type="text" class="form-control" id="google-android_client_id" name="android_client_id" value="{{ old('android_client_id', $google->getExtraValue('android_client_id')) }}" placeholder="xxx.apps.googleusercontent.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="google-ios_client_id" class="form-label">{{ custom_trans('iOS Client ID', 'admin') }} <span class="text-muted small">({{ custom_trans('Optional, for app API verification', 'admin') }})</span></label>
                                        <input type="text" class="form-control" id="google-ios_client_id" name="ios_client_id" value="{{ old('ios_client_id', $google->getExtraValue('ios_client_id')) }}" placeholder="xxx.apps.googleusercontent.com">
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ custom_trans('Save Google settings', 'admin') }}</button>
                                </form>
                            </div>

                            {{-- Twitter tab --}}
                            <div class="tab-pane fade" id="twitter-pane" role="tabpanel">
                                <form action="{{ route('admin.settings.social-login.update-twitter') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3 form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enabled" id="twitter-enabled" value="1" {{ ($twitter->enabled ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="twitter-enabled">{{ custom_trans('Enable Twitter (X) login', 'admin') }}</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="twitter-client_id" class="form-label">{{ custom_trans('Client ID (OAuth 2.0)', 'admin') }}</label>
                                        <input type="text" class="form-control" id="twitter-client_id" name="client_id" value="{{ old('client_id', $twitter->client_id) }}" placeholder="OAuth 2.0 Client ID">
                                    </div>
                                    <div class="mb-3">
                                        <label for="twitter-client_secret" class="form-label">{{ custom_trans('Client Secret', 'admin') }} <span class="text-muted small">({{ custom_trans('Update only to change; leave blank to keep current', 'admin') }})</span></label>
                                        <input type="password" class="form-control" id="twitter-client_secret" name="client_secret" value="" placeholder="{{ $twitter->client_secret ? '••••••••••••' : '' }}" autocomplete="new-password">
                                        @if($twitter->client_secret)
                                            <div class="form-text">{{ custom_trans('Currently set. Enter a new value only to replace.', 'admin') }}</div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ custom_trans('Redirect URI', 'admin') }}</label>
                                        <input type="text" class="form-control bg-light" value="{{ old('redirect_uri', $twitter->redirect_uri ?? $defaultRedirectTwitter) }}" name="redirect_uri" id="twitter-redirect_uri" placeholder="{{ $defaultRedirectTwitter }}">
                                        <div class="form-text">{{ custom_trans('Default (if empty):', 'admin') }} <code>{{ $defaultRedirectTwitter }}</code>. {{ custom_trans('Must match the URI in Twitter Developer Portal.', 'admin') }}</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ custom_trans('Save Twitter settings', 'admin') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
