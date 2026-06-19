@php
    $statusColors = [
        'scheduled' => 'bg-primary',
        'live' => 'bg-success',
        'completed' => 'bg-secondary',
        'cancelled' => 'bg-danger',
    ];
    $statusClass = $statusColors[$liveClass->status] ?? 'bg-secondary';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $liveClass->id . ', \'' . $liveClass->status . '\', [{ value: \'scheduled\', label: \'Scheduled\' }, { value: \'live\', label: \'Live\' }, { value: \'completed\', label: \'Completed\' }, { value: \'cancelled\', label: \'Cancelled\' }], \'' . route('admin.live-classes.update_status', $liveClass->id) . '\')">' . ucfirst($liveClass->status) . '</button>';

    $chips = [
        '<span class="badge bg-light text-dark">' . e($liveClass->course->name) . '</span>',
        '<span class="badge bg-info">' . e($liveClass->instructor->name) . '</span>',
        '<span class="badge bg-secondary">' . e($liveClass->formatted_duration) . '</span>',
    ];

    $participantText = $liveClass->max_participants
        ? $liveClass->current_participants . '/' . $liveClass->max_participants . ' participants'
        : $liveClass->current_participants . ' registered';

    $stats = [
        ['icon' => 'fa-calendar', 'text' => $liveClass->scheduled_at->format('M d, Y h:i A')],
        ['icon' => 'fa-users', 'text' => $participantText],
    ];

    $actionsHtml = '<a href="' . route('admin.live-classes.edit', $liveClass) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<a href="' . route('admin.live-classes.registrations', $liveClass) . '" class="btn btn-sm btn-outline-info" title="Registrations"><i class="fa fa-users"></i></a>'
        . '<button type="button" onclick="duplicateClass(' . $liveClass->id . ')" class="btn btn-sm btn-outline-secondary" title="Duplicate"><i class="fa fa-copy"></i></button>'
        . '<button type="button" onclick="deleteClass(' . $liveClass->id . ')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $liveClass->id,
    'checkboxClass' => 'class-checkbox',
    'checkboxValue' => $liveClass->id,
    'checkboxLabel' => 'Select ' . $liveClass->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.live-classes.edit', $liveClass),
    'iconClass' => 'fa-video',
    'placeholder' => strtoupper(substr($liveClass->name, 0, 2)),
    'title' => $liveClass->name,
    'subtitle' => $liveClass->description ? Str::limit($liveClass->description, 50) : null,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $liveClass->scheduled_at->format('M d, Y'),
    'footerSecondary' => $liveClass->scheduled_at->diffForHumans(),
    'actionsHtml' => $actionsHtml,
])
