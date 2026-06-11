@php
    $settings = \App\Models\MainContentSettings::getActive();
    $siteName = $settings?->site_name ?? config('app.name');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ $siteName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-login.css') }}">
</head>

<body class="admin-login-page">
    <div class="admin-login-shell">
        <div class="admin-login-card">
            <div class="admin-login-header">
                @if ($settings?->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $siteName }}" class="admin-login-logo">
                @else
                    <div class="admin-login-icon">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                @endif
                <h1>Admin Panel</h1>
                <p>Sign in to manage {{ $siteName }}</p>
            </div>

            <div class="admin-login-body">
                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger py-2 mb-0">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.attempt') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="btn password-toggle-btn" id="togglePassword"
                                aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="admin-login-options">
                        <div class="form-check mb-0">
                            <input type="checkbox" name="remember" id="remember" value="1" class="form-check-input"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Keep me logged in</label>
                        </div>
                    </div>

                    <button type="submit" class="btn admin-login-btn">
                        <i class="fas fa-right-to-bracket me-2"></i>Sign in
                    </button>
                </form>

                <div class="admin-login-footer">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-arrow-left me-1"></i>Back to website
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center">
            <span class="admin-login-badge">Staff access only</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            var key = 'admin_keep_logged_in';
            var checkbox = document.getElementById('remember');
            if (checkbox) {
                if (localStorage.getItem(key) === '1') {
                    checkbox.checked = true;
                }
                checkbox.addEventListener('change', function () {
                    localStorage.setItem(key, checkbox.checked ? '1' : '0');
                });
            }

            var toggle = document.getElementById('togglePassword');
            var password = document.getElementById('password');
            if (toggle && password) {
                toggle.addEventListener('click', function () {
                    var isHidden = password.type === 'password';
                    password.type = isHidden ? 'text' : 'password';
                    toggle.querySelector('i').classList.toggle('fa-eye', !isHidden);
                    toggle.querySelector('i').classList.toggle('fa-eye-slash', isHidden);
                });
            }
        })();
    </script>
</body>

</html>
