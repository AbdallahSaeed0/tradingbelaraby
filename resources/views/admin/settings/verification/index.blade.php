@extends('admin.layout')

@section('title', custom_trans('Verification Settings', 'admin'))

@section('content')
    <div class="container-fluid admin-settings-subpage py-3 py-lg-4"
        data-settings-back-url="{{ route('admin.settings.index') }}"
        data-settings-back-label="{{ custom_trans('Settings', 'admin') }}">
        @include('admin.settings.partials.subpage-header', [
            'title' => custom_trans('User Verification Settings', 'admin'),
            'subtitle' => custom_trans('Choose how new users verify their account after registration.', 'admin'),
            'activeBreadcrumb' => custom_trans('Verification Settings', 'admin'),
        ])

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

        @include('admin.settings.partials.section-nav', [
            'sections' => [
                ['id' => 'settings-section-method', 'label' => 'Method', 'icon' => 'fa-shield-alt'],
                ['id' => 'settings-section-whatsapp', 'label' => 'WhatsApp', 'icon' => 'fa-whatsapp'],
            ],
        ])

        <form id="verificationSettingsForm" action="{{ route('admin.settings.verification.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- Verification Method --}}
                <div class="col-12 col-lg-5 mb-4">
                    <div class="card h-100" id="settings-section-method" data-settings-section="Method">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-shield-alt me-2 text-primary"></i>
                                {{ custom_trans('Verification Method', 'admin') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                {{ custom_trans('Select which method new users must use to verify their account after registration.', 'admin') }}
                            </p>

                            <div class="mb-3">
                                <div class="form-check method-card p-3 border rounded mb-2 {{ $settings->method === 'whatsapp' ? 'border-success bg-success bg-opacity-10' : '' }}">
                                    <input class="form-check-input" type="radio" name="method" id="method_whatsapp"
                                        value="whatsapp" {{ $settings->method === 'whatsapp' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="method_whatsapp">
                                        <strong><i class="fab fa-whatsapp text-success me-1"></i> WhatsApp OTP</strong>
                                        <div class="text-muted small mt-1">
                                            Send a 6-digit code to the user's WhatsApp number. Phone number is required at registration.
                                        </div>
                                    </label>
                                </div>

                                <div class="form-check method-card p-3 border rounded mb-2 {{ $settings->method === 'email' ? 'border-primary bg-primary bg-opacity-10' : '' }}">
                                    <input class="form-check-input" type="radio" name="method" id="method_email"
                                        value="email" {{ $settings->method === 'email' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="method_email">
                                        <strong><i class="fas fa-envelope text-primary me-1"></i> Email Link</strong>
                                        <div class="text-muted small mt-1">
                                            Send a verification link to the user's email. Phone number is optional at registration.
                                        </div>
                                    </label>
                                </div>

                                <div class="form-check method-card p-3 border rounded mb-2 {{ $settings->method === 'both' ? 'border-warning bg-warning bg-opacity-10' : '' }}">
                                    <input class="form-check-input" type="radio" name="method" id="method_both"
                                        value="both" {{ $settings->method === 'both' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="method_both">
                                        <strong><i class="fas fa-layer-group text-warning me-1"></i> Both (WhatsApp + Email)</strong>
                                        <div class="text-muted small mt-1">
                                            Send both a WhatsApp OTP and an email link. The user is verified as soon as they complete <em>either one</em>. Phone number is required.
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="whatsapp-note" class="alert alert-info small py-2 {{ $settings->method === 'email' ? 'd-none' : '' }}">
                                <i class="fab fa-whatsapp me-1"></i>
                                Fill in the <strong>WhatsApp API</strong> section on the right before enabling WhatsApp OTP.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- WhatsApp API Config --}}
                <div class="col-12 col-lg-7 mb-4">
                    <div class="card h-100" id="settings-section-whatsapp" data-settings-section="WhatsApp">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="fab fa-whatsapp fa-lg text-success"></i>
                            <h5 class="card-title mb-0">{{ custom_trans('WhatsApp API Configuration', 'admin') }}</h5>
                            <span class="badge bg-success ms-auto">Meta Cloud API</span>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Required when the method includes <strong>WhatsApp OTP</strong>.
                                Values here override the <code>.env</code> file.
                                Leave blank to keep using what's in <code>.env</code>.
                            </p>

                            <div class="mb-3">
                                <label class="form-label" for="whatsapp_token">
                                    {{ custom_trans('Access Token', 'admin') }}
                                    <span class="text-muted small">(META_WHATSAPP_TOKEN)</span>
                                </label>
                                <input type="text" class="form-control font-monospace" id="whatsapp_token"
                                    name="whatsapp_token"
                                    value="{{ old('whatsapp_token') }}"
                                    placeholder="{{ $settings->whatsapp_token ? '••••••••••••  (currently set — leave blank to keep)' : 'Paste your permanent system-user access token' }}"
                                    autocomplete="off">
                                @if ($settings->whatsapp_token)
                                    <div class="form-text text-success"><i class="fas fa-check-circle"></i> Token is set. Leave blank to keep the current value.</div>
                                @else
                                    <div class="form-text">Get this from Meta Business Manager → System Users → Generate Token.</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="whatsapp_phone_number_id">
                                    {{ custom_trans('Phone Number ID', 'admin') }}
                                    <span class="text-muted small">(META_WHATSAPP_PHONE_NUMBER_ID)</span>
                                </label>
                                <input type="text" class="form-control font-monospace" id="whatsapp_phone_number_id"
                                    name="whatsapp_phone_number_id"
                                    value="{{ old('whatsapp_phone_number_id', $settings->whatsapp_phone_number_id) }}"
                                    placeholder="e.g. 123456789012345">
                                <div class="form-text">Found in Meta Developer Console → WhatsApp → API Setup → From (phone number).</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="whatsapp_otp_template">
                                        {{ custom_trans('OTP Template Name', 'admin') }}
                                    </label>
                                    <input type="text" class="form-control" id="whatsapp_otp_template"
                                        name="whatsapp_otp_template"
                                        value="{{ old('whatsapp_otp_template', $settings->whatsapp_otp_template) }}"
                                        placeholder="otp_verification">
                                    <div class="form-text">The approved Authentication template name in WhatsApp Manager.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="whatsapp_api_version">
                                        {{ custom_trans('API Version', 'admin') }}
                                    </label>
                                    <input type="text" class="form-control" id="whatsapp_api_version"
                                        name="whatsapp_api_version"
                                        value="{{ old('whatsapp_api_version', $settings->whatsapp_api_version) }}"
                                        placeholder="v21.0">
                                </div>
                            </div>

                            <div class="alert alert-light border small py-2 mt-1">
                                <strong>Quick setup guide:</strong>
                                <ol class="mb-0 ps-3 mt-1">
                                    <li>Go to <a href="https://developers.facebook.com" target="_blank" rel="noopener">developers.facebook.com</a> → your app → WhatsApp → API Setup</li>
                                    <li>Copy the <strong>Phone Number ID</strong></li>
                                    <li>In <a href="https://business.facebook.com" target="_blank" rel="noopener">business.facebook.com</a> → System Users → Generate Token (with <code>whatsapp_business_messaging</code> scope)</li>
                                    <li>In WhatsApp Manager → Message Templates → Create Authentication template → submit for approval</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 mb-4 admin-settings-inline-actions">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i>{{ custom_trans('Save Settings', 'admin') }}
                </button>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                    {{ custom_trans('Cancel', 'admin') }}
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Highlight selected card and show/hide WhatsApp note
    document.querySelectorAll('input[name="method"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.method-card').forEach(function (card) {
                card.classList.remove('border-success', 'bg-success', 'border-primary', 'bg-primary', 'border-warning', 'bg-warning', 'bg-opacity-10');
            });
            const card = this.closest('.method-card');
            if (this.value === 'whatsapp') {
                card.classList.add('border-success', 'bg-success', 'bg-opacity-10');
            } else if (this.value === 'email') {
                card.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
            } else {
                card.classList.add('border-warning', 'bg-warning', 'bg-opacity-10');
            }
            const note = document.getElementById('whatsapp-note');
            note.classList.toggle('d-none', this.value === 'email');
        });
    });
</script>
@endpush
