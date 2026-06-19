@php
    if ($type->isAdminType()) {
        $statusHtml = '<span class="badge bg-success admin-mobile-card__status">Active</span>';
    } else {
        $statusClass = $type->is_active ? 'bg-success' : 'bg-danger';
        $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $type->id . ', \'' . ($type->is_active ? 'active' : 'inactive') . '\', [{ value: \'active\', label: \'Active\' }, { value: \'inactive\', label: \'Inactive\' }], \'' . route('admin.admin-types.update_status', $type->id) . '\')">' . ($type->is_active ? 'Active' : 'Inactive') . '</button>';
    }

    $chips = [
        '<span class="badge bg-info">' . ($type->admins_count ?? 0) . ' admins</span>',
        '<span class="badge bg-light text-dark">Order ' . $type->sort_order . '</span>',
    ];
    if ($type->isAdminType()) {
        $chips[] = '<span class="badge bg-warning text-dark">System</span>';
    } elseif ($type->permissions && count($type->permissions) > 0) {
        $chips[] = '<span class="badge bg-primary">' . count($type->permissions) . ' permissions</span>';
    }

    $metaHtml = $type->description
        ? e(Str::limit($type->description, 80))
        : '<span class="text-muted">No description</span>';

    $actionsHtml = '<a href="' . route('admin.admin-types.show', $type) . '" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>';
    if (!$type->isAdminType()) {
        $actionsHtml .= '<a href="' . route('admin.admin-types.edit', $type) . '" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fa fa-edit"></i></a>';
        if (($type->admins_count ?? 0) == 0) {
            $actionsHtml .= '<form action="' . route('admin.admin-types.destroy', $type) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this admin type?\');">'
                . '<input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE">'
                . '<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button></form>';
        }
    }
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $type->id,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.admin-types.show', $type),
    'iconClass' => 'fa-user-tag',
    'placeholder' => strtoupper(substr($type->display_name, 0, 2)),
    'title' => $type->display_name,
    'subtitle' => $type->slug,
    'chips' => $chips,
    'metaHtml' => $metaHtml,
    'footerPrimary' => $type->created_at->format('M d, Y'),
    'footerSecondary' => null,
    'actionsHtml' => $actionsHtml,
])
