@php
    $currentStatus = $coupon->is_active ? 'active' : 'inactive';
    if ($coupon->is_active && $coupon->isValid()) {
        $statusLabel = 'Active';
        $statusClass = 'bg-success';
    } elseif ($coupon->is_active) {
        $statusLabel = 'Scheduled';
        $statusClass = 'bg-warning text-dark';
    } else {
        $statusLabel = 'Inactive';
        $statusClass = 'bg-secondary';
    }
    $statusHtml = '<button type="button" class="badge ' . $statusClass . ' status-badge border-0 admin-mobile-card__status" onclick="showStatusModal(' . $coupon->id . ', \'' . $currentStatus . '\', [{ value: \'active\', label: \'Active\' }, { value: \'inactive\', label: \'Inactive\' }], \'' . route('admin.coupons.update_status', $coupon->id) . '\')">' . $statusLabel . '</button>';

    $discountChip = $coupon->discount_type === 'percentage'
        ? '<span class="badge bg-info">' . $coupon->discount_value . '%</span>'
        : '<span class="badge bg-success">' . $coupon->discount_value . ' SAR</span>';

    $scopeChip = $coupon->scope === 'all_courses'
        ? '<span class="badge bg-primary">All Courses</span>'
        : '<span class="badge bg-secondary">' . e($coupon->course->name ?? 'N/A') . '</span>';

    $userScopeChip = $coupon->user_scope === 'all_users'
        ? '<span class="badge bg-light text-dark">All Users</span>'
        : '<span class="badge bg-light text-dark">' . e($coupon->user->name ?? 'N/A') . '</span>';

    $chips = [$discountChip, $scopeChip, $userScopeChip];

    $usageText = $coupon->usages_count . ($coupon->usage_limit ? ' / ' . $coupon->usage_limit : ' / ∞');
    $stats = [
        ['icon' => 'fa-calendar', 'text' => $coupon->start_date->format('Y-m-d') . ' – ' . $coupon->end_date->format('Y-m-d')],
        ['icon' => 'fa-chart-line', 'text' => $usageText . ' used'],
    ];

    $actionsHtml = '<a href="' . route('admin.coupons.show', $coupon) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>'
        . '<a href="' . route('admin.coupons.edit', $coupon) . '" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-edit"></i></a>'
        . '<form action="' . route('admin.coupons.destroy', $coupon) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure?\');">'
        . '<input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE">'
        . '<button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button></form>';
@endphp

@include('admin.partials.mobile-data-card', [
    'itemId' => $coupon->id,
    'checkboxClass' => 'coupon-checkbox',
    'checkboxValue' => $coupon->id,
    'checkboxLabel' => 'Select ' . $coupon->name,
    'statusHtml' => $statusHtml,
    'heroUrl' => route('admin.coupons.show', $coupon),
    'iconClass' => 'fa-ticket-alt',
    'placeholder' => strtoupper(substr($coupon->code, 0, 2)),
    'title' => $coupon->name,
    'subtitle' => $coupon->code . ($coupon->description ? ' · ' . Str::limit($coupon->description, 40) : ''),
    'chips' => $chips,
    'stats' => $stats,
    'footerPrimary' => null,
    'footerSecondary' => null,
    'actionsHtml' => $actionsHtml,
])
