@extends('layouts.app')

@section('title', custom_trans('Edit Profile', 'front'))

@php
    // Social logins store a full URL; uploads store a path on the public disk
    $avatarUrl = $user->avatar
        ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://', 'https://']) ? $user->avatar : asset('storage/' . $user->avatar))
        : null;
    $initial = mb_strtoupper(mb_substr(trim($user->name ?? '?'), 0, 1));
@endphp

@section('content')
    <section class="profile-page">
        <div class="container">
            <header class="profile-page-head">
                <h1>{{ custom_trans('Edit Profile', 'front') }}</h1>
                <p>{{ custom_trans('Manage your personal information and account security', 'front') }}</p>
            </header>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show profile-alert" role="alert">
                    <i class="fa fa-check-circle"></i><span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show profile-alert" role="alert">
                    <i class="fa fa-exclamation-circle"></i>
                    <div>
                        <strong>{{ custom_trans('Please fix the following errors:', 'front') }}</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                {{-- Summary --}}
                <div class="col-lg-4">
                    <aside class="profile-card profile-summary">
                        <div class="profile-summary-cover"></div>
                        <div class="profile-avatar-wrap">
                            <div class="profile-avatar" id="avatarBox">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" id="avatarImg"
                                        onerror="this.remove(); document.getElementById('avatarInitial').hidden = false;">
                                @endif
                                <span class="profile-avatar-initial" id="avatarInitial" @if ($avatarUrl) hidden @endif>{{ $initial }}</span>
                            </div>
                            <label for="avatar" class="profile-avatar-btn" title="{{ custom_trans('Change photo', 'front') }}">
                                <i class="fas fa-camera" aria-hidden="true"></i>
                                <span class="visually-hidden">{{ custom_trans('Change photo', 'front') }}</span>
                            </label>
                        </div>
                        <h2 class="profile-summary-name">{{ $user->name }}</h2>
                        <p class="profile-summary-email">{{ $user->email }}</p>

                        <div class="profile-badges">
                            @if ($user->is_active)
                                <span class="profile-badge is-ok"><i class="fas fa-circle-check"></i>{{ custom_trans('Active', 'front') }}</span>
                            @else
                                <span class="profile-badge is-bad"><i class="fas fa-circle-xmark"></i>{{ custom_trans('Inactive', 'front') }}</span>
                            @endif
                            @if ($user->email_verified_at)
                                <span class="profile-badge is-ok"><i class="fas fa-envelope-circle-check"></i>{{ custom_trans('Verified', 'front') }}</span>
                            @else
                                <span class="profile-badge is-warn"><i class="fas fa-envelope"></i>{{ custom_trans('Not Verified', 'front') }}</span>
                            @endif
                        </div>

                        <ul class="profile-facts">
                            <li>
                                <span><i class="far fa-calendar-check"></i>{{ custom_trans('Member Since', 'front') }}</span>
                                <strong>{{ $user->created_at->format('d-m-Y') }}</strong>
                            </li>
                            <li>
                                <span><i class="far fa-clock"></i>{{ custom_trans('Last Updated', 'front') }}</span>
                                <strong>{{ $user->updated_at->format('d-m-Y') }}</strong>
                            </li>
                            @if ($user->date_of_birth)
                                <li>
                                    <span><i class="fas fa-cake-candles"></i>{{ custom_trans('Age', 'front') }}</span>
                                    <strong>{{ $user->date_of_birth->age }} {{ custom_trans('years old', 'front') }}</strong>
                                </li>
                            @endif
                            @if ($user->country)
                                <li>
                                    <span><i class="fas fa-earth-africa"></i>{{ custom_trans('Country', 'front') }}</span>
                                    <strong>{{ $user->country }}</strong>
                                </li>
                            @endif
                        </ul>
                    </aside>
                </div>

                {{-- Forms --}}
                <div class="col-lg-8">
                    <div class="profile-card">
                        <div class="profile-card-head">
                            <span class="profile-card-icon"><i class="fas fa-user-pen"></i></span>
                            <div>
                                <h3>{{ custom_trans('Profile Information', 'front') }}</h3>
                                <p>{{ custom_trans('Update your name, contact details and bio', 'front') }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form">
                            @csrf
                            @method('PUT')

                            {{-- The camera button on the photo opens this input --}}
                            <input type="file" class="visually-hidden @error('avatar') is-invalid @enderror"
                                id="avatar" name="avatar" accept="image/jpeg,image/png,image/gif">
                            <div class="profile-avatar-hint" id="avatarHint">
                                <i class="fas fa-image"></i>
                                <span id="avatarHintText">{{ custom_trans('Upload a profile picture (JPG, PNG, GIF - Max 2MB)', 'front') }}</span>
                            </div>
                            @error('avatar')
                                <div class="text-danger small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ custom_trans('Full Name', 'front') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ custom_trans('Email Address', 'front') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}" required dir="ltr">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @include('partials.intl-phone-input', [
                                    'label' => custom_trans('Phone Number', 'front'),
                                    'value' => old('phone', $user->phone),
                                    'wrapperClass' => 'col-md-6',
                                    'required' => false,
                                ])

                                <div class="col-md-6">
                                    <label for="gender" class="form-label">{{ custom_trans('Gender', 'front') }}</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">{{ custom_trans('Select Gender', 'front') }}</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ custom_trans('Male', 'front') }}</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ custom_trans('Female', 'front') }}</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ custom_trans('Other', 'front') }}</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label">{{ custom_trans('Date of Birth', 'front') }}</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                        id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="country" class="form-label">{{ custom_trans('Country', 'front') }}</label>
                                    <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                                        <option value="">{{ custom_trans('Select Country', 'front') }}</option>
                                        @foreach ($countries as $code => $name)
                                            <option value="{{ $name }}" {{ old('country', $user->country) == $name ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="bio" class="form-label d-flex justify-content-between">
                                        <span>{{ custom_trans('Bio', 'front') }}</span>
                                        <small class="text-muted"><span id="charCount">0</span>/500</small>
                                    </label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4"
                                        maxlength="500" placeholder="{{ custom_trans("Tell us about yourself, your interests, or what you're learning...", 'front') }}">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="profile-form-actions">
                                <button type="submit" class="profile-btn">
                                    <i class="fa fa-save"></i>{{ custom_trans('Update Profile', 'front') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="profile-card mt-4">
                        <div class="profile-card-head">
                            <span class="profile-card-icon is-warn"><i class="fas fa-lock"></i></span>
                            <div>
                                <h3>{{ custom_trans('Change Password', 'front') }}</h3>
                                <p>{{ custom_trans('Use a strong password you do not use elsewhere', 'front') }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.password') }}" class="profile-form">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label">{{ custom_trans('Current Password', 'front') }}</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password" required autocomplete="current-password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">{{ custom_trans('New Password', 'front') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">{{ custom_trans('Confirm New Password', 'front') }}</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="profile-form-actions">
                                <button type="submit" class="profile-btn is-dark">
                                    <i class="fa fa-key"></i>{{ custom_trans('Change Password', 'front') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Avatar preview (works for repeated picks too)
            var input = document.getElementById('avatar');
            var box = document.getElementById('avatarBox');
            var initial = document.getElementById('avatarInitial');
            var hintText = document.getElementById('avatarHintText');
            input.addEventListener('change', function () {
                var file = this.files && this.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = document.getElementById('avatarImg');
                    if (!img) {
                        img = document.createElement('img');
                        img.id = 'avatarImg';
                        img.alt = '';
                        box.insertBefore(img, initial);
                    }
                    img.src = e.target.result;
                    initial.hidden = true;
                    hintText.textContent = file.name + ' — ' + @json(custom_trans('Click "Update Profile" to save', 'front'));
                    document.getElementById('avatarHint').classList.add('is-ready');
                };
                reader.readAsDataURL(file);
            });

            // Bio character counter
            var bio = document.getElementById('bio');
            var count = document.getElementById('charCount');
            function updateCount() {
                var n = bio.value.length;
                count.textContent = n;
                count.style.color = n > 450 ? '#dc3545' : (n > 400 ? '#d97706' : '');
            }
            bio.addEventListener('input', updateCount);
            updateCount();
        });
    </script>
@endsection

@push('styles')
    <style>
        .profile-page { padding: 2.5rem 0 3.5rem; background: #f7f9fc; }
        .profile-page-head { margin-bottom: 1.5rem; }
        .profile-page-head h1 { margin: 0 0 .25rem; font-size: 1.75rem; font-weight: 800; color: #1e293b; }
        .profile-page-head p { margin: 0; color: #64748b; }
        .profile-alert { display: flex; align-items: flex-start; gap: .6rem; border-radius: 12px; }

        .profile-card {
            background: #fff; border: 1px solid #e8edf3; border-radius: 16px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .04); padding: 1.5rem;
        }

        /* Summary */
        .profile-summary { position: sticky; top: 96px; padding: 0 0 1.25rem; overflow: hidden; text-align: center; }
        .profile-summary-cover { height: 96px; background: linear-gradient(135deg, #156481 0%, #1f86ad 60%, #0f3d52 100%); }
        .profile-avatar-wrap { position: relative; width: 112px; margin: -56px auto .75rem; }
        .profile-avatar {
            width: 112px; height: 112px; border-radius: 50%; overflow: hidden;
            border: 4px solid #fff; background: #e0eef4; box-shadow: 0 6px 18px rgba(15, 23, 42, .15);
            display: flex; align-items: center; justify-content: center;
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-avatar-initial { font-size: 2.5rem; font-weight: 800; color: #156481; }
        .profile-avatar-btn {
            position: absolute; bottom: 4px; inset-inline-end: 0;
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: #ff7043; color: #fff; border: 3px solid #fff; cursor: pointer;
            box-shadow: 0 4px 10px rgba(255, 112, 67, .4); transition: background-color .2s ease;
        }
        .profile-avatar-btn:hover { background: #156481; }
        .profile-summary-name { margin: 0 1rem .2rem; font-size: 1.2rem; font-weight: 800; color: #1e293b; word-break: break-word; }
        .profile-summary-email { margin: 0 1rem .9rem; font-size: .88rem; color: #64748b; word-break: break-all; }
        .profile-badges { display: flex; flex-wrap: wrap; justify-content: center; gap: .4rem; margin: 0 1rem 1.1rem; }
        .profile-badge { display: inline-flex; align-items: center; gap: .35rem; padding: .25rem .65rem; border-radius: 999px; font-size: .76rem; font-weight: 700; }
        .profile-badge.is-ok { background: #dcfce7; color: #15803d; }
        .profile-badge.is-warn { background: #fef3c7; color: #b45309; }
        .profile-badge.is-bad { background: #fee2e2; color: #b91c1c; }
        .profile-facts { list-style: none; margin: 0 1.25rem; padding: .25rem 0 0; border-top: 1px solid #eef2f7; text-align: start; }
        .profile-facts li { display: flex; justify-content: space-between; align-items: center; gap: .75rem; padding: .65rem 0; border-bottom: 1px dashed #eef2f7; font-size: .88rem; }
        .profile-facts li:last-child { border-bottom: 0; }
        .profile-facts span { display: inline-flex; align-items: center; gap: .5rem; color: #64748b; }
        .profile-facts span i { width: 16px; color: #94a3b8; text-align: center; }
        .profile-facts strong { color: #1e293b; font-weight: 700; }

        /* Form cards */
        .profile-card-head { display: flex; align-items: center; gap: .9rem; padding-bottom: 1rem; margin-bottom: 1.25rem; border-bottom: 1px solid #eef2f7; }
        .profile-card-head h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b; }
        .profile-card-head p { margin: .15rem 0 0; font-size: .85rem; color: #64748b; }
        .profile-card-icon {
            flex: 0 0 auto; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            background: #e0eef4; color: #156481; font-size: 1.1rem;
        }
        .profile-card-icon.is-warn { background: #fff1ec; color: #ff7043; }
        .profile-form .form-label { font-size: .88rem; font-weight: 600; color: #334155; margin-bottom: .35rem; }
        .profile-form .form-control,
        .profile-form .form-select { min-height: 46px; border-radius: 10px; border-color: #dbe3ec; }
        .profile-form .form-control:focus,
        .profile-form .form-select:focus { border-color: #156481; box-shadow: 0 0 0 .2rem rgba(21, 100, 129, .15); }
        .profile-form textarea.form-control { min-height: 110px; }
        .profile-form .mb-3 { margin-bottom: 0 !important; }
        .profile-avatar-hint {
            display: flex; align-items: center; gap: .5rem; margin-bottom: 1.1rem; padding: .6rem .85rem;
            border-radius: 10px; background: #f8fafc; border: 1px dashed #cbd5e1; color: #64748b; font-size: .84rem;
        }
        .profile-avatar-hint.is-ready { background: #ecfdf5; border-color: #86efac; color: #15803d; }
        .profile-form-actions { display: flex; justify-content: flex-end; margin-top: 1.25rem; }
        .profile-btn {
            display: inline-flex; align-items: center; gap: .5rem; padding: .7rem 1.4rem; border: 0; border-radius: 10px;
            background: #ff7043; color: #fff; font-weight: 700; transition: background-color .2s ease;
        }
        .profile-btn:hover { background: #156481; color: #fff; }
        .profile-btn.is-dark { background: #156481; }
        .profile-btn.is-dark:hover { background: #0f4a60; }

        @media (max-width: 991.98px) {
            .profile-summary { position: static; }
        }
        @media (max-width: 575.98px) {
            .profile-card { padding: 1.1rem; }
            .profile-summary { padding: 0 0 1rem; }
            .profile-form-actions .profile-btn { width: 100%; justify-content: center; }
        }
    </style>
@endpush
