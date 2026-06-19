@php
    $quizStatus = $quiz->is_published ? 'published' : 'draft';
    $statusClass = $quiz->is_published ? 'bg-success' : 'bg-warning text-dark';
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $quiz->id . ', \'' . $quizStatus . '\', [{ value: \'published\', label: \'Published\' }, { value: \'draft\', label: \'Draft\' }], \'' . route('admin.quizzes.update_status', $quiz->id) . '\')">' . ($quiz->is_published ? 'Published' : 'Draft') . '</button>';

    $chips = [
        '<span class="badge bg-primary">' . e($quiz->course->name) . '</span>',
        '<span class="badge bg-info">' . $quiz->questions->count() . ' questions</span>',
        '<span class="badge bg-secondary">' . e($quiz->formatted_time_limit) . '</span>',
    ];

    $stats = [
        ['icon' => 'fa-clock', 'text' => $quiz->formatted_time_limit],
    ];

    $actionsHtml = '<a href="' . route('admin.quizzes.show', $quiz) . '" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.quizzes.edit', $quiz) . '" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<a href="' . route('admin.quizzes.analytics', $quiz) . '" class="btn btn-sm btn-outline-info" title="Analytics"><i class="fa fa-chart-bar"></i></a>'
        . '<form action="' . route('admin.quizzes.destroy', $quiz) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure?\');">'
        . '<input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE">'
        . '<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button></form>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $quiz->id,
    'checkboxClass' => 'row-checkbox',
    'checkboxValue' => $quiz->id,
    'checkboxLabel' => 'Select ' . $quiz->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.quizzes.show', $quiz),
    'iconClass' => 'fa-question-circle',
    'placeholder' => strtoupper(substr($quiz->name, 0, 2)),
    'title' => $quiz->name,
    'subtitle' => Str::limit($quiz->description, 50),
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $quiz->created_at->format('M d, Y'),
    'footerSecondary' => $quiz->created_at->diffForHumans(),
    'actionsHtml' => $actionsHtml,
])
