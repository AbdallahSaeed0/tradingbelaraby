@php
    $typeClass = $admin->type == 'admin' ? 'bg-danger' : ($admin->type == 'instructor' ? 'bg-warning text-dark' : 'bg-info');
    $statusClass = $admin->is_active ? 'bg-success' : 'bg-secondary';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $admin->id . ', \'' . ($admin->is_active ? 'active' : 'inactive') . '\', [{ value: \'active\', label: \'Active\' }, { value: \'inactive\', label: \'Inactive\' }], \'' . route('admin.admins.update_status', $admin->id) . '\')">' . ($admin->is_active ? 'Active' : 'Inactive') . '</button>';

    $chips = [
        '<span class="badge ' . $typeClass . '">' . e(ucfirst($admin->type)) . '</span>',
    ];
    if ($admin->phone) {
        $chips[] = '<span class="badge bg-light text-dark"><i class="fa fa-phone me-1"></i>' . e($admin->phone) . '</span>';
    }

    $stats = [
        ['icon' => 'fa-envelope', 'text' => e($admin->email)],
    ];

    $actionsHtml = '<a href="' . route('admin.admins.show', $admin) . '" class="btn btn-sm btn-outline-info" title="View"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.admins.edit', $admin) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<button type="button" onclick="deleteAdmin(' . $admin->id . ')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $admin->id,
    'checkboxClass' => 'admin-checkbox',
    'checkboxValue' => $admin->id,
    'checkboxLabel' => 'Select ' . $admin->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.admins.show', $admin),
    'iconClass' => 'fa-user-shield',
    'placeholder' => strtoupper(substr($admin->name, 0, 2)),
    'title' => $admin->name,
    'subtitle' => 'ID: ' . $admin->id,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $admin->created_at->format('M d, Y'),
    'footerSecondary' => $admin->created_at->diffForHumans(),
    'actionsHtml' => $actionsHtml,
])
