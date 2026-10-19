@php
    /** @var \App\Models\Course|null $course */
    $course = $course ?? null;
    $allowLifetime = old('allow_lifetime', $course ? (int) $course->offersLifetime() : 1);
    $allowSubscription = old('allow_subscription', $course ? (int) $course->allow_subscription : 0);
    $planRows = old('plans');
    if ($planRows === null) {
        $planRows = $course && $course->subscriptionPlans->isNotEmpty()
            ? $course->subscriptionPlans->map(fn ($plan) => [
                'id' => $plan->id,
                'duration_months' => $plan->duration_months,
                'price' => $plan->price,
                'renewal_price' => $plan->renewal_price,
                'is_active' => $plan->is_active ? 1 : 0,
            ])->all()
            : [
                ['duration_months' => 3, 'price' => '', 'renewal_price' => '', 'is_active' => 1],
                ['duration_months' => 6, 'price' => '', 'renewal_price' => '', 'is_active' => 1],
                ['duration_months' => 12, 'price' => '', 'renewal_price' => '', 'is_active' => 1],
            ];
    }
@endphp

<div class="col-md-12 mb-3">
    <div class="card border">
        <div class="card-header bg-light fw-bold">
            <i class="fas fa-tags me-2"></i>Pricing Options
        </div>
        <div class="card-body">
            <div class="form-check form-switch mb-2">
                <input type="hidden" name="allow_lifetime" value="0">
                <input class="form-check-input" type="checkbox" id="allow_lifetime" name="allow_lifetime" value="1"
                    {{ $allowLifetime ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_lifetime">
                    <strong>Buy forever (lifetime access)</strong>
                    <small class="text-muted d-block">Uses the Original / Discount price above.</small>
                </label>
            </div>
            <div class="form-check form-switch mb-3">
                <input type="hidden" name="allow_subscription" value="0">
                <input class="form-check-input" type="checkbox" id="allow_subscription" name="allow_subscription"
                    value="1" {{ $allowSubscription ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_subscription">
                    <strong>Subscription (time-limited access)</strong>
                    <small class="text-muted d-block">Student gets access for the chosen period and can add more time later.</small>
                </label>
            </div>
            @error('allow_lifetime')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div id="subscription_plans_wrapper" style="{{ $allowSubscription ? '' : 'display:none;' }}">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-2">
                        <thead>
                            <tr>
                                <th style="min-width:120px">Duration (months)</th>
                                <th style="min-width:130px">Price (SAR)</th>
                                <th style="min-width:150px">Extension price (SAR)</th>
                                <th>Active</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="subscription_plans_body">
                            @foreach ($planRows as $i => $row)
                                <tr class="subscription-plan-row">
                                    <td>
                                        <input type="hidden" name="plans[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
                                        <input type="number" class="form-control form-control-sm" min="1" max="120"
                                            name="plans[{{ $i }}][duration_months]" value="{{ $row['duration_months'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" min="0" step="0.01"
                                            name="plans[{{ $i }}][price]" value="{{ $row['price'] ?? '' }}" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" min="0" step="0.01"
                                            name="plans[{{ $i }}][renewal_price]" value="{{ $row['renewal_price'] ?? '' }}"
                                            placeholder="Same as price">
                                    </td>
                                    <td>
                                        <input type="hidden" name="plans[{{ $i }}][is_active]" value="0">
                                        <input class="form-check-input" type="checkbox" name="plans[{{ $i }}][is_active]" value="1"
                                            {{ !empty($row['is_active']) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-plan-row" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add_plan_row">
                    <i class="fas fa-plus me-1"></i>Add plan
                </button>
                <small class="text-muted d-block mt-2">
                    <strong>Price</strong> is what a new student pays for that period.
                    <strong>Extension price</strong> is what an existing subscriber pays to add that period to their access
                    (leave empty to charge the normal price). Rows with no duration or price are ignored.
                </small>
                @error('plans')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @foreach ($errors->get('plans.*') as $messages)
                    <div class="text-danger small mt-1">{{ $messages[0] }}</div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('allow_subscription');
        const wrapper = document.getElementById('subscription_plans_wrapper');
        const body = document.getElementById('subscription_plans_body');
        const addBtn = document.getElementById('add_plan_row');
        if (!toggle || !wrapper || !body || !addBtn) return;

        toggle.addEventListener('change', () => {
            wrapper.style.display = toggle.checked ? '' : 'none';
        });

        let nextIndex = body.querySelectorAll('.subscription-plan-row').length + 100;

        addBtn.addEventListener('click', () => {
            const i = nextIndex++;
            const tr = document.createElement('tr');
            tr.className = 'subscription-plan-row';
            tr.innerHTML = `
                <td><input type="hidden" name="plans[${i}][id]" value="">
                    <input type="number" class="form-control form-control-sm" min="1" max="120" name="plans[${i}][duration_months]"></td>
                <td><input type="number" class="form-control form-control-sm" min="0" step="0.01" name="plans[${i}][price]" placeholder="0.00"></td>
                <td><input type="number" class="form-control form-control-sm" min="0" step="0.01" name="plans[${i}][renewal_price]" placeholder="Same as price"></td>
                <td><input type="hidden" name="plans[${i}][is_active]" value="0">
                    <input class="form-check-input" type="checkbox" name="plans[${i}][is_active]" value="1" checked></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-plan-row" title="Remove"><i class="fas fa-trash"></i></button></td>`;
            body.appendChild(tr);
        });

        body.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-plan-row');
            if (btn) btn.closest('tr').remove();
        });
    });
</script>
