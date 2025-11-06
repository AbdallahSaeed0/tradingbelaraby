<!DOCTYPE html>
<html lang="{{ \App\Helpers\TranslationHelper::getCurrentLanguage()->code }}"
    dir="{{ \App\Helpers\TranslationHelper::getCurrentLanguage()->direction }}">

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

    <!-- Admin Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-settings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-analytics.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-content-management.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-quiz.css') }}">

    <!-- Shared Component Styles -->
    <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alerts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/badges.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/language-switcher.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities-extended.css') }}">

    @stack('styles')
</head>

<body class="bg-light">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <button id="sidebarToggle" class="btn btn-outline-secondary me-3 shadow-sm" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                <i class="fa fa-bars"></i>
            </button>
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Visit Site -->
                <li class="nav-item me-3">
                    <a class="nav-link" href="{{ url('/') }}" target="_blank">
                        <i class="fa fa-globe"></i>
                    </a>
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
                    <a class="nav-link position-relative" href="#" id="notifDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
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

                <!-- Language Switcher -->
                @php
                    $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
                    $availableLanguages = \App\Helpers\TranslationHelper::getAvailableLanguages();
                @endphp
                <li class="nav-item dropdown me-3">
                    <a class="nav-link d-flex align-items-center" href="#" id="langDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" title="{{ custom_trans('switch_language', 'admin') }}">
                        <i class="fa fa-globe me-1"></i>
                        <span class="d-none d-md-inline">{{ $currentLanguage->code }}</span>
                        <i class="fa fa-chevron-down ms-1 small"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end language-switcher min-w-200"
                        aria-labelledby="langDropdown">
                        <li class="dropdown-header">
                            <i class="fa fa-language me-2"></i>{{ custom_trans('select_language', 'admin') }}
                        </li>
                        @foreach ($availableLanguages as $language)
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ $currentLanguage->id == $language->id ? 'active bg-primary text-white' : '' }}"
                                    href="{{ route('language.switch', $language->code) }}">
                                    <span class="me-2">
                                        @if ($language->direction == 'rtl')
                                            <i class="fa fa-text-width" title="RTL"></i>
                                        @else
                                            <i class="fa fa-text-width" title="LTR"></i>
                                        @endif
                                    </span>
                                    <span class="flex-grow-1">{{ $language->native_name }}</span>
                                    <small class="text-muted">({{ $language->code }})</small>
                                    @if ($currentLanguage->id == $language->id)
                                        <i class="fa fa-check ms-2"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.languages.index') }}">
                                <i class="fa fa-cog me-2"></i>{{ custom_trans('manage_languages', 'admin') }}
                            </a>
                        </li>
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
                        <span>Hi {{ auth('admin')->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}">My Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
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
    <div class="d-lg-flex">
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

    <script>
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

        // extra toggle to hide sidebar on lg when button clicked
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('adminSidebar');
            if (window.innerWidth >= 992) {
                sidebar.classList.toggle('d-none');
            }
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
