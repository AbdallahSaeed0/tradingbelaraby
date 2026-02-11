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
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                title="Dashboard">
                <i class="fa fa-chart-line"></i>
                <span class="sidebar-nav-text">Dashboard</span>
            </a>

            <!-- Course Management Section -->
            <div class="list-group-item sidebar-section-header bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-graduation-cap"></i>
                <span class="sidebar-nav-text">Course Management</span>
            </div>
            @if (auth('admin')->user()->hasPermission('manage_courses') ||
                    auth('admin')->user()->hasPermission('manage_own_courses'))
                <a href="{{ route('admin.courses.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"
                    title="Courses">
                    <i class="fa fa-book"></i>
                    <span class="sidebar-nav-text">Courses</span>
                </a>
                <a href="{{ route('admin.bundles.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.bundles.*') ? 'active' : '' }}"
                    title="Bundles">
                    <i class="fa fa-box"></i>
                    <span class="sidebar-nav-text">Bundles</span>
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
                    title="Coupons">
                    <i class="fa fa-ticket-alt"></i>
                    <span class="sidebar-nav-text">Coupons</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_quizzes') ||
                    auth('admin')->user()->hasPermission('manage_own_quizzes'))
                <a href="{{ route('admin.quizzes.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}"
                    title="Quizzes">
                    <i class="fa fa-question-circle"></i>
                    <span class="sidebar-nav-text">Quizzes</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_homework') ||
                    auth('admin')->user()->hasPermission('manage_own_homework'))
                <a href="{{ route('admin.homework.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.homework.*') ? 'active' : '' }}"
                    title="Homework">
                    <i class="fa fa-tasks"></i>
                    <span class="sidebar-nav-text">Homework</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_live_classes') ||
                    auth('admin')->user()->hasPermission('manage_own_live_classes'))
                <a href="{{ route('admin.live-classes.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.live-classes.*') ? 'active' : '' }}"
                    title="Live Classes">
                    <i class="fa fa-video"></i>
                    <span class="sidebar-nav-text">Live Classes</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_enrollments'))
                <a href="{{ route('admin.enrollments.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}"
                    title="Enrollments">
                    <i class="fa fa-user-graduate"></i>
                    <span class="sidebar-nav-text">Enrollments</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_questions_answers') ||
                    auth('admin')->user()->hasPermission('manage_own_questions_answers'))
                @php
                    $pendingQACount = \App\Models\QuestionsAnswer::where('status', 'pending')->count();
                @endphp
                <a href="{{ route('admin.questions-answers.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.questions-answers.*') ? 'active' : '' }}"
                    title="Q&A Management">
                    <i class="fa fa-comments"></i>
                    <span class="sidebar-nav-text">Q&A Management</span>
                    @if ($pendingQACount > 0)
                        <span class="badge bg-warning">{{ $pendingQACount }}</span>
                    @endif
                </a>
            @endif

            <!-- User Management Section -->
            <div class="list-group-item sidebar-section-header bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-users"></i>
                <span class="sidebar-nav-text">User Management</span>
            </div>
            @if (auth('admin')->user()->hasPermission('manage_admins'))
                <a href="{{ route('admin.admins.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}"
                    title="Admins">
                    <i class="fa fa-user-shield"></i>
                    <span class="sidebar-nav-text">Admins</span>
                </a>
                <a href="{{ route('admin.admin-types.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.admin-types.*') ? 'active' : '' }}"
                    title="Admin Types">
                    <i class="fa fa-user-tag"></i>
                    <span class="sidebar-nav-text">Admin Types</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_users'))
                <a href="{{ route('admin.users.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    title="Users">
                    <i class="fa fa-users"></i>
                    <span class="sidebar-nav-text">Users</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_users'))
                <a href="{{ route('admin.subscribers.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}"
                    title="Subscribers">
                    <i class="fa fa-user-plus"></i>
                    <span class="sidebar-nav-text">Subscribers</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_users'))
                <a href="{{ route('admin.traders.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.traders.*') ? 'active' : '' }}"
                    title="Traders">
                    <i class="fa fa-chart-bar"></i>
                    <span class="sidebar-nav-text">Traders</span>
                </a>
            @endif

            <!-- Content Management Section -->
            <div class="list-group-item sidebar-section-header bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-edit"></i>
                <span class="sidebar-nav-text">Content Management</span>
            </div>
            @if (auth('admin')->user()->hasPermission('manage_categories'))
                <a href="{{ route('admin.categories.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                    title="Categories">
                    <i class="fa fa-list"></i>
                    <span class="sidebar-nav-text">Categories</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_blogs'))
                <a href="{{ route('admin.blog-categories.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}"
                    title="Blog Categories">
                    <i class="fa fa-tags"></i>
                    <span class="sidebar-nav-text">Blog Categories</span>
                </a>
                <a href="{{ route('admin.blogs.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}"
                    title="Blogs">
                    <i class="fa fa-blog"></i>
                    <span class="sidebar-nav-text">Blogs</span>
                </a>
            @endif

            <!-- Translation Management Section -->
            <div class="list-group-item sidebar-section-header bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-language"></i>
                <span class="sidebar-nav-text">Translation Management</span>
            </div>
            @if (auth('admin')->user()->hasPermission('manage_languages'))
                <a href="{{ route('admin.languages.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}"
                    title="Languages">
                    <i class="fa fa-flag"></i>
                    <span class="sidebar-nav-text">Languages</span>
                </a>
            @endif
            @if (auth('admin')->user()->hasPermission('manage_translations'))
                <a href="{{ route('admin.translations.index') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.translations.*') ? 'active' : '' }}"
                    title="Translations">
                    <i class="fa fa-language"></i>
                    <span class="sidebar-nav-text">Translations</span>
                </a>
                <a href="{{ route('admin.debug-translations') }}"
                    class="list-group-item list-group-item-action {{ request()->routeIs('admin.debug-translations') ? 'active' : '' }}"
                    title="Debug Translations">
                    <i class="fa fa-bug"></i>
                    <span class="sidebar-nav-text">Debug Translations</span>
                </a>
            @endif

            <!-- Settings Section -->
            <div class="list-group-item sidebar-section-header bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-cog"></i>
                <span class="sidebar-nav-text">Settings</span>
            </div>
            <a href="{{ route('admin.settings.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"
                title="General Settings">
                <i class="fa fa-sliders-h"></i>
                <span class="sidebar-nav-text">General Settings</span>
            </a>
        </div>
    </div>
</div>
