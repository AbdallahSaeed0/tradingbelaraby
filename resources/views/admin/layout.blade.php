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
    <link rel="stylesheet" href="{{ asset('css/admin/admin-lang-tabs-mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-settings-mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-list-mobile.css') }}">
    @if (request()->routeIs('admin.courses.show'))
        <link rel="stylesheet" href="{{ asset('css/admin/admin-course-detail.css') }}">
    @endif
</head>

@php
    $isAdminSettingsPage = request()->routeIs('admin.settings.*', 'admin.partner-logos.*');
    $isAdminListPage = request()->routeIs(
        'admin.bundles.*',
        'admin.coupons.*',
        'admin.quizzes.*',
        'admin.homework.*',
        'admin.live-classes.*',
        'admin.enrollments.*',
        'admin.questions-answers.*',
        'admin.courses.show',
        'admin.admins.*',
        'admin.admin-types.*',
        'admin.users.*',
        'admin.subscribers.*',
        'admin.traders.*',
        'admin.categories.*',
        'admin.blog-categories.*',
        'admin.blogs.*'
    );
    $isAdminDetailPage = request()->routeIs(
        'admin.courses.show',
        'admin.admins.show',
        'admin.admin-types.show',
        'admin.users.show',
        'admin.subscribers.show',
        'admin.traders.show',
        'admin.coupons.show',
        'admin.questions-answers.show',
        'admin.blog-categories.show',
        'admin.blogs.show'
    );
    $isAdminCourseDetailPage = request()->routeIs('admin.courses.show');
@endphp

<body class="bg-light @yield('body_class'){{ $isAdminSettingsPage ? ' admin-settings-page' : '' }}{{ $isAdminListPage ? ' admin-list-page' : '' }}{{ $isAdminDetailPage ? ' admin-detail-page' : '' }}{{ $isAdminCourseDetailPage ? ' admin-course-detail-page' : '' }}">
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

        // Sidebar: icon-only collapse on desktop, offcanvas drawer on mobile
        (function () {
            var SIDEBAR_KEY = 'adminSidebarCollapsed';
            var sidebar   = document.getElementById('adminSidebar');
            var toggleBtn = document.getElementById('sidebarToggle');
            if (!sidebar || !toggleBtn) { return; }

            function isDesktop() { return window.innerWidth >= 1200; }

            /* ── Bootstrap offcanvas attribute management ── */
            function detachBootstrapOffcanvas() {
                toggleBtn.removeAttribute('data-bs-toggle');
                toggleBtn.removeAttribute('data-bs-target');
                toggleBtn.removeAttribute('aria-controls');
            }
            function attachBootstrapOffcanvas() {
                toggleBtn.setAttribute('data-bs-toggle', 'offcanvas');
                toggleBtn.setAttribute('data-bs-target', '#adminSidebar');
                toggleBtn.setAttribute('aria-controls', 'adminSidebar');
            }

            /* ── Bootstrap tooltip management ── */
            function initTooltips() {
                if (!window.bootstrap || !bootstrap.Tooltip) { return; }
                sidebar.querySelectorAll('.list-group-item-action[title]').forEach(function (el) {
                    // Dispose existing before re-creating to avoid duplicates
                    var existing = bootstrap.Tooltip.getInstance(el);
                    if (existing) { existing.dispose(); }
                    bootstrap.Tooltip.getOrCreateInstance(el, {
                        placement: 'right',
                        trigger: 'hover',
                        container: 'body',
                        boundary: 'clippingParents'
                    });
                });
            }
            function destroyTooltips() {
                if (!window.bootstrap || !bootstrap.Tooltip) { return; }
                sidebar.querySelectorAll('.list-group-item-action').forEach(function (el) {
                    var tt = bootstrap.Tooltip.getInstance(el);
                    if (tt) { tt.dispose(); }
                });
            }

            /* ── Apply / remove collapsed state ── */
            function collapse(animate) {
                if (!animate) {
                    sidebar.style.transition = 'none';
                    void sidebar.offsetWidth;
                }
                sidebar.classList.add('sidebar-collapsed');
                if (!animate) {
                    void sidebar.offsetWidth;
                    sidebar.style.transition = '';
                }
                // Tooltips become active only after the transition finishes
                setTimeout(initTooltips, animate ? 300 : 0);
            }
            function expand() {
                destroyTooltips();
                sidebar.classList.remove('sidebar-collapsed');
            }

            /* ── Initial setup ── */
            if (isDesktop()) {
                detachBootstrapOffcanvas();
                if (localStorage.getItem(SIDEBAR_KEY) === '1') {
                    collapse(false); // no animation on page load
                }
            }

            /* ── Resize handler ── */
            window.addEventListener('resize', function () {
                if (isDesktop()) {
                    detachBootstrapOffcanvas();
                } else {
                    attachBootstrapOffcanvas();
                    expand(); // clear collapsed state on mobile
                }
            });

            /* ── Toggle button ── */
            toggleBtn.addEventListener('click', function () {
                if (!isDesktop()) { return; }
                var willCollapse = !sidebar.classList.contains('sidebar-collapsed');
                if (willCollapse) {
                    collapse(true);
                } else {
                    expand();
                }
                localStorage.setItem(SIDEBAR_KEY, willCollapse ? '1' : '0');
            });
        })();

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

        function initAdminSettingsMobile() {
            if (!document.body.classList.contains('admin-settings-page')) {
                return;
            }

            initAdminMobileTables();
            initSettingsSectionNav();
            initSettingsTableMobile();
            initSettingsFilterCards();
            initSettingsMobileToolbar();
        }

        function initSettingsSectionNav() {
            var page = document.body;
            var existingNav = document.querySelector('.admin-settings-section-nav');
            var sectionNodes = Array.from(document.querySelectorAll('[data-settings-section]'));

            if (!sectionNodes.length) {
                sectionNodes = Array.from(document.querySelectorAll('[id^="settings-section-"]'));
            }

            if (!sectionNodes.length) {
                return;
            }

            var links = [];

            if (existingNav) {
                links = Array.from(existingNav.querySelectorAll('[data-settings-section-link]'));
            } else if (window.matchMedia('(max-width: 991.98px)').matches) {
                var nav = document.createElement('nav');
                nav.className = 'admin-settings-section-nav d-lg-none';
                nav.setAttribute('aria-label', 'Settings sections');

                sectionNodes.forEach(function(section) {
                    var label = section.getAttribute('data-settings-section') || section.getAttribute('data-section-label');
                    if (!label) {
                        var heading = section.querySelector('h5, h6, .card-title');
                        label = heading ? heading.textContent.trim() : section.id.replace(/^settings-section-/, '');
                    }

                    var link = document.createElement('a');
                    link.href = '#' + section.id;
                    link.className = 'admin-settings-section-nav__link';
                    link.setAttribute('data-settings-section-link', section.id);
                    link.textContent = label;
                    nav.appendChild(link);
                    links.push(link);
                });

                var anchor = document.querySelector('.page-title-box');
                if (anchor && anchor.parentNode) {
                    anchor.parentNode.insertBefore(nav, anchor.nextSibling);
                } else {
                    var container = document.querySelector('.container-fluid');
                    if (container) {
                        container.insertBefore(nav, container.firstChild);
                    }
                }
            } else {
                links = Array.from(document.querySelectorAll('[data-settings-section-link]'));
            }

            links.forEach(function(link) {
                var sectionId = link.getAttribute('data-settings-section-link');
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var target = document.getElementById(sectionId);
                    if (target) {
                        if (target.classList.contains('tab-pane')) {
                            var tabTrigger = document.querySelector('[data-bs-target="#' + sectionId + '"]');
                            if (tabTrigger && window.bootstrap && bootstrap.Tab) {
                                bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                            }
                        }
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            if (links.length && window.matchMedia('(max-width: 991.98px)').matches) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (!entry.isIntersecting) {
                            return;
                        }
                        links.forEach(function(navLink) {
                            navLink.classList.toggle(
                                'is-active',
                                navLink.getAttribute('data-settings-section-link') === entry.target.id
                            );
                        });
                    });
                }, { rootMargin: '-20% 0px -60% 0px', threshold: 0 });

                sectionNodes.forEach(function(section) {
                    observer.observe(section);
                });
            }
        }

        function initSettingsMobileToolbar() {
            if (window.innerWidth >= 992) {
                return;
            }

            if (document.getElementById('settingsMobileToolbar')) {
                document.body.classList.add('settings-has-mobile-toolbar');
                return;
            }

            var pageForms = Array.from(document.querySelectorAll('main form, main .container-fluid form')).filter(function(form) {
                if (form.closest('.modal, .admin-top-navbar, nav.navbar, .navbar, .dropdown-menu')) {
                    return false;
                }
                if (form.dataset.settingsMobileToolbar === 'skip') {
                    return false;
                }
                if ((form.method || '').toLowerCase() === 'get') {
                    return false;
                }
                return !!form.querySelector('button[type="submit"], input[type="submit"]');
            });

            var primaryForm = document.getElementById('mainContentForm')
                || document.getElementById('paymentSettingsForm')
                || document.getElementById('verificationSettingsForm')
                || document.getElementById('aboutUniversityForm')
                || document.getElementById('contactSettingsForm')
                || document.getElementById('termsConditionsForm')
                || document.getElementById('aboutUsForm')
                || document.getElementById('academyPolicyForm');

            if (primaryForm) {
                pageForms = [primaryForm];
            }

            var toolbar = document.createElement('div');
            toolbar.className = 'admin-settings-mobile-toolbar d-lg-none';
            toolbar.id = 'settingsMobileToolbar';

            var backShell = document.querySelector('.container-fluid[data-settings-back-url]');
            var backUrl = backShell
                ? backShell.getAttribute('data-settings-back-url')
                : @json(route('admin.settings.index'));
            var backLabel = backShell
                ? (backShell.getAttribute('data-settings-back-label') || 'Settings')
                : 'Settings';

            var backBtn = document.createElement('a');
            backBtn.href = backUrl;
            backBtn.className = 'btn btn-outline-secondary btn-sm';
            backBtn.innerHTML = '<i class="fa fa-arrow-left me-1"></i>' + backLabel;

            toolbar.appendChild(backBtn);

            if (pageForms.length === 1) {
                var form = pageForms[0];
                if (!form.id) {
                    form.id = 'settingsPrimaryForm';
                }

                var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                var label = submitBtn ? submitBtn.textContent.trim() : 'Save';
                var actionWrap = submitBtn ? submitBtn.closest('.d-flex, .d-grid, .text-end, .mt-3, .mb-3') : null;
                if (actionWrap) {
                    actionWrap.classList.add('admin-settings-inline-actions');
                }

                var saveBtn = document.createElement('button');
                saveBtn.type = 'submit';
                saveBtn.setAttribute('form', form.id);
                saveBtn.className = 'btn btn-primary btn-sm admin-settings-mobile-toolbar__save';
                saveBtn.innerHTML = '<i class="fa fa-save me-1"></i>' + label;
                toolbar.appendChild(saveBtn);
            }

            var topBtn = document.createElement('button');
            topBtn.type = 'button';
            topBtn.className = 'btn btn-outline-secondary btn-sm admin-settings-scroll-top';
            topBtn.setAttribute('aria-label', 'Scroll to top');
            topBtn.setAttribute('title', 'Scroll to top');
            topBtn.innerHTML = '<i class="fa fa-arrow-up"></i>';
            topBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            toolbar.appendChild(topBtn);

            document.body.appendChild(toolbar);
            document.body.classList.add('settings-has-mobile-toolbar');
        }

        window.initAdminSettingsMobile = initAdminSettingsMobile;

        function initSettingsTableMobile() {
            if (!document.body.classList.contains('admin-settings-page')) {
                return;
            }

            var hideLabelPatterns = [
                /\(ar\)/i,
                /\(arabic\)/i,
                /welcome text/i,
                /subtitle/i,
                /^content$/i,
                /^description$/i,
                /^order$/i,
                /^answer$/i,
                /^created/i,
                /^updated/i
            ];

            var settingsCellClasses = [
                'admin-settings-col-hide',
                'admin-settings-col-check',
                'admin-settings-col-order',
                'admin-settings-col-media',
                'admin-settings-col-title',
                'admin-settings-col-meta',
                'admin-settings-col-status',
                'admin-settings-col-toggle',
                'admin-settings-col-actions'
            ];

            document.querySelectorAll('main .admin-table-mobile table').forEach(function(table) {
                var headers = table.tHead && table.tHead.rows.length
                    ? Array.from(table.tHead.rows[0].cells).map(getAdminTableHeaderLabel)
                    : [];

                var titleAssigned = false;

                Array.from(table.tBodies).forEach(function(tbody) {
                    Array.from(tbody.rows).forEach(function(row) {
                        if (row.cells.length <= 1) {
                            return;
                        }

                        row.classList.add('admin-settings-table-row');

                        Array.from(row.cells).forEach(function(cell) {
                            settingsCellClasses.forEach(function(cls) {
                                cell.classList.remove(cls);
                            });
                        });

                        Array.from(row.cells).forEach(function(cell, index) {
                            var label = (cell.getAttribute('data-label') || headers[index] || '').trim();

                            if (index === 0 && cell.querySelector('.form-check-input[type="checkbox"]')) {
                                cell.classList.add('admin-settings-col-check');
                            }

                            if (/^order$/i.test(label)) {
                                cell.classList.add('admin-settings-col-order');
                            }

                            if (cell.querySelector('img') || cell.querySelector('.slider-thumb') || cell.querySelector('.logo-preview-max')) {
                                cell.classList.add('admin-settings-col-media');
                            }

                            if (label && hideLabelPatterns.some(function(pattern) {
                                return pattern.test(label);
                            })) {
                                cell.classList.add('admin-settings-col-hide');
                            }

                            if (cell.querySelector('.btn-group') || /actions?/i.test(label)) {
                                cell.classList.add('admin-settings-col-actions');
                            }

                            if (!titleAssigned && /^title$/i.test(label) && !cell.classList.contains('admin-settings-col-hide')) {
                                cell.classList.add('admin-settings-col-title');
                                titleAssigned = true;
                            } else if (!titleAssigned && /^question$/i.test(label) && !cell.classList.contains('admin-settings-col-hide')) {
                                cell.classList.add('admin-settings-col-title');
                                titleAssigned = true;
                            } else if (!titleAssigned && (/^name$/i.test(label) || /^email$/i.test(label)) && !cell.classList.contains('admin-settings-col-hide')) {
                                cell.classList.add('admin-settings-col-title');
                                titleAssigned = true;
                            }

                            if (/^status$/i.test(label) || (cell.querySelector('.status-badge') && !cell.querySelector('.form-switch'))) {
                                cell.classList.add('admin-settings-col-status');
                            } else if (/expanded/i.test(label) || (cell.querySelector('.form-switch') && !cell.classList.contains('admin-settings-col-status'))) {
                                cell.classList.add('admin-settings-col-toggle');
                            } else if (cell.querySelector('.form-switch')) {
                                cell.classList.add('admin-settings-col-status');
                            }

                            if (/^rating$/i.test(label) || /^category$/i.test(label) || /^type$/i.test(label)) {
                                cell.classList.add('admin-settings-col-meta');
                            }
                        });

                        titleAssigned = false;
                    });
                });
            });
        }

        window.initSettingsTableMobile = initSettingsTableMobile;

        function initSettingsFilterCards() {
            if (!document.body.classList.contains('admin-settings-page') || window.innerWidth >= 992) {
                return;
            }

            function wireFilterCard(card) {
                if (!card || card.dataset.settingsFilterWired === '1') {
                    return;
                }

                var body = card.querySelector('.card-body');
                if (!body) {
                    return;
                }

                card.dataset.settingsFilterWired = '1';
                card.classList.add('admin-settings-filter-card');

                if (card.querySelector('.admin-settings-filter-card__toggle')) {
                    return;
                }

                var toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.className = 'admin-settings-filter-card__toggle';
                toggle.setAttribute('aria-expanded', card.classList.contains('is-open') ? 'true' : 'false');
                toggle.innerHTML = '<span><i class="fa fa-filter me-2"></i>Search &amp; Filters</span><i class="fa fa-chevron-down admin-settings-filter-card__chevron"></i>';

                card.insertBefore(toggle, body);
                body.classList.add('admin-settings-filter-card__body');

                toggle.addEventListener('click', function() {
                    var open = card.classList.toggle('is-open');
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
            }

            document.querySelectorAll('#filterForm').forEach(function(form) {
                wireFilterCard(form.closest('.card'));
            });

            document.querySelectorAll('.admin-settings-filter-card').forEach(function(card) {
                wireFilterCard(card);
            });
        }

        function initAdminListMobile() {
            if (!document.body.classList.contains('admin-list-page')) {
                return;
            }

            if (window.innerWidth >= 992 || document.getElementById('listMobileToolbar')) {
                if (document.getElementById('listMobileToolbar')) {
                    document.body.classList.add('list-has-mobile-toolbar');
                }
                return;
            }

            var shell = document.querySelector('.container-fluid[data-mobile-back-url]');
            var backUrl = shell ? shell.getAttribute('data-mobile-back-url') : null;
            var backLabel = shell ? shell.getAttribute('data-mobile-back-label') : 'Back';
            var isListIndex = !!document.querySelector('.admin-mobile-list') && !backUrl;
            var isDetailPage = !!document.querySelector('.admin-detail-page');
            var pageForms = [];

            if (!isListIndex) {
                pageForms = Array.from(document.querySelectorAll('.container-fluid form')).filter(function(form) {
                    if (form.closest('.modal')) {
                        return false;
                    }
                    if (form.closest('.admin-mobile-list')) {
                        return false;
                    }
                    if (form.id === 'filterForm' || form.dataset.settingsMobileToolbar === 'skip') {
                        return false;
                    }
                    if (form.classList.contains('d-inline')) {
                        return false;
                    }
                    if ((form.method || 'get').toLowerCase() === 'get') {
                        return false;
                    }
                    return !!form.querySelector('button[type="submit"], input[type="submit"]');
                });
            }

            var primaryForm = document.getElementById('bundleForm')
                || document.getElementById('couponForm')
                || document.getElementById('quizForm')
                || document.getElementById('homeworkForm')
                || document.getElementById('liveClassForm');

            if (!primaryForm && shell) {
                primaryForm = shell.querySelector('form[id]:not(#filterForm)');
            }

            if (primaryForm) {
                pageForms = [primaryForm];
            } else if (isListIndex || isDetailPage) {
                pageForms = [];
            }

            var toolbar = document.createElement('div');
            toolbar.className = 'admin-list-mobile-toolbar d-lg-none';
            toolbar.id = 'listMobileToolbar';

            if (backUrl) {
                var backBtn = document.createElement('a');
                backBtn.href = backUrl;
                backBtn.className = 'btn btn-outline-secondary btn-sm';
                backBtn.innerHTML = '<i class="fa fa-arrow-left me-1"></i>' + backLabel;
                toolbar.appendChild(backBtn);
            }

            if (pageForms.length === 1) {
                var form = pageForms[0];
                if (!form.id) {
                    form.id = 'listPrimaryForm';
                }

                var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                var label = submitBtn ? submitBtn.textContent.trim() : 'Save';
                var actionWrap = submitBtn ? submitBtn.closest('.d-flex, .d-grid, .text-end, .mt-3, .mb-3, .card-footer') : null;
                if (actionWrap) {
                    actionWrap.classList.add('admin-form-inline-actions');
                }

                var saveBtn = document.createElement('button');
                saveBtn.type = 'submit';
                saveBtn.setAttribute('form', form.id);
                saveBtn.className = 'btn btn-primary btn-sm';
                saveBtn.innerHTML = '<i class="fa fa-save me-1"></i>' + label;
                toolbar.appendChild(saveBtn);
            } else {
                var header = document.querySelector('.admin-list-header-actions')
                    || document.querySelector('.admin-detail-header-actions')
                    || document.querySelector('.row.mb-4 .d-flex.justify-content-between > div:last-child')
                    || document.querySelector('.page-header .col-auto');
                var headerAction = header ? header.querySelector('.btn-primary, .btn-warning, .btn-success') : null;

                if (!backUrl && isDetailPage) {
                    var detailBack = document.querySelector('.admin-detail-back-url');
                    if (detailBack) {
                        backUrl = detailBack.getAttribute('href');
                        backLabel = detailBack.textContent.trim() || 'Back';
                    }
                }

                if (backUrl && !toolbar.querySelector('a.btn-outline-secondary')) {
                    var detailBackBtn = document.createElement('a');
                    detailBackBtn.href = backUrl;
                    detailBackBtn.className = 'btn btn-outline-secondary btn-sm';
                    detailBackBtn.innerHTML = '<i class="fa fa-arrow-left me-1"></i>' + backLabel;
                    toolbar.insertBefore(detailBackBtn, toolbar.firstChild);
                }

                if (headerAction) {
                    header.classList.add('admin-list-header-actions');
                    if (headerAction.hasAttribute('data-bs-toggle') && headerAction.getAttribute('data-bs-target')) {
                        toolbar.appendChild(headerAction.cloneNode(true));
                    } else if (headerAction.tagName === 'A') {
                        var linkClone = headerAction.cloneNode(true);
                        linkClone.classList.add('btn-sm');
                        toolbar.appendChild(linkClone);
                    }
                }
            }

            if (toolbar.children.length > 0) {
                document.body.appendChild(toolbar);
                document.body.classList.add('list-has-mobile-toolbar');
            }
        }

        window.initAdminListMobile = initAdminListMobile;

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
                initAdminSettingsMobile();
                initAdminListMobile();
                initAdminSidebarMobile();
            });
        } else {
            initAdminMobileTables();
            initAdminFormMobile();
            initAdminSettingsMobile();
            initAdminListMobile();
            initAdminSidebarMobile();
        }

        var settingsTableMobileResizeTimer;
        window.addEventListener('resize', function() {
            if (!document.body.classList.contains('admin-settings-page')) {
                return;
            }
            clearTimeout(settingsTableMobileResizeTimer);
            settingsTableMobileResizeTimer = setTimeout(function() {
                initAdminMobileTables();
                initSettingsTableMobile();
            }, 150);
        });

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
