@php
    $statusClass = $category->status === 'active' ? 'bg-success' : 'bg-secondary';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $category->id . ', \'' . $category->status . '\', [{ value: \'active\', label: \'Active\' }, { value: \'inactive\', label: \'Inactive\' }], \'' . route('admin.blog-categories.update_status', $category->id) . '\')">' . ucfirst($category->status) . '</button>';

    $chips = [
        '<span class="badge bg-primary">' . $category->blogs_count . ' blogs</span>',
        '<span class="badge bg-light text-dark">Order ' . $category->order . '</span>',
    ];
    if ($category->published_blogs_count > 0) {
        $chips[] = '<span class="badge bg-success">' . $category->published_blogs_count . ' published</span>';
    }

    $metaHtml = $category->description
        ? e(Str::limit($category->description, 80))
        : '<span class="text-muted">No description</span>';

    $actionsHtml = '<a href="' . route('admin.blog-categories.show', $category) . '" class="btn btn-sm btn-outline-info" title="View"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.blog-categories.edit', $category) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>';
    if (!$category->hasBlogs()) {
        $actionsHtml .= '<button type="button" onclick="deleteCategory(' . $category->id . ')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>';
    }
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $category->id,
    'checkboxClass' => 'category-checkbox',
    'checkboxValue' => $category->id,
    'checkboxLabel' => 'Select ' . $category->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.blog-categories.show', $category),
    'imageUrl' => $category->image ? $category->image_url : null,
    'iconClass' => $category->image ? null : 'fa-tag',
    'placeholder' => strtoupper(substr($category->name, 0, 2)),
    'title' => $category->name,
    'subtitle' => $category->slug,
    'chips' => $chips,
    'metaHtml' => $metaHtml,
    'footerPrimary' => $category->created_at->format('M d, Y'),
    'footerSecondary' => null,
    'actionsHtml' => $actionsHtml,
])
