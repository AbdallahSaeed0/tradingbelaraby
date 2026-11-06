{{-- "Not connected" / API error messages. Expects $problem and $error. --}}
@if ($problem)
    <div class="alert alert-warning">
        <h5 class="alert-heading"><i class="fa fa-plug me-2"></i>{{ custom_trans('Google Analytics is not connected yet', 'admin') }}</h5>
        <p class="mb-2">{{ $problem }}</p>
        <ul class="mb-0 small">
            <li>Set <code>ANALYTICS_PROPERTY_ID</code> in <code>.env</code> (the number after <code>p</code> in the Analytics URL).</li>
            <li>Place the service account JSON key at <code>storage/app/analytics/service-account-credentials.json</code>.</li>
            <li>Run <code>php artisan config:clear</code>.</li>
        </ul>
    </div>
@elseif ($error)
    <div class="alert alert-danger">
        <h5 class="alert-heading"><i class="fa fa-exclamation-triangle me-2"></i>{{ custom_trans('Could not load Google Analytics data', 'admin') }}</h5>
        <p class="mb-1 small text-break">{{ \Illuminate\Support\Str::limit($error, 500) }}</p>
        <p class="mb-0 small">Check that the Google Analytics Data API is enabled and the service account has Viewer access on the property.</p>
    </div>
@endif
