@extends('admin.layout')

@section('title', 'Main Content Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                            <li class="breadcrumb-item active">Main Content</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Main Content Settings</h4>
                    <p class="text-muted mb-0">Manage your website's main content including logo, site information, and
                        social media links.</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Main Content Configuration
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.main-content.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Logo Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-image me-2"></i>
                                        Logo Management
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Upload Logo</label>
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                            id="logo" name="logo" accept="image/*">
                                        <div class="form-text">Recommended size: 200x60px. Max file size: 2MB.</div>
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo_alt_text" class="form-label">Logo Alt Text</label>
                                        <input type="text"
                                            class="form-control @error('logo_alt_text') is-invalid @enderror"
                                            id="logo_alt_text" name="logo_alt_text"
                                            value="{{ old('logo_alt_text', $settings->logo_alt_text) }}"
                                            placeholder="Enter logo alt text for accessibility">
                                        @error('logo_alt_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @if ($settings->logo)
                                    <div class="col-12">
                                        <div class="current-logo mb-3">
                                            <label class="form-label">Current Logo</label>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $settings->logo_url }}" alt="Current Logo"
                                                    class="img-thumbnail me-3" style="max-height: 60px;">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeLogo()">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Remove Logo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Favicon Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-star me-2"></i>
                                        Favicon
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="favicon" class="form-label">Upload Favicon</label>
                                        <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                            id="favicon" name="favicon" accept="image/x-icon,image/png">
                                        <div class="form-text">Recommended size: 32x32px or 48x48px. Format: .ico or .png.
                                            Max file size: 512KB.</div>
                                        @error('favicon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if ($settings->favicon)
                                        <div class="mb-3">
                                            <label class="form-label">Current Favicon</label>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $settings->favicon_url }}" alt="Current Favicon"
                                                    style="height:32px;width:32px;" class="me-3">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removeFavicon()">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Remove Favicon
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Site Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Site Information
                                    </h6>
                                    <div class="alert alert-info small mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        The <strong>Site Name</strong> you set here will be used as the default
                                        &lt;title&gt; in the browser tab and in the &lt;head&gt; of your site.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label">Site Name</label>
                                        <input type="text"
                                            class="form-control @error('site_name') is-invalid @enderror" id="site_name"
                                            name="site_name" value="{{ old('site_name', $settings->site_name) }}"
                                            placeholder="Enter your site name">
                                        @error('site_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_author" class="form-label">Site Author</label>
                                        <input type="text"
                                            class="form-control @error('site_author') is-invalid @enderror"
                                            id="site_author" name="site_author"
                                            value="{{ old('site_author', $settings->site_author) }}"
                                            placeholder="Enter site author name">
                                        @error('site_author')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="site_description" class="form-label">Site Description</label>
                                        <textarea class="form-control @error('site_description') is-invalid @enderror" id="site_description"
                                            name="site_description" rows="3" placeholder="Enter a brief description of your site">{{ old('site_description', $settings->site_description) }}</textarea>
                                        @error('site_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="site_keywords" class="form-label">Site Keywords</label>
                                        <input type="text"
                                            class="form-control @error('site_keywords') is-invalid @enderror"
                                            id="site_keywords" name="site_keywords"
                                            value="{{ old('site_keywords', $settings->site_keywords) }}"
                                            placeholder="Enter keywords separated by commas">
                                        @error('site_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back to Settings
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function removeLogo() {
            if (confirm('Are you sure you want to remove the current logo?')) {
                fetch('{{ route('admin.settings.main-content.remove-logo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the logo display
                            const currentLogo = document.querySelector('.current-logo');
                            if (currentLogo) {
                                currentLogo.remove();
                            }

                            // Show success message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                            document.querySelector('.container-fluid').insertBefore(alert, document.querySelector(
                                '.row'));
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the logo.');
                    });
            }
        }

        function removeFavicon() {
            if (confirm('Are you sure you want to remove the current favicon?')) {
                fetch('{{ route('admin.settings.main-content.remove-favicon') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the favicon display
                            const currentFavicon = document.querySelector('.current-favicon');
                            if (currentFavicon) {
                                currentFavicon.remove();
                            }
                            // Show success message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                            document.querySelector('.container-fluid').insertBefore(alert, document.querySelector(
                                '.row'));
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the favicon.');
                    });
            }
        }
    </script>
@endpush
