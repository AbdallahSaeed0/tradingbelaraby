{{-- Shows how the course in a cart line is being bought: lifetime, subscription, or extension --}}
@if (!$item->isBundle())
    @if ($item->isSubscription() && $item->subscriptionPlan)
        <span class="badge bg-info text-dark mb-2">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ $item->isRenewal() ? custom_trans('Extend access', 'front') : custom_trans('Subscription', 'front') }}:
            {{ $item->subscriptionPlan->duration_label }}
        </span>
    @else
        <span class="badge bg-secondary mb-2">
            <i class="fas fa-infinity me-1"></i>{{ custom_trans('Lifetime access', 'front') }}
        </span>
    @endif
@endif
