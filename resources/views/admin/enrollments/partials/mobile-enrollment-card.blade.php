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
        '<span class="badge bg-light text-dark">' . round($enrollment->progress_percentage) . '% progress</span>',
    ];

    $stats = [
        ['icon' => 'fa-book', 'text' => $enrollment->completed_lectures . '/' . $enrollment->total_lectures . ' lectures'],
        ['icon' => 'fa-user', 'text' => e($instructorNames)],
    ];

    if ($enrollment->last_activity) {
        $footerPrimary = $enrollment->created_at->format('M d, Y');
        $footerSecondary = 'Active ' . $enrollment->last_activity->diffForHumans();
    } else {
        $footerPrimary = $enrollment->created_at->format('M d, Y');
        $footerSecondary = $enrollment->created_at->diffForHumans();
    }

    $avatarUrl = $enrollment->user->avatar ? asset('storage/' . $enrollment->user->avatar) : null;

    $actionsHtml = '<a href="' . route('admin.courses.enrollments', $enrollment->course) . '" class="btn btn-sm btn-outline-info" title="View Course Enrollments"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.courses.show', $enrollment->course) . '" class="btn btn-sm btn-outline-primary" title="View Course"><i class="fa fa-book"></i></a>'
        . '<button type="button" onclick="sendMessage(' . $enrollment->user_id . ')" class="btn btn-sm btn-outline-warning" title="Send Message"><i class="fa fa-envelope"></i></button>'
        . '<button type="button" onclick="viewProgress(' . $enrollment->id . ')" class="btn btn-sm btn-outline-success" title="View Progress"><i class="fa fa-chart-bar"></i></button>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $enrollment->id,
    'checkboxClass' => 'enrollment-checkbox',
    'checkboxValue' => $enrollment->id,
    'checkboxLabel' => 'Select ' . $enrollment->user->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.courses.show', $enrollment->course),
    'imageUrl' => $avatarUrl,
    'placeholder' => strtoupper(substr($enrollment->user->name, 0, 2)),
    'title' => $enrollment->user->name,
    'subtitle' => $enrollment->user->email,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $footerPrimary,
    'footerSecondary' => $footerSecondary,
    'actionsHtml' => $actionsHtml,
])
