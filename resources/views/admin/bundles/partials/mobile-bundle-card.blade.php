@php
    $statusClass = $bundle->status === 'published' ? 'bg-success' : ($bundle->status === 'draft' ? 'bg-warning text-dark' : 'bg-secondary');
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $bundle->id . ', \'' . $bundle->status . '\', [{ value: \'published\', label: \'Published\' }, { value: \'draft\', label: \'Draft\' }, { value: \'archived\', label: \'Archived\' }], \'' . route('admin.bundles.update_status', $bundle->id) . '\')">' . ucfirst($bundle->status) . '</button>';

    $chips = [
        '<span class="badge bg-info">' . $bundle->courses_count . ' courses</span>',
        '<span class="badge bg-dark">' . e($bundle->formatted_price) . '</span>',
    ];
    if ($bundle->is_featured) {
        $chips[] = '<span class="badge bg-primary">Featured</span>';
    }

    $actionsHtml = '<a href="' . route('admin.bundles.show', $bundle) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.bundles.edit', $bundle) . '" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<form action="' . route('admin.bundles.destroy', $bundle) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure?\');">'
        . '<input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE">'
        . '<button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button></form>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $bundle->id,
    'checkboxClass' => 'bundle-checkbox',
    'checkboxValue' => $bundle->id,
    'checkboxLabel' => 'Select ' . $bundle->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.bundles.show', $bundle),
    'imageUrl' => $bundle->image_url,
    'placeholder' => strtoupper(substr($bundle->name, 0, 2)),
    'title' => $bundle->name,
    'subtitle' => $bundle->name_ar,
    'chips' => $chips,
    'stats' => [],
    'footerPrimary' => $bundle->created_at->format('M d, Y'),
    'footerSecondary' => $bundle->created_at->diffForHumans(),
    'actionsHtml' => $actionsHtml,
])
