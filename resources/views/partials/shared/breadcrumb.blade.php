<!-- Breadcrumb Component -->
<nav aria-label="breadcrumb" class="breadcrumb-section py-3 bg-light">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <i class="fa fa-home me-1"></i>Home
                </a>
            </li>

            @if (isset($breadcrumbs))
                @foreach ($breadcrumbs as $breadcrumb)
                    @if ($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $breadcrumb['title'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">
                                {{ $breadcrumb['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            @else
                @if (isset($category))
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories') }}" class="text-decoration-none">Categories</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                    </li>
                @elseif(isset($course))
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories') }}" class="text-decoration-none">Courses</a>
                    </li>
                    @if (isset($course->category))
                        <li class="breadcrumb-item">
                            <a href="{{ route('categories') }}"
                                class="text-decoration-none">{{ \App\Helpers\TranslationHelper::getLocalizedContent($course->category->name, $course->category->name_ar) }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->name }}</li>
                @elseif(isset($blog))
                    <li class="breadcrumb-item">
                        <a href="{{ route('blog.index') }}" class="text-decoration-none">Blog</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $blog->title }}</li>
                @elseif(request()->routeIs('blog'))
                    <li class="breadcrumb-item active" aria-current="page">Blog</li>
                @elseif(request()->routeIs('categories'))
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                @elseif(request()->routeIs('contact'))
                    <li class="breadcrumb-item active" aria-current="page">Contact</li>
                @elseif(request()->routeIs('about'))
                    <li class="breadcrumb-item active" aria-current="page">About</li>
                @elseif(isset($title))
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                @endif
            @endif
        </ol>
    </div>
</nav>

<style>
    .breadcrumb-section {
        border-bottom: 1px solid #e9ecef;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: ">";
        color: #6c757d;
        font-weight: bold;
    }

    .breadcrumb-item a {
        color: #6c757d;
        transition: color 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: #ff6b35;
    }

    .breadcrumb-item.active {
        color: #495057;
        font-weight: 600;
    }
</style>
