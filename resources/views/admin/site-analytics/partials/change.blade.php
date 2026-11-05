@if ($change === null)
    <span class="db-stat-sub text-muted">—</span>
@else
    <span class="db-stat-sub {{ $change >= 0 ? 'text-success' : 'text-danger' }}">
        <i class="fa fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}"></i>
        {{ abs($change) }}%
    </span>
@endif
