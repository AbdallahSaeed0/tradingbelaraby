<!DOCTYPE html>
<html lang="{{ \App\Helpers\TranslationHelper::getCurrentLanguage()->code }}"
    dir="{{ \App\Helpers\TranslationHelper::getCurrentLanguage()->direction }}" data-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('admin/pages-ltr/blog.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/pages-ltr/admin.css') }}">

    <!-- Admin Styles - Base (always loaded) -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-settings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-analytics.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-content-management.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-quiz.css') }}">
    
    <!-- Dark Mode Styles - Loaded conditionally -->
    <link rel="stylesheet" href="{{ asset('css/admin/dark/admin-common-dark.css') }}" id="dark-common" media="none">
    <link rel="stylesheet" href="{{ asset('css/admin/dark/admin-forms-dark.css') }}" id="dark-forms" media="none">
    <link rel="stylesheet" href="{{ asset('css/admin/dark/admin-tables-dark.css') }}" id="dark-tables" media="none">
    <link rel="stylesheet" href="{{ asset('css/admin/dark/admin-settings-dark.css') }}" id="dark-settings" media="none">
    <link rel="stylesheet" href="{{ asset('css/admin/dark/admin-analytics-dark.css') }}" id="dark-analytics" media="none">
    <link rel="stylesheet" href="{{ asset('css/admin/dark/admin-content-management-dark.css') }}" id="dark-content" media="none">
    <link rel="stylesheet" href="{{ asset('css/admin/dark/admin-quiz-dark.css') }}" id="dark-quiz" media="none">

    <!-- Shared Component Styles -->
    <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alerts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/badges.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/language-switcher.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities-extended.css') }}">

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-tables-mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-form-mobile.css') }}">
</head>

<body class="bg-light @yield('body_class')">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm admin-top-navbar">
        <div class="container-fluid admin-navbar-inner">
            <div class="admin-navbar-start">
                <button id="sidebarToggle" class="btn btn-outline-secondary shadow-sm" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar"
                    aria-label="Toggle sidebar">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <ul class="navbar-nav admin-navbar-actions ms-auto align-items-center">
                <!-- Visit Site -->
                <li class="nav-item me-3 admin-nav-visit-site">
                    <a class="nav-link admin-navbar-icon-btn" href="{{ url('/') }}" target="_blank"
                        title="Visit site">
                        <i class="fa fa-globe"></i>
                    </a>
                </li>
                <!-- Theme Toggle -->
                <li class="nav-item me-3">
                    <button class="btn btn-link nav-link admin-navbar-icon-btn p-0" id="themeToggle" type="button"
                        title="Toggle theme">
                        <i class="fa fa-moon" id="themeIcon"></i>
                    </button>
                </li>
                <!-- Admin Language Switcher -->
                <li class="nav-item me-3">
                    @include('partials.admin-language-switcher')
                </li>
                <!-- Notifications -->
                @php
                    $pendingQuestions = \App\Models\QuestionsAnswer::where('status', 'pending')->count();
                    $urgentQuestions = \App\Models\QuestionsAnswer::where('priority', 'urgent')->count();
                    $totalNotifications = $pendingQuestions + $urgentQuestions;
                @endphp
                <li class="nav-item dropdown me-3">
                    <a class="nav-link admin-navbar-icon-btn position-relative" href="#" id="notifDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                        <i class="fa fa-bell"></i>
                        @if ($totalNotifications > 0)
                            <span
                                class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle p-1 small">{{ $totalNotifications }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end min-w-250" aria-labelledby="notifDropdown">
                        <li class="dropdown-header">Notifications</li>
                        @if ($totalNotifications > 0)
                            @if ($pendingQuestions > 0)
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.questions-answers.index', ['status' => 'pending']) }}">
                                        <i class="fa fa-clock text-warning me-2"></i>
                                        {{ $pendingQuestions }} pending question{{ $pendingQuestions > 1 ? 's' : '' }}
                                    </a>
                                </li>
                            @endif
                            @if ($urgentQuestions > 0)
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.questions-answers.index', ['priority' => 'urgent']) }}">
                                        <i class="fa fa-exclamation-triangle text-danger me-2"></i>
                                        {{ $urgentQuestions }} urgent question{{ $urgentQuestions > 1 ? 's' : '' }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li><span class="dropdown-item text-muted">No new notifications</span></li>
                        @endif
                    </ul>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center profile-dropdown" href="#"
                        id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if (auth('admin')->user()->avatar)
                            <img src="{{ auth('admin')->user()->avatar_url }}?v={{ auth('admin')->user()->updated_at->timestamp }}"
                                class="rounded-circle me-2" width="32" height="32" alt="avatar">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth('admin')->user()->name) }}&size=32&background=007bff&color=fff"
                                class="rounded-circle me-2" width="32" height="32" alt="avatar">
                        @endif
                        <span class="admin-profile-name">Hi {{ auth('admin')->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}">My Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="d-xl-flex">
        @include('admin.partials.sidebar')

        <!-- Page Content -->
        <main class="flex-grow-1 py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @stack('scripts')
    
    <!-- Placeholder fix - Loads after all styles to override Bootstrap -->
    <style id="final-placeholder-fix">
        html[data-theme="dark"] input::placeholder,
        html[data-theme="dark"] input::-webkit-input-placeholder,
        html[data-theme="dark"] input::-moz-placeholder,
        html[data-theme="dark"] input:-ms-input-placeholder,
        html[data-theme="dark"] input:-moz-placeholder,
        html[data-theme="dark"] textarea::placeholder,
        html[data-theme="dark"] textarea::-webkit-input-placeholder,
        html[data-theme="dark"] textarea::-moz-placeholder,
        html[data-theme="dark"] textarea:-ms-input-placeholder,
        html[data-theme="dark"] textarea:-moz-placeholder,
        html[data-theme="dark"] .form-control::placeholder,
        html[data-theme="dark"] .form-control::-webkit-input-placeholder,
        html[data-theme="dark"] .form-control::-moz-placeholder,
        html[data-theme="dark"] .form-control:-ms-input-placeholder,
        html[data-theme="dark"] .form-control:-moz-placeholder,
        html[data-theme="dark"] .input-group input::placeholder,
        html[data-theme="dark"] .input-group input::-webkit-input-placeholder,
        html[data-theme="dark"] .input-group input::-moz-placeholder,
        html[data-theme="dark"] .input-group input:-ms-input-placeholder,
        html[data-theme="dark"] .input-group input:-moz-placeholder {
            color: #d0d3d8 !important;
            opacity: 1 !important;
        }
    </style>
    
    <script>
        // Additional placeholder fix that runs after page load
        (function() {
            function forcePlaceholderFix() {
                if (document.documentElement.getAttribute('data-theme') === 'dark') {
                    const inputs = document.querySelectorAll('input, textarea');
                    inputs.forEach(input => {
                        if (input.placeholder) {
                            const id = input.id || ('ph-' + Math.random().toString(36).substr(2, 9));
                            if (!input.id) input.id = id;
                            
                            const styleId = 'ph-force-' + id;
                            if (!document.getElementById(styleId)) {
                                const style = document.createElement('style');
                                style.id = styleId;
                                style.textContent = `#${id}::placeholder { color: #d0d3d8 !important; opacity: 1 !important; }`;
                                document.head.appendChild(style);
                            }
                        }
                    });
                }
            }
            
            // Run multiple times
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(forcePlaceholderFix, 100);
                    setTimeout(forcePlaceholderFix, 500);
                    setTimeout(forcePlaceholderFix, 1000);
                });
            } else {
                setTimeout(forcePlaceholderFix, 100);
                setTimeout(forcePlaceholderFix, 500);
                setTimeout(forcePlaceholderFix, 1000);
            }
        })();
    </script>

    <script>
        // Theme Management
        (function() {
            // Initialize theme on page load
            function initTheme() {
                const savedTheme = localStorage.getItem('adminTheme') || 'light';
                setTheme(savedTheme);
            }

            // Set theme
            function setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                const themeIcon = document.getElementById('themeIcon');
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.className = 'fa fa-sun';
                        themeIcon.parentElement.setAttribute('title', 'Switch to light mode');
                    } else {
                        themeIcon.className = 'fa fa-moon';
                        themeIcon.parentElement.setAttribute('title', 'Switch to dark mode');
                    }
                }
                localStorage.setItem('adminTheme', theme);
                
                // Toggle dark mode CSS files
                const darkStyles = [
                    'dark-common',
                    'dark-forms',
                    'dark-tables',
                    'dark-settings',
                    'dark-analytics',
                    'dark-content',
                    'dark-quiz'
                ];
                
                darkStyles.forEach(id => {
                    const link = document.getElementById(id);
                    if (link) {
                        if (theme === 'dark') {
                            link.media = 'all';
                        } else {
                            link.media = 'none';
                        }
                    }
                });
            }

            // Toggle theme
            function toggleTheme() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
                applyPlaceholderFix(newTheme);
            }

            // Global placeholder fix for dark mode - NEW APPROACH: Direct style manipulation
            function applyPlaceholderFix(theme) {
                const placeholderColor = '#d0d3d8'; // Brighter color
                
                function fixAllInputs() {
                    const inputs = document.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        if (input.placeholder && theme === 'dark') {
                            // Method 1: Set CSS custom property
                            input.style.setProperty('--placeholder-color', placeholderColor, 'important');
                            
                            // Method 2: Direct style manipulation via computed styles override
                            // Create a style element for this specific input
                            const inputId = input.id || ('ph-input-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5));
                            if (!input.id) input.id = inputId;
                            
                            const styleId = 'ph-fix-' + inputId;
                            let existingStyle = document.getElementById(styleId);
                            if (existingStyle) existingStyle.remove();
                            
                            const style = document.createElement('style');
                            style.id = styleId;
                            style.textContent = `
                                #${inputId}::placeholder { color: ${placeholderColor} !important; opacity: 1 !important; }
                                #${inputId}::-webkit-input-placeholder { color: ${placeholderColor} !important; opacity: 1 !important; }
                                #${inputId}::-moz-placeholder { color: ${placeholderColor} !important; opacity: 1 !important; }
                                #${inputId}:-ms-input-placeholder { color: ${placeholderColor} !important; opacity: 1 !important; }
                                #${inputId}:-moz-placeholder { color: ${placeholderColor} !important; opacity: 1 !important; }
                            `;
                            document.head.appendChild(style);
                        }
                    });
                }
                
                if (theme === 'dark') {
                    // Apply immediately
                    fixAllInputs();
                    
                    // Apply multiple times to catch all inputs
                    setTimeout(fixAllInputs, 50);
                    setTimeout(fixAllInputs, 200);
                    setTimeout(fixAllInputs, 500);
                    setTimeout(fixAllInputs, 1000);
                }
            }
            
            // Apply placeholder fix on init
            applyPlaceholderFix(document.documentElement.getAttribute('data-theme') || 'light');
            
            // Watch for dynamically added inputs and apply placeholder fix
            const inputObserver = new MutationObserver(function(mutations) {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                if (currentTheme === 'dark') {
                    applyPlaceholderFix('dark');
                }
            });
            
            // Start observing when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    inputObserver.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                    // Apply fix after DOM is ready
                    setTimeout(() => applyPlaceholderFix(document.documentElement.getAttribute('data-theme') || 'light'), 100);
                });
            } else {
                inputObserver.observe(document.body, {
                    childList: true,
                    subtree: true
                });
                // Apply fix immediately
                setTimeout(() => applyPlaceholderFix(document.documentElement.getAttribute('data-theme') || 'light'), 100);
            }

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTheme);
            } else {
                initTheme();
            }

            // Attach toggle button event
            document.addEventListener('DOMContentLoaded', function() {
                const themeToggle = document.getElementById('themeToggle');
                if (themeToggle) {
                    themeToggle.addEventListener('click', toggleTheme);
                }
            });
        })();

        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // extra toggle to hide sidebar on xl+ when button clicked
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('adminSidebar');
            if (window.innerWidth >= 1200) {
                sidebar.classList.toggle('d-none');
            }
        });

        // Mobile tables: add column labels for card layout
        function getAdminTableHeaderLabel(th) {
            if (th.getAttribute('data-label')) {
                return th.getAttribute('data-label').trim();
            }

            var clone = th.cloneNode(true);
            clone.querySelectorAll('input, button, .form-check, .dropdown, .btn').forEach(function(el) {
                el.remove();
            });
            var text = clone.textContent.replace(/\s+/g, ' ').trim();
            if (text) {
                return text;
            }

            var classLabelMap = {
                'col-select': '',
                'col-course': 'Course',
                'col-category': 'Category',
                'col-instructor': 'Instructor',
                'col-price': 'Price',
                'col-students': 'Students',
                'col-status': 'Status',
                'col-created': 'Created',
                'col-actions': 'Actions'
            };

            for (var i = 0; i < th.classList.length; i++) {
                var cls = th.classList[i];
                if (Object.prototype.hasOwnProperty.call(classLabelMap, cls)) {
                    return classLabelMap[cls];
                }
            }

            return '';
        }

        function initAdminMobileTables() {
            document.querySelectorAll('main .table-responsive:not(.admin-table-no-mobile-cards)').forEach(function(wrapper) {
                var table = wrapper.firstElementChild;
                if (!table || table.tagName !== 'TABLE') {
                    table = wrapper.querySelector('table');
                }
                if (!table || !table.tHead || !table.tHead.rows.length) {
                    return;
                }

                var headers = Array.from(table.tHead.rows[0].cells).map(getAdminTableHeaderLabel);
                if (!headers.length) {
                    return;
                }

                wrapper.classList.add('admin-table-mobile');

                Array.from(table.tBodies).forEach(function(tbody) {
                    Array.from(tbody.rows).forEach(function(row) {
                        if (row.cells.length !== headers.length) {
                            return;
                        }

                        Array.from(row.cells).forEach(function(cell, index) {
                            var label = headers[index] || '';
                            cell.setAttribute('data-label', label);

                            if (!label) {
                                cell.classList.add('admin-table-mobile-skip-label');
                            } else {
                                cell.classList.remove('admin-table-mobile-skip-label');
                            }
                        });
                    });
                });
            });
        }

        window.initAdminMobileTables = initAdminMobileTables;

        function initAdminFormMobile() {
            var page = document.querySelector('.admin-form-page');
            if (!page) {
                return;
            }

            var links = page.querySelectorAll('[data-form-section-link]');
            var observedSections = [];

            links.forEach(function(link) {
                var sectionId = link.getAttribute('data-form-section-link');
                var sectionEl = document.getElementById(sectionId);
                if (sectionEl) {
                    observedSections.push({ link: link, el: sectionEl });
                }

                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var target = document.getElementById(sectionId);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            if (observedSections.length && window.matchMedia('(max-width: 991.98px)').matches) {
                var navObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (!entry.isIntersecting) {
                            return;
                        }
                        links.forEach(function(navLink) {
                            navLink.classList.toggle(
                                'is-active',
                                navLink.getAttribute('data-form-section-link') === entry.target.id
                            );
                        });
                    });
                }, { rootMargin: '-20% 0px -60% 0px', threshold: 0 });

                observedSections.forEach(function(item) {
                    navObserver.observe(item.el);
                });
            }

            var summary = document.getElementById('mobilePublishSummary');
            if (!summary) {
                return;
            }

            var statusEl = page.querySelector('#status');
            var priceEl = page.querySelector('#price');
            var originalEl = page.querySelector('#original_price');
            var featuredEl = page.querySelector('#is_featured, #featured');
            var freeEl = page.querySelector('#is_free');

            function updatePublishSummary() {
                var statusBadge = summary.querySelector('[data-summary="status"]');
                if (statusBadge && statusEl) {
                    statusBadge.textContent = statusEl.options[statusEl.selectedIndex].text;
                    statusBadge.className = 'badge ' + (
                        statusEl.value === 'published' ? 'bg-success' :
                        statusEl.value === 'archived' ? 'bg-secondary' :
                        'bg-warning text-dark'
                    );
                }

                var priceBadge = summary.querySelector('[data-summary="price"]');
                if (priceBadge) {
                    var isFree = freeEl && freeEl.checked;
                    var original = parseFloat(originalEl && originalEl.value) || 0;
                    var discount = priceEl && priceEl.value !== '' ? parseFloat(priceEl.value) : null;

                    if (isFree || discount === 0) {
                        priceBadge.textContent = 'Free';
                    } else if (discount !== null && !isNaN(discount) && original > discount) {
                        priceBadge.textContent = discount + ' SAR (sale)';
                    } else if (original > 0) {
                        priceBadge.textContent = original + ' SAR';
                    } else {
                        priceBadge.textContent = 'Pricing';
                    }
                }

                var featuredBadge = summary.querySelector('[data-summary="featured"]');
                if (featuredBadge && featuredEl) {
                    featuredBadge.textContent = featuredEl.checked ? 'Featured' : 'Not featured';
                    featuredBadge.className = 'badge ' + (
                        featuredEl.checked ? 'bg-primary' : 'bg-light text-dark border'
                    );
                }
            }

            [statusEl, priceEl, originalEl, featuredEl, freeEl].forEach(function(el) {
                if (!el) {
                    return;
                }
                el.addEventListener('change', updatePublishSummary);
                if (el.type !== 'checkbox') {
                    el.addEventListener('input', updatePublishSummary);
                }
            });

            updatePublishSummary();
        }

        window.initAdminFormMobile = initAdminFormMobile;

        // Close sidebar after navigation on mobile/tablet
        function initAdminSidebarMobile() {
            var sidebarEl = document.getElementById('adminSidebar');
            if (!sidebarEl) {
                return;
            }

            sidebarEl.querySelectorAll('a.list-group-item-action').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth >= 1200) {
                        return;
                    }

                    var instance = bootstrap.Offcanvas.getInstance(sidebarEl);
                    if (instance) {
                        instance.hide();
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initAdminMobileTables();
                initAdminFormMobile();
                initAdminSidebarMobile();
            });
        } else {
            initAdminMobileTables();
            initAdminFormMobile();
            initAdminSidebarMobile();
        }

        // Display session messages as toasts
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>
</body>

</html>
