@php
    $chips = [
        '<span class="qa-badge qa-badge-status-' . $question->status . '">' . ucfirst($question->status) . '</span>',
        '<span class="qa-badge qa-badge-priority-' . $question->priority . '">' . ucfirst($question->priority) . '</span>',
        '<span class="qa-badge qa-badge-type">' . ucfirst(str_replace('_', ' ', $question->question_type)) . '</span>',
    ];
    if ($question->is_anonymous) {
        $chips[] = '<span class="badge bg-secondary">Anonymous</span>';
    }
    if ($question->course) {
        array_unshift($chips, '<span class="badge bg-primary">' . e($question->course->name) . '</span>');
    }

    $studentLabel = $question->is_anonymous
        ? 'Anonymous'
        : ($question->user->name ?? 'Unknown');

    $stats = [
        ['icon' => 'fa-eye', 'text' => $question->views_count . ' views'],
        ['icon' => 'fa-thumbs-up', 'text' => $question->helpful_votes . '/' . $question->total_votes . ' votes'],
        ['icon' => 'fa-user', 'text' => $studentLabel],
    ];

    $replyUrl = route('admin.questions-answers.show', $question);
    $actionsHtml = '<a href="' . $replyUrl . '" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>';
    if ($question->status === 'pending' || $question->status === 'answered') {
        $actionsHtml .= '<a href="' . route('admin.questions-answers.reply', $question) . '" class="btn btn-sm btn-outline-info" title="Reply"><i class="fa fa-reply"></i></a>';
    }
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $question->id,
    'checkboxClass' => 'question-checkbox',
    'checkboxValue' => $question->id,
    'checkboxLabel' => 'Select question',
    'statusHtml' => '',
    'heroUrl' => route('admin.questions-answers.show', $question),
    'iconClass' => 'fa-question-circle',
    'placeholder' => '?',
    'title' => Str::limit($question->question_title, 60),
    'subtitle' => Str::limit(strip_tags($question->question_content), 80),
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $question->created_at ? $question->created_at->format('M d, Y') : null,
    'footerSecondary' => $question->created_at ? $question->created_at->format('g:i A') : null,
    'actionsHtml' => $actionsHtml,
])
