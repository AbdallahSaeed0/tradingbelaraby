@php
    $statusClass = $user->is_active ? 'bg-success' : 'bg-danger';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $user->id . ', \'' . ($user->is_active ? 'active' : 'inactive') . '\', [{ value: \'active\', label: \'Active\' }, { value: \'inactive\', label: \'Inactive\' }], \'' . route('admin.users.update_status', $user->id) . '\')">' . ($user->is_active ? 'Active' : 'Inactive') . '</button>';

    $chips = [
        $user->email_verified_at
            ? '<span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>Verified</span>'
            : '<span class="badge bg-warning text-dark"><i class="fa fa-times-circle me-1"></i>Not Verified</span>',
        '<span class="badge bg-primary">' . ($user->enrollments_count ?? 0) . ' enrollments</span>',
    ];

    $stats = [];
    if ($user->phone) {
        $stats[] = ['icon' => 'fa-phone', 'text' => e($user->phone)];
    }
    $stats[] = ['icon' => 'fa-envelope', 'text' => e($user->email)];

    $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : null;

    $actionsHtml = '<a href="' . route('admin.users.show', $user) . '" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.users.enrollments-report', $user) . '" class="btn btn-sm btn-outline-success" title="Enrollments"><i class="fa fa-chart-bar"></i></a>'
        . '<a href="' . route('admin.users.edit', $user) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $user->id,
    'checkboxClass' => 'row-checkbox',
    'checkboxValue' => $user->id,
    'checkboxLabel' => 'Select ' . $user->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.users.show', $user),
    'imageUrl' => $avatarUrl,
    'placeholder' => strtoupper(substr($user->name, 0, 2)),
    'title' => $user->name,
    'subtitle' => 'ID: ' . $user->id,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $user->created_at->format('M d, Y'),
    'footerSecondary' => $user->created_at->diffForHumans(),
    'actionsHtml' => $actionsHtml,
])
