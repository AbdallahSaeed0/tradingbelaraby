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
                        <form id="mainContentForm" action="{{ route('admin.settings.main-content.update') }}" method="POST"
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
                                        <label for="logo" class="form-label">Upload Logo <span
                                                class="text-muted small">(Optional)</span></label>
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                            id="logo" name="logo" accept="image/*">
                                        <div class="form-text">Recommended size: 200x60px. Max file size: 2MB.</div>
                                        <div id="logoFileInfo" class="mt-1"></div>
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo_alt_text" class="form-label">Logo Alt Text <span
                                                class="text-muted small">(Optional)</span></label>
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
                                                    class="img-thumbnail me-3 logo-preview-sm">
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
                                        <label for="favicon" class="form-label">Upload Favicon <span
                                                class="text-muted small">(Optional)</span></label>
                                        <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                            id="favicon" name="favicon" accept=".ico,.png,.jpg">
                                        <div class="form-text">Recommended size: 32x32px or 48x48px. Format: .ico, .png, or
                                            .jpg.
                                            Max file size: 512KB.</div>
                                        <div id="faviconFileInfo" class="mt-1"></div>
                                        @error('favicon')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if ($settings->favicon)
                                        <div class="mb-3 current-favicon-container">
                                            <label class="form-label">Current Favicon</label>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $settings->favicon_url }}" alt="Current Favicon"
                                                    class="favicon-preview me-3">
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
                                        <label for="site_name" class="form-label">Site Name <span
                                                class="text-muted small">(Optional)</span></label>
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
                                        <label for="site_author" class="form-label">Site Author <span
                                                class="text-muted small">(Optional)</span></label>
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
                                        <label for="site_description" class="form-label">Site Description <span
                                                class="text-muted small">(Optional)</span></label>
                                        <textarea class="form-control @error('site_description') is-invalid @enderror" id="site_description"
                                            name="site_description" rows="3" placeholder="Enter a brief description of your site">{{ old('site_description', $settings->site_description) }}</textarea>
                                        @error('site_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="site_keywords" class="form-label">Site Keywords <span
                                                class="text-muted small">(Optional)</span></label>
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
                                        <button type="submit" class="btn btn-primary" id="saveSettingsBtn">
                                            <i class="fas fa-save me-2"></i>
                                            <span id="btnText">Save Settings</span>
                                            <span id="btnLoading" class="d-none">
                                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                                    aria-hidden="true"></span>
                                                Saving...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coming Soon Control - Outside main form to avoid nesting -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-8">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Coming Soon Mode Control
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-warning mb-2">Website Access Control</h6>
                                <p class="text-muted mb-0">
                                    When enabled, all visitors will see the coming soon page except
                                    admin users.
                                    This is useful when you're preparing your website for launch.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="coming-soon-status mb-3 justify-center">
                                    @php
                                        $settings = \App\Models\MainContentSettings::getActive();
                                        $comingSoonEnabled = $settings
                                            ? $settings->coming_soon_enabled
                                            : false;
                                    @endphp
                                    <div
                                        class="alert {{ $comingSoonEnabled ? 'alert-warning' : 'alert-success' }} mb-0 status-box-fit">
                                        <i
                                            class="fas {{ $comingSoonEnabled ? 'fa-exclamation-triangle' : 'fa-check-circle' }} me-2"></i>
                                        <strong>{{ $comingSoonEnabled ? 'ACTIVE' : 'DISABLED' }}</strong>
                                    </div>
                                </div>
                                <form action="{{ route('admin.settings.coming-soon.update') }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="coming_soon_enabled"
                                        value="{{ $comingSoonEnabled ? '0' : '1' }}">
                                    <button type="submit"
                                        class="btn {{ $comingSoonEnabled ? 'btn-success' : 'btn-warning' }} btn-lg">
                                        <i
                                            class="fas {{ $comingSoonEnabled ? 'fa-toggle-off' : 'fa-toggle-on' }} me-2"></i>
                                        {{ $comingSoonEnabled ? 'Disable Coming Soon' : 'Enable Coming Soon' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Client-side file validation
        document.addEventListener('DOMContentLoaded', function() {
            // Add form submit debugging and loading state
            const mainForm = document.getElementById('mainContentForm');
            const saveBtn = document.getElementById('saveSettingsBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            console.log('Main form found:', mainForm !== null);
            console.log('Save button found:', saveBtn !== null);

            if (mainForm) {
                // Check if form has any validation issues
                console.log('Form validation state:', mainForm.checkValidity ? mainForm.checkValidity() : 'N/A');

                // Add invalid event listener to catch validation errors
                mainForm.addEventListener('invalid', function(e) {
                    console.error('❌ Form validation failed on field:', e.target.name, e.target
                        .validationMessage);
                }, true);

                mainForm.addEventListener('submit', function(e) {
                    console.log('✅ Form is being submitted...');

                    // Check form validity first
                    if (!this.checkValidity()) {
                        console.log('❌ Form validation failed');
                        this.reportValidity();
                        e.preventDefault();
                        return false; // Don't submit if invalid
                    }

                    // Show loading state
                    if (saveBtn && btnText && btnLoading) {
                        saveBtn.disabled = true;
                        btnText.classList.add('d-none');
                        btnLoading.classList.remove('d-none');
                    }

                    // Log form data for debugging
                    const formData = new FormData(this);
                    console.log('📋 Form fields:');
                    for (let [key, value] of formData.entries()) {
                        if (value instanceof File) {
                            console.log('  📎 ' + key + ':', value.name, value.size + ' bytes', 'Type:',
                                value.type);
                        } else if (value) {
                            console.log('  ✏️ ' + key + ':', value);
                        }
                    }

                    // Set a timeout to re-enable button if form doesn't submit (fallback)
                    setTimeout(function() {
                        if (saveBtn && saveBtn.disabled) {
                            console.warn('⚠️ Form submission seems stuck, re-enabling button');
                            saveBtn.disabled = false;
                            if (btnText && btnLoading) {
                                btnText.classList.remove('d-none');
                                btnLoading.classList.add('d-none');
                            }
                        }
                    }, 10000); // 10 seconds timeout

                    // Allow form to submit normally (don't prevent default)
                    console.log('🚀 Form will now submit to server...');
                    // Form will submit naturally - no preventDefault()
                });

                // Re-enable button if page loads with errors (form was submitted but had validation errors)
                window.addEventListener('load', function() {
                    if (saveBtn && btnText && btnLoading) {
                        // Always re-enable on page load (in case of redirect back with errors)
                        saveBtn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                    }
                });
            } else {
                console.error('❌ Main form NOT found! Check if form ID is correct.');
            }

            // Logo file validation
            const logoInput = document.getElementById('logo');
            const logoFileInfo = document.getElementById('logoFileInfo');

            if (logoInput) {
                logoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const fileSize = (file.size / 1024).toFixed(2);
                        const maxSize = 2 * 1024 * 1024; // 2MB in bytes

                        // Display file info
                        if (logoFileInfo) {
                            logoFileInfo.innerHTML = `<div class="alert alert-info py-2 mb-0">
                                <i class="fas fa-check-circle me-1"></i>
                                <strong>Selected:</strong> ${file.name} (${fileSize} KB)
                            </div>`;
                        }

                        // Check file size
                        if (file.size > maxSize) {
                            alert('Logo file is too large. Maximum size is 2MB. Your file is ' + fileSize +
                                'KB.');
                            e.target.value = '';
                            if (logoFileInfo) {
                                logoFileInfo.innerHTML = `<div class="alert alert-danger py-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i> File too large!
                                </div>`;
                            }
                            return;
                        }

                        // Check file type
                        const fileName = file.name.toLowerCase();
                        const validExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.svg'];
                        const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));

                        if (!hasValidExtension) {
                            alert(
                                'Invalid file type. Please upload an image file (JPEG, PNG, GIF, or SVG).'
                                );
                            e.target.value = '';
                            if (logoFileInfo) {
                                logoFileInfo.innerHTML = `<div class="alert alert-danger py-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Invalid file type!
                                </div>`;
                            }
                            return;
                        }

                        console.log('Logo file selected:', file.name, 'Size:', fileSize + ' KB');
                    } else {
                        if (logoFileInfo) {
                            logoFileInfo.innerHTML = '';
                        }
                    }
                });
            }

            // Favicon file validation
            const faviconInput = document.getElementById('favicon');
            const faviconFileInfo = document.getElementById('faviconFileInfo');

            if (faviconInput) {
                faviconInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Check file size (512KB max)
                        const maxSize = 512 * 1024; // 512KB in bytes
                        const fileSize = (file.size / 1024).toFixed(2);

                        // Display file info
                        if (faviconFileInfo) {
                            faviconFileInfo.innerHTML = `<div class="alert alert-info py-2 mb-0">
                                <i class="fas fa-check-circle me-1"></i>
                                <strong>Selected:</strong> ${file.name} (${fileSize} KB)
                            </div>`;
                        }

                        if (file.size > maxSize) {
                            alert('Favicon file is too large. Maximum size is 512KB. Your file is ' +
                                fileSize + 'KB.');
                            e.target.value = '';
                            if (faviconFileInfo) {
                                faviconFileInfo.innerHTML = `<div class="alert alert-danger py-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i> File too large!
                                </div>`;
                            }
                            return;
                        }

                        // Check file type
                        const allowedTypes = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png',
                            'image/jpeg', 'image/jpg'
                        ];
                        const allowedExtensions = ['.ico', '.png', '.jpg', '.jpeg'];
                        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

                        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(
                                fileExtension)) {
                            alert('Invalid file type. Please upload a favicon file (.ico, .png, or .jpg).');
                            e.target.value = '';
                            if (faviconFileInfo) {
                                faviconFileInfo.innerHTML = `<div class="alert alert-danger py-2 mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Invalid file type!
                                </div>`;
                            }
                            return;
                        }

                        console.log('Favicon file selected:', file.name, 'Type:', file.type, 'Size:',
                            fileSize + ' KB');
                    } else {
                        if (faviconFileInfo) {
                            faviconFileInfo.innerHTML = '';
                        }
                    }
                });
            }
        });

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
                            const container = document.querySelector('.container-fluid');
                            const firstRow = container.querySelector('.row');
                            if (firstRow && firstRow.parentNode === container) {
                                container.insertBefore(alert, firstRow);
                            } else {
                                container.insertBefore(alert, container.firstChild);
                            }
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
            // Use Bootstrap modal for confirmation
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.setAttribute('tabindex', '-1');
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Removal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to remove the current favicon?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmRemoveFavicon">Remove</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            const confirmBtn = document.getElementById('confirmRemoveFavicon');
            confirmBtn.addEventListener('click', function() {
                bsModal.hide();
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
                            const currentFaviconContainer = document.querySelector('.current-favicon-container');
                            if (currentFaviconContainer && currentFaviconContainer.parentNode) {
                                currentFaviconContainer.parentNode.removeChild(currentFaviconContainer);
                            }
                            // Show success message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                            const container = document.querySelector('.container-fluid');
                            const firstRow = container.querySelector('.row');
                            if (firstRow && firstRow.parentNode === container) {
                                container.insertBefore(alert, firstRow);
                            } else {
                                container.insertBefore(alert, container.firstChild);
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the favicon.');
                    });
            });
            
            // Remove modal when hidden
            modal.addEventListener('hidden.bs.modal', function() {
                if (modal && modal.parentNode === document.body) {
                    try {
                        document.body.removeChild(modal);
                    } catch (e) {
                        console.log('Modal already removed');
                    }
                }
            });
        }
    </script>
@endpush
