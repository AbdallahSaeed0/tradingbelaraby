@extends('layouts.app')

@section('title', custom_trans('Edit Profile', 'front'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">
                            <i class="fa fa-user-edit me-2"></i>{{ custom_trans('Edit Profile', 'front') }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i>{{ custom_trans('Please fix the following errors:', 'front') }}
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Profile Information Form -->
                        <div class="mb-5">
                            <h5 class="border-bottom pb-3 mb-4">
                                <i class="fa fa-user me-2"></i>{{ custom_trans('Profile Information', 'front') }}
                            </h5>
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Avatar Section -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <div class="avatar-container mb-3">
                                                @if ($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                                        alt="{{ $user->name }}" class="avatar-preview" id="avatarPreview">
                                                @else
                                                    <div class="avatar-placeholder" id="avatarPreview">
                                                        <i class="fa fa-user fa-3x"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                                <label for="avatar" class="form-label">{{ custom_trans('Profile Picture', 'front') }}</label>
                                                <input type="file"
                                                    class="form-control @error('avatar') is-invalid @enderror"
                                                    id="avatar" name="avatar" accept="image/*">
                                                <div class="form-text">{{ custom_trans('Upload a profile picture (JPG, PNG, GIF - Max 2MB)', 'front') }}
                                                </div>
                                                @error('avatar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">{{ custom_trans('Full Name', 'front') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">{{ custom_trans('Email Address', 'front') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">{{ custom_trans('Phone Number', 'front') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                                placeholder="{{ custom_trans('Enter your phone number', 'front') }}">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">{{ custom_trans('Gender', 'front') }}</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                            name="gender">
                                            <option value="">{{ custom_trans('Select Gender', 'front') }}</option>
                                            <option value="male"
                                                {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ custom_trans('Male', 'front') }}
                                            </option>
                                            <option value="female"
                                                {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ custom_trans('Female', 'front') }}
                                            </option>
                                            <option value="other"
                                                {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ custom_trans('Other', 'front') }}
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">{{ custom_trans('Date of Birth', 'front') }}</label>
                                        <input type="date"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            id="date_of_birth" name="date_of_birth"
                                            value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">{{ custom_trans('Country', 'front') }}</label>
                                        <select class="form-select @error('country') is-invalid @enderror" id="country"
                                            name="country">
                                            <option value="">{{ custom_trans('Select Country', 'front') }}</option>
                                            @foreach ($countries as $code => $name)
                                                <option value="{{ $name }}"
                                                    {{ old('country', $user->country) == $name ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="bio" class="form-label">{{ custom_trans('Bio', 'front') }}</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4"
                                            maxlength="500" placeholder="{{ custom_trans("Tell us about yourself, your interests, or what you're learning...", 'front') }}">{{ old('bio', $user->bio) }}</textarea>
                                        <div class="form-text">
                                            <span id="charCount">0</span>/500 {{ custom_trans('characters', 'front') }}
                                        </div>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-save me-2"></i>{{ custom_trans('Update Profile', 'front') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Form -->
                        <div class="border-top pt-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fa fa-lock me-2"></i>{{ custom_trans('Change Password', 'front') }}
                            </h5>
                            <form method="POST" action="{{ route('profile.password') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="current_password" class="form-label">{{ custom_trans('Current Password', 'front') }}</label>
                                        <input type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" name="current_password" required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">{{ custom_trans('New Password', 'front') }}</label>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">{{ custom_trans('Confirm New Password', 'front') }}</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa fa-key me-2"></i>{{ custom_trans('Change Password', 'front') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Account Information -->
                        <div class="border-top pt-4 mt-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fa fa-info-circle me-2"></i>{{ custom_trans('Account Information', 'front') }}
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ custom_trans('Member Since', 'front') }}:</strong> {{ $user->created_at->format('F d, Y') }}</p>
                                    <p><strong>{{ custom_trans('Last Updated', 'front') }}:</strong> {{ $user->updated_at->format('F d, Y') }}</p>
                                    @if ($user->date_of_birth)
                                        <p><strong>{{ custom_trans('Age', 'front') }}:</strong> {{ $user->date_of_birth->age }} {{ custom_trans('years old', 'front') }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ custom_trans('Account Status', 'front') }}:</strong>
                                        @if ($user->is_active)
                                            <span class="badge bg-success">{{ custom_trans('Active', 'front') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ custom_trans('Inactive', 'front') }}</span>
                                        @endif
                                    </p>
                                    <p><strong>{{ custom_trans('Email Verified', 'front') }}:</strong>
                                        @if ($user->email_verified_at)
                                            <span class="badge bg-success">{{ custom_trans('Verified', 'front') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ custom_trans('Not Verified', 'front') }}</span>
                                        @endif
                                    </p>
                                    @if ($user->country)
                                        <p><strong>{{ custom_trans('Country', 'front') }}:</strong> {{ $user->country }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><script>
        document.addEventListener('DOMContentLoaded', function() {
            // Avatar preview
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('avatarPreview');

            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (avatarPreview.tagName === 'IMG') {
                            avatarPreview.src = e.target.result;
                        } else {
                            // Replace placeholder with image
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Profile Preview';
                            img.className = 'avatar-preview';
                            img.id = 'avatarPreview';
                            avatarPreview.parentNode.replaceChild(img, avatarPreview);
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Character counter for bio
            const bioTextarea = document.getElementById('bio');
            const charCount = document.getElementById('charCount');

            function updateCharCount() {
                const length = bioTextarea.value.length;
                charCount.textContent = length;

                if (length > 450) {
                    charCount.style.color = '#dc3545';
                } else if (length > 400) {
                    charCount.style.color = '#ffc107';
                } else {
                    charCount.style.color = '#6c757d';
                }
            }

            bioTextarea.addEventListener('input', updateCharCount);
            updateCharCount(); // Initialize count
        });
    </script>
@endsection

