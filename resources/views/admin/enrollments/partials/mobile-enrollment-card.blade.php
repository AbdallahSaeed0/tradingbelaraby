@php
    $statusClasses = [
        'active' => 'bg-primary',
        'enrolled' => 'bg-primary',
        'pending' => 'bg-warning text-dark',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger',
    ];
    $statusClass = $statusClasses[$enrollment->status] ?? 'bg-secondary';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $enrollment->id . ', \'' . $enrollment->status . '\', [{ value: \'active\', label: \'Active\' }, { value: \'pending\', label: \'Pending\' }, { value: \'completed\', label: \'Completed\' }, { value: \'cancelled\', label: \'Cancelled\' }], \'' . route('admin.enrollments.update_status', $enrollment->id) . '\')">' . ucfirst($enrollment->status) . '</button>';

    $instructorNames = $enrollment->course->instructors && $enrollment->course->instructors->count() > 0
        ? $enrollment->course->instructors->pluck('name')->take(2)->join(', ')
        : ($enrollment->course->instructor->name ?? 'N/A');

    $chips = [
        '<span class="badge bg-primary">' . e($enrollment->course->name) . '</span>',
        '<span class="badge bg-light text-dark">' . ($enrollment->live_percent ?? 0) . '% progress</span>',
    ];

    $stats = [
        ['icon' => 'fa-book', 'text' => ($enrollment->live_completed ?? 0) . '/' . ($enrollment->live_total ?? 0) . ' lectures'],
        ['icon' => 'fa-user', 'text' => e($instructorNames)],
    ];

    if ($enrollment->live_last_activity ?? null) {
        $footerPrimary = $enrollment->created_at->format('M d, Y');
        $footerSecondary = 'Active ' . $enrollment->live_last_activity->diffForHumans();
    } else {
        $footerPrimary = $enrollment->created_at->format('M d, Y');
        $footerSecondary = $enrollment->created_at->diffForHumans();
    }

    $avatarUrl = $enrollment->user->avatar ? asset('storage/' . $enrollment->user->avatar) : null;

    $actionsHtml = '<a href="' . route('admin.enrollments.show', $enrollment) . '" class="btn btn-sm btn-outline-success" title="View Progress"><i class="fa fa-chart-bar"></i></a>'
        . '<a href="' . route('admin.courses.enrollments', $enrollment->course) . '" class="btn btn-sm btn-outline-info" title="View Course Enrollments"><i class="fa fa-users"></i></a>'
        . '<a href="' . route('admin.courses.show', $enrollment->course) . '" class="btn btn-sm btn-outline-primary" title="View Course"><i class="fa fa-book"></i></a>'
        . '<a href="mailto:' . e($enrollment->user->email) . '" class="btn btn-sm btn-outline-warning" title="Email Student"><i class="fa fa-envelope"></i></a>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $enrollment->id,
    'checkboxClass' => 'enrollment-checkbox',
    'checkboxValue' => $enrollment->id,
    'checkboxLabel' => 'Select ' . $enrollment->user->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.enrollments.show', $enrollment),
    'imageUrl' => $avatarUrl,
    'iconClass' => 'fa-user',
    'title' => $enrollment->user->name,
    'subtitle' => $enrollment->user->email,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $footerPrimary,
    'footerSecondary' => $footerSecondary,
    'actionsHtml' => $actionsHtml,
])
