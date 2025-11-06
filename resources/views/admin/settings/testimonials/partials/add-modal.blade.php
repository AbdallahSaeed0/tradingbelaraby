<!-- Add Testimonial Modal -->
<div class="modal fade" id="addTestimonialModal" tabindex="-1" aria-labelledby="addTestimonialModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTestimonialModalLabel">
                    <i class="fas fa-plus text-primary me-2"></i>{{ custom_trans('Add New Testimonial', 'admin') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTestimonialForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ custom_trans('Name', 'admin') }} *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name_ar" class="form-label">{{ custom_trans('Name (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="name_ar" name="name_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">{{ custom_trans('Position', 'admin') }} *</label>
                                <input type="text" class="form-control" id="position" name="position" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position_ar" class="form-label">{{ custom_trans('Position (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="position_ar" name="position_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company" class="form-label">{{ custom_trans('Company', 'admin') }} *</label>
                                <input type="text" class="form-control" id="company" name="company" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_ar" class="form-label">{{ custom_trans('Company (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="company_ar" name="company_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="content" class="form-label">{{ custom_trans('Content', 'admin') }} *</label>
                                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="content_ar" class="form-label">{{ custom_trans('Content (Arabic)', 'admin') }}</label>
                                <textarea class="form-control" id="content_ar" name="content_ar" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="rating" class="form-label">{{ custom_trans('Rating', 'admin') }} *</label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value="5">5 {{ custom_trans('Stars', 'admin') }}</option>
                                    <option value="4">4 {{ custom_trans('Stars', 'admin') }}</option>
                                    <option value="3">3 {{ custom_trans('Stars', 'admin') }}</option>
                                    <option value="2">2 {{ custom_trans('Stars', 'admin') }}</option>
                                    <option value="1">1 {{ custom_trans('Star', 'admin') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                <input type="number" class="form-control" id="order" name="order"
                                    value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">{{ custom_trans('Avatar', 'admin') }}</label>
                                <input type="file" class="form-control" id="avatar" name="avatar"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                {{ custom_trans('Active', 'admin') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>{{ custom_trans('Cancel', 'admin') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ custom_trans('Save Testimonial', 'admin') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Add testimonial form submission
        $('#addTestimonialForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Disable button and show loading
            submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin me-2"></i>{{ custom_trans('Saving...', 'admin') }}');

            $.ajax({
                url: '{{ route('admin.settings.testimonials.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#addTestimonialModal').modal('hide');
                        $('#addTestimonialForm')[0].reset();
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        if (response.errors) {
                            Object.keys(response.errors).forEach(function(key) {
                                toastr.error(response.errors[key][0]);
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                            toastr.error(xhr.responseJSON.errors[key][0]);
                        });
                    } else {
                        toastr.error(
                            '{{ custom_trans('An error occurred while saving the testimonial', 'admin') }}'
                            );
                    }
                },
                complete: function() {
                    // Re-enable button and restore original text
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
