@php
    $chips = [
        $cat->is_featured
            ? '<span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>Featured</span>'
            : '<span class="badge bg-secondary">Regular</span>',
        '<span class="badge bg-info"><i class="fas fa-graduation-cap me-1"></i>' . ($cat->courses_count ?? 0) . ' courses</span>',
    ];

    $metaHtml = $cat->description
        ? e(Str::limit($cat->description, 80))
        : '<span class="text-muted">No description</span>';

    $actionsHtml = '<a href="' . route('admin.categories.edit', $cat) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<form action="' . route('admin.categories.destroy', $cat) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this category?\');">'
        . '<input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE">'
        . '<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button></form>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $cat->id,
    'checkboxClass' => 'category-checkbox',
    'checkboxValue' => $cat->id,
    'checkboxLabel' => 'Select ' . $cat->name,
    'heroUrl' => route('admin.categories.edit', $cat),
    'imageUrl' => $cat->image ? $cat->image_url : null,
    'iconClass' => $cat->image ? null : 'fa-folder',
    'placeholder' => strtoupper(substr($cat->name, 0, 2)),
    'title' => $cat->name,
    'subtitle' => $cat->slug,
    'chips' => $chips,
    'metaHtml' => $metaHtml,
    'footerPrimary' => optional($cat->created_at)->format('M d, Y'),
    'footerSecondary' => null,
    'actionsHtml' => $actionsHtml,
])
