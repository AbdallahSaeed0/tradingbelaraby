@php
    $langClass = $subscriber->language === 'ar' ? 'bg-warning text-dark' : 'bg-primary';
    $langLabel = $subscriber->language === 'ar' ? 'العربية' : 'English';

    $chips = [
        '<span class="badge bg-info">' . e($subscriber->country) . '</span>',
        '<span class="badge bg-secondary">' . e($subscriber->years_of_experience) . ' Years</span>',
        '<span class="badge ' . $langClass . '">' . $langLabel . '</span>',
    ];

    $stats = [
        ['icon' => 'fa-envelope', 'text' => e($subscriber->email)],
        ['icon' => 'fa-phone', 'text' => e($subscriber->phone)],
    ];
    if ($subscriber->whatsapp_number) {
        $stats[] = ['icon' => 'fa-whatsapp', 'text' => e($subscriber->whatsapp_number)];
    }

    $actionsHtml = '<a href="' . route('admin.subscribers.show', $subscriber) . '" class="btn btn-sm btn-outline-info" title="View"><i class="fa fa-eye"></i></a>'
        . '<form action="' . route('admin.subscribers.destroy', $subscriber) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this subscriber?\');">'
        . '<input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE">'
        . '<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button></form>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $subscriber->id,
    'checkboxClass' => 'subscriber-checkbox',
    'checkboxValue' => $subscriber->id,
    'checkboxLabel' => 'Select ' . $subscriber->name,
    'heroUrl' => route('admin.subscribers.show', $subscriber),
    'placeholder' => strtoupper(substr($subscriber->name, 0, 1)),
    'title' => $subscriber->name,
    'subtitle' => $subscriber->email,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $subscriber->created_at->format('M d, Y'),
    'footerSecondary' => $subscriber->created_at->format('H:i'),
    'actionsHtml' => $actionsHtml,
    'checkboxExtraAttrs' => 'data-subscriber-name="' . e($subscriber->name) . '"',
])
