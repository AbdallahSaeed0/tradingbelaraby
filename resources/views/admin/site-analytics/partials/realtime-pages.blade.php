@forelse ($pages as $page)
    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
        <span class="text-truncate me-2" title="{{ $page['title'] }}">{{ $page['title'] ?: '(no title)' }}</span>
        <span class="badge bg-warning text-dark">{{ $page['users'] }}</span>
    </div>
@empty
    <div class="db-widget-empty">
        <i class="fa fa-bolt"></i>
        <span>{{ custom_trans('Nobody on the site right now', 'admin') }}</span>
    </div>
@endforelse
