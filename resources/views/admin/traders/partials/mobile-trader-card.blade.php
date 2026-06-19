@php
    $chips = [
        '<span class="badge bg-secondary">' . e(ucfirst($trader->sex)) . '</span>',
    ];
    if ($trader->trading_community) {
        $chips[] = '<span class="badge bg-success">' . e($trader->trading_community) . '</span>';
    }
    $langText = $trader->first_language;
    if ($trader->second_language) {
        $langText .= ' · ' . $trader->second_language;
    }
    $chips[] = '<span class="badge bg-light text-dark">' . e($langText) . '</span>';

    $stats = [
        ['icon' => 'fa-envelope', 'text' => e($trader->email)],
    ];
    if ($trader->phone_number) {
        $stats[] = ['icon' => 'fa-phone', 'text' => e($trader->phone_number)];
    }

    $actionsHtml = '<a href="' . route('admin.traders.show', $trader) . '" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>'
        . '<form action="' . route('admin.traders.destroy', $trader) . '" method="POST" class="d-inline" onsubmit="return confirm(\'' . e(custom_trans('Are you sure you want to delete this trader?', 'admin')) . '\');">'
        . '<input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE">'
        . '<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button></form>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $trader->id,
    'checkboxClass' => 'trader-checkbox',
    'checkboxValue' => $trader->id,
    'checkboxLabel' => 'Select ' . $trader->name,
    'heroUrl' => route('admin.traders.show', $trader),
    'iconClass' => 'fa-user',
    'placeholder' => strtoupper(substr($trader->name, 0, 2)),
    'title' => $trader->name,
    'subtitle' => $trader->email,
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => $trader->created_at->format('M d, Y'),
    'footerSecondary' => $trader->created_at->format('H:i'),
    'actionsHtml' => $actionsHtml,
])
