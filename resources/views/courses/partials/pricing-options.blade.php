{{--
    Pricing choices for a course: "buy forever" and/or subscription periods.
    Existing subscribers see the extension prices. All option groups on the page stay in sync,
    and the enroll button sends the checked value as plan_id ("" = lifetime).
    Params: $course, $context (unique suffix for the radio group name)
--}}
@php
    $enrollment = auth()->check() ? $course->getUserEnrollment(auth()->user()) : null;
    $isSubscriber = $enrollment && $enrollment->isSubscription()
        && in_array($enrollment->status, ['active', 'completed', 'expired'], true);
    $plans = $course->offersSubscription() ? $course->activeSubscriptionPlans : collect();
    $showLifetime = $course->offersLifetime();
    $currentPlanId = $enrollment && $enrollment->isSubscription() ? $enrollment->subscription_plan_id : null;
    $defaultPlanId = $showLifetime && !$isSubscriber ? '' : (string) optional($plans->firstWhere('id', $currentPlanId) ?? $plans->first())->id;
@endphp

@if ($plans->isNotEmpty())
    <div class="course-pricing-options mb-3">
        @if ($isSubscriber && $enrollment->expires_at)
            <div class="small mb-2 {{ $enrollment->isExpired() ? 'text-danger' : 'text-muted' }}">
                <i class="fas fa-calendar-alt me-1"></i>
                @if ($enrollment->isExpired())
                    {{ custom_trans('Your access expired on', 'front') }}
                @else
                    {{ custom_trans('Your access ends on', 'front') }}
                @endif
                <strong>{{ $enrollment->expires_at->format('Y-m-d') }}</strong>
            </div>
            <div class="fw-bold small mb-2">
                {{ $enrollment->isExpired() ? custom_trans('Renew your access', 'front') : custom_trans('Add more time', 'front') }}
            </div>
        @endif

        @if ($showLifetime)
            <label class="d-flex align-items-center justify-content-between border rounded-3 p-2 mb-2" style="cursor:pointer">
                <span>
                    <input class="form-check-input me-2" type="radio" name="pricing_option_{{ $context }}"
                        data-pricing-option value="" {{ $defaultPlanId === '' ? 'checked' : '' }}>
                    <i class="fas fa-infinity text-orange me-1"></i>
                    {{ $isSubscriber ? custom_trans('Upgrade to lifetime access', 'front') : custom_trans('Lifetime access', 'front') }}
                </span>
                <span class="fw-bold text-orange">{{ number_format($course->price, 2) }} SAR</span>
            </label>
        @endif

        @foreach ($plans as $plan)
            @php
                $price = $plan->priceFor($isSubscriber);
            @endphp
            <label class="d-flex align-items-center justify-content-between border rounded-3 p-2 mb-2" style="cursor:pointer">
                <span>
                    <input class="form-check-input me-2" type="radio" name="pricing_option_{{ $context }}"
                        data-pricing-option value="{{ $plan->id }}" {{ $defaultPlanId === (string) $plan->id ? 'checked' : '' }}>
                    <i class="fas fa-calendar-alt text-orange me-1"></i>
                    {{ $isSubscriber ? '+ ' : '' }}{{ $plan->duration_label }}
                </span>
                <span class="fw-bold text-orange">
                    {{ number_format($price, 2) }} SAR
                    @if ($price < (float) $plan->price)
                        <span class="small text-decoration-line-through text-muted ms-1">{{ number_format($plan->price, 2) }}</span>
                    @endif
                </span>
            </label>
        @endforeach
    </div>
@endif
