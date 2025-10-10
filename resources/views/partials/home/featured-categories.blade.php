<!-- Featured Categories Section -->
<section class="featured-categories-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="featured-categories-title mb-0">Featured Categories</h2>
                <p class="text-muted mt-2">Explore our most popular course categories</p>
            </div>
        </div>

        @if ($featuredCategories->count() > 0)
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4 justify-content-center">
                @foreach ($featuredCategories as $category)
                    <div class="col">
                        <div class="category-card h-100">
                            <div class="category-img-wrap">
                                @if ($category->image)
                                    <img src="{{ $category->image_url }}"
                                        alt="{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}"
                                        class="category-img">
                                @else
                                    <div class="category-placeholder">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="category-label">
                                <i class="fas fa-folder me-2"></i>
                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                            </div>
                            <div class="category-hover-text">{{ $category->courses_count }} courses</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Fallback categories if no featured categories exist -->
            @php
                $fallbackCategories = [
                    ['icon' => 'fa-briefcase', 'name' => 'Business', 'courses' => 6],
                    ['icon' => 'fa-code', 'name' => 'Development', 'courses' => 8],
                    ['icon' => 'fa-laptop-code', 'name' => 'Programming', 'courses' => 5],
                    ['icon' => 'fa-heart-pulse', 'name' => 'Health & Fitness', 'courses' => 3],
                    ['icon' => 'fa-bullhorn', 'name' => 'Marketing', 'courses' => 7],
                    ['icon' => 'fa-flask', 'name' => 'Science', 'courses' => 4],
                ];
            @endphp
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4 justify-content-center">
                @foreach ($fallbackCategories as $category)
                    <div class="col">
                        <div class="category-card h-100">
                            <div class="category-img-wrap">
                                <div class="category-placeholder">
                                    <i class="fas {{ $category['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="category-label">
                                <i class="fas {{ $category['icon'] }} me-2"></i>
                                {{ $category['name'] }}
                            </div>
                            <div class="category-hover-text">{{ $category['courses'] }} courses</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- View All Categories Button -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ route('categories.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-th-large me-2"></i>View All Categories
                </a>
            </div>
        </div>
    </div>
</section>
