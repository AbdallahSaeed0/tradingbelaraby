{{-- Wires .category-enroll-btn buttons on course cards (partials.courses.category-course-card):
     free courses enroll directly, paid ones are added to the cart. Include once per page inside @push('scripts'). --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ─── Enroll ──────────────────────────────────────────────────────────────
    document.querySelectorAll('.category-enroll-btn').forEach(button => {
        button.addEventListener('click', function () {
            const courseId   = this.dataset.courseId;
            const enrollType = this.dataset.enrollType || 'free';
            const original   = this.innerHTML;
            const csrf       = document.querySelector('meta[name="csrf-token"]');

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ custom_trans('processing', 'front') }}';

            if (!csrf) {
                toastr.error('CSRF token not found.');
                this.disabled = false;
                this.innerHTML = original;
                return;
            }

            const reset = () => { this.disabled = false; this.innerHTML = original; };

            if (enrollType === 'paid') {
                fetch(`/cart/add/${courseId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf.getAttribute('content') },
                    body: JSON.stringify({})
                })
                .then(r => {
                    if (r.status === 401) { window.location.href = '/login'; return; }
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    return r.json();
                })
                .then(data => {
                    if (data?.success) {
                        const modal = document.getElementById('categoryCartSuccessModal');
                        if (modal && typeof bootstrap !== 'undefined') new bootstrap.Modal(modal).show();
                        else toastr.success(data.message || '{{ custom_trans('Course added to cart successfully.', 'front') }}');
                    } else if (data) {
                        toastr.info(data.message || '{{ custom_trans('Unable to add course to cart.', 'front') }}');
                    }
                    reset();
                })
                .catch(() => { toastr.error('{{ custom_trans('An unexpected error occurred. Please try again later.', 'front') }}'); reset(); });
                return;
            }

            fetch(`/courses/${courseId}/enroll`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf.getAttribute('content') },
                body: JSON.stringify({})
            })
            .then(r => {
                if (r.status === 401) { window.location.href = '/login'; return; }
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (data?.success) {
                    this.className = 'cc-btn cc-btn-primary';
                    this.innerHTML = '<i class="fas fa-check me-1"></i>{{ custom_trans('enrolled', 'front') }}';
                    this.disabled = true;
                    toastr.success(data.message || '{{ custom_trans('Successfully enrolled in course!', 'front') }}');
                    setTimeout(() => {
                        const link = document.createElement('a');
                        link.href = `/courses/${courseId}/learn`;
                        link.className = 'cc-btn cc-btn-primary';
                        link.innerHTML = '<i class="fas fa-graduation-cap me-1"></i>{{ custom_trans('go_to_course', 'front') }}';
                        this.parentNode.replaceChild(link, this);
                    }, 2000);
                } else if (data) {
                    toastr.error(data.message || 'Error');
                    reset();
                }
            })
            .catch(() => { toastr.error('An error occurred.'); reset(); });
        });
    });
});
</script>
