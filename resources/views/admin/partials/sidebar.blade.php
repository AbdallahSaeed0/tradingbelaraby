<!-- Admin Sidebar -->
<div class="offcanvas offcanvas-start offcanvas-lg-show bg-white offcanvas-sidebar-width" data-bs-backdrop="false"
    tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminSidebarLabel">Admin Panel</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.dashboard') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa fa-chart-line me-2"></i> Dashboard
            </a>

            <!-- Course Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-graduation-cap me-2"></i> Course Management
            </div>
            @if (auth('admin')->user()->hasPermission('manage_courses') ||
                    auth('admin')->user()->hasPermission('manage_own_courses'))
                <a href="{{ route('admin.courses.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <i class="fa fa-book me-2"></i> Courses
                </a>
                <a href="{{ route('admin.bundles.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.bundles.*') ? 'active' : '' }}">
                    <i class="fa fa-box me-2"></i> Bundles
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="fa fa-ticket-alt me-2"></i> Coupons
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_quizzes') ||
                    auth('admin')->user()->hasPermission('manage_own_quizzes'))
                <a href="{{ route('admin.quizzes.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
                    <i class="fa fa-question-circle me-2"></i> Quizzes
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_homework') ||
                    auth('admin')->user()->hasPermission('manage_own_homework'))
                <a href="{{ route('admin.homework.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.homework.*') ? 'active' : '' }}">
                    <i class="fa fa-tasks me-2"></i> Homework
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_live_classes') ||
                    auth('admin')->user()->hasPermission('manage_own_live_classes'))
                <a href="{{ route('admin.live-classes.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.live-classes.*') ? 'active' : '' }}">
                    <i class="fa fa-video me-2"></i> Live Classes
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_enrollments'))
                <a href="{{ route('admin.enrollments.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                    <i class="fa fa-user-graduate me-2"></i> Enrollments
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_questions_answers') ||
                    auth('admin')->user()->hasPermission('manage_own_questions_answers'))
                @php
                    $pendingQACount = \App\Models\QuestionsAnswer::where('status', 'pending')->count();
                @endphp
                <a href="{{ route('admin.questions-answers.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.questions-answers.*') ? 'active' : '' }}">
                    <i class="fa fa-comments me-2"></i> Q&A Management
                    @if ($pendingQACount > 0)
                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingQACount }}</span>
                    @endif
                </a>
            @endif

            <!-- User Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-users me-2"></i> User Management
            </div>
            @if (auth('admin')->user()->hasPermission('manage_admins'))
                <a href="{{ route('admin.admins.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <i class="fa fa-user-shield me-2"></i> Admins
                </a>
                <a href="{{ route('admin.admin-types.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.admin-types.*') ? 'active' : '' }}">
                    <i class="fa fa-user-tag me-2"></i> Admin Types
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_users'))
                <a href="{{ route('admin.users.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa fa-users me-2"></i> Users
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_users'))
                <a href="{{ route('admin.subscribers.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}">
                    <i class="fa fa-user-plus me-2"></i> Subscribers
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_users'))
                <a href="{{ route('admin.traders.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.traders.*') ? 'active' : '' }}">
                    <i class="fa fa-chart-bar me-2"></i> Traders
                </a>
            @endif

            <!-- Content Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-edit me-2"></i> Content Management
            </div>
            @if (auth('admin')->user()->hasPermission('manage_categories'))
                <a href="{{ route('admin.categories.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fa fa-list me-2"></i> Categories
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_blogs'))
                <a href="{{ route('admin.blog-categories.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">
                    <i class="fa fa-tags me-2"></i> Blog Categories
                </a>
                <a href="{{ route('admin.blogs.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                    <i class="fa fa-blog me-2"></i> Blogs
                </a>
            @endif

            <!-- Translation Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-language me-2"></i> Translation Management
            </div>
            @if (auth('admin')->user()->hasPermission('manage_languages'))
                <a href="{{ route('admin.languages.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                    <i class="fa fa-flag me-2"></i> Languages
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_translations'))
                <a href="{{ route('admin.translations.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.translations.*') ? 'active' : '' }}">
                    <i class="fa fa-language me-2"></i> Translations
                </a>
                <a href="{{ route('admin.debug-translations') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.debug-translations') ? 'active' : '' }}">
                    <i class="fa fa-bug me-2"></i> Debug Translations
                </a>
            @endif

            <!-- Settings Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-cog me-2"></i> Settings
            </div>
            <a href="{{ route('admin.settings.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="fa fa-sliders-h me-2"></i> General Settings
            </a>
        </div>
    </div>
</div>
