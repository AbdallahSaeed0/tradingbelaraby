@php
    $hwStatus = $hw->is_published ? 'published' : 'draft';
    $statusClass = $hw->is_published ? 'bg-success' : 'bg-secondary';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $hw->id . ', \'' . $hwStatus . '\', [{ value: \'published\', label: \'Published\' }, { value: \'draft\', label: \'Draft\' }], \'' . route('admin.homework.update_status', $hw->id) . '\')">' . ($hw->is_published ? 'Published' : 'Draft') . '</button>';

    $chips = [
        '<span class="badge bg-light text-dark">' . e($hw->course->name) . '</span>',
        '<span class="badge bg-info">' . e($hw->instructor->name) . '</span>',
        '<span class="badge bg-' . ($hw->due_date->isPast() ? 'danger' : 'success') . '">Due ' . $hw->due_date->format('M d, Y') . '</span>',
    ];

    $submissionText = $hw->submitted_assignments . '/' . ($hw->total_assignments ?: 'N/A') . ' submissions';
    if ($hw->graded_assignments > 0) {
        $submissionText .= ' · ' . $hw->graded_assignments . ' graded';
    }

    $stats = [
        ['icon' => 'fa-upload', 'text' => $submissionText],
        ['icon' => 'fa-clock', 'text' => $hw->due_date->format('h:i A')],
    ];

    $actionsHtml = '<a href="' . route('admin.homework.edit', $hw) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<a href="' . route('admin.homework.submissions', $hw) . '" class="btn btn-sm btn-outline-info" title="Submissions"><i class="fa fa-upload"></i></a>'
        . '<a href="' . route('admin.homework.analytics', $hw) . '" class="btn btn-sm btn-outline-success" title="Analytics"><i class="fa fa-chart-bar"></i></a>'
        . '<button type="button" onclick="duplicateHomework(' . $hw->id . ')" class="btn btn-sm btn-outline-secondary" title="Duplicate"><i class="fa fa-copy"></i></button>'
        . '<button type="button" onclick="deleteHomework(' . $hw->id . ')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $hw->id,
    'checkboxClass' => 'homework-checkbox',
    'checkboxValue' => $hw->id,
    'checkboxLabel' => 'Select ' . $hw->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.homework.edit', $hw),
    'iconClass' => 'fa-book',
    'placeholder' => strtoupper(substr($hw->name, 0, 2)),
    'title' => $hw->name,
    'subtitle' => $hw->description ? Str::limit($hw->description, 50) : null,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $hw->due_date->format('M d, Y'),
    'footerSecondary' => $hw->due_date->diffForHumans(),
    'actionsHtml' => $actionsHtml,
])
