{{-- "View full report" footer for overview widgets. Expects $report, $view, $days. --}}
<div class="db-widget-footer">
    <a href="{{ route('admin.site-analytics.report', ['reportKey' => $report, 'view' => $view, 'period' => $days]) }}" class="db-widget-link db-link-primary">
        {{ custom_trans('View full report', 'admin') }} <i class="fa fa-arrow-right ms-1"></i>
    </a>
</div>
