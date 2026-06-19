@php
    $statusClasses = [
        'published' => 'bg-success',
        'draft' => 'bg-warning text-dark',
        'scheduled' => 'bg-info',
        'archived' => 'bg-secondary',
    ];
    $statusClass = $statusClasses[$blog->status] ?? 'bg-secondary';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showBlogStatusModal(' . $blog->id . ', \'' . $blog->status . '\')">' . ucfirst($blog->status) . '</button>';

    $chips = [];
    if ($blog->category) {
        $chips[] = '<span class="badge bg-info">' . e($blog->category->name) . '</span>';
    }
    if ($blog->is_featured) {
        $chips[] = '<span class="badge bg-warning text-dark">Featured</span>';
    }
    $chips[] = '<span class="badge bg-primary">' . $blog->views_count . ' views</span>';

    $stats = [
        ['icon' => 'fa-user', 'text' => e($blog->author_name ?: 'Admin')],
    ];

    $subtitle = Str::limit($blog->excerpt ?: $blog->description, 60);

    $actionsHtml = '<a href="' . route('admin.blogs.show', $blog) . '" class="btn btn-sm btn-outline-info" title="View"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.blogs.edit', $blog) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<button type="button" onclick="toggleFeatured(' . $blog->id . ')" class="btn btn-sm btn-outline-' . ($blog->is_featured ? 'success' : 'secondary') . '" title="Feature"><i class="fa fa-star"></i></button>'
        . '<button type="button" onclick="deleteBlog(' . $blog->id . ')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $blog->id,
    'checkboxClass' => 'blog-checkbox',
    'checkboxValue' => $blog->id,
    'checkboxLabel' => 'Select ' . $blog->title,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.blogs.show', $blog),
    'imageUrl' => $blog->image ? $blog->image_url : null,
    'iconClass' => $blog->image ? null : 'fa-file-alt',
    'placeholder' => strtoupper(substr($blog->title, 0, 2)),
    'title' => $blog->title,
    'subtitle' => $subtitle,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $blog->created_at->format('M d, Y'),
    'footerSecondary' => null,
    'actionsHtml' => $actionsHtml,
])
