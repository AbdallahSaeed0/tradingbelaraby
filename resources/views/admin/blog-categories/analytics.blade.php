@extends('admin.layout')

@section('title', 'Blog Category Analytics')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-bar me-2"></i>Blog Category Analytics
            </h1>
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Categories
            </a>
        </div>

        @php
            $stats = [
                ['label' => 'Total Categories', 'value' => $totalCategories, 'icon' => 'fa-folder', 'color' => 'primary'],
                ['label' => 'Active Categories', 'value' => $activeCategories, 'icon' => 'fa-check-circle', 'color' => 'success'],
                ['label' => 'Inactive Categories', 'value' => $totalCategories - $activeCategories, 'icon' => 'fa-pause-circle', 'color' => 'warning'],
                ['label' => 'Categories With Blogs', 'value' => $categoriesWithBlogs, 'icon' => 'fa-newspaper', 'color' => 'info'],
            ];
            $maxBlogs = max(1, $topCategories->max('blogs_count') ?? 0);
        @endphp

        <div class="row g-3 mb-4">
            @foreach ($stats as $stat)
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="small text-uppercase fw-semibold text-{{ $stat['color'] }} mb-1">
                                    {{ $stat['label'] }}</div>
                                <div class="h4 mb-0 fw-bold">{{ $stat['value'] }}</div>
                            </div>
                            <i class="fas {{ $stat['icon'] }} fa-2x text-{{ $stat['color'] }} opacity-50"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3">
            <!-- Top Categories -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Top Categories by Blogs</div>
                    <div class="card-body">
                        @forelse ($topCategories as $category)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.blog-categories.show', $category) }}"
                                        class="text-decoration-none">{{ $category->name }}</a>
                                    <span class="badge bg-primary">{{ $category->blogs_count }}</span>
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ ($category->blogs_count / $maxBlogs) * 100 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center mb-0">No categories found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Categories -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Recently Added Categories</div>
                    <div class="card-body">
                        @if ($recentCategories->count() > 0)
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentCategories as $category)
                                            <tr>
                                                <td>{{ $category->name }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($category->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $category->created_at?->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.blog-categories.edit', $category) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">No categories found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
