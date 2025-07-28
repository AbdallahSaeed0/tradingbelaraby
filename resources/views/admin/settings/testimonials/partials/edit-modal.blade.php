<!-- Edit Testimonial Modal -->
<div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-labelledby="editTestimonialModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTestimonialModalLabel">
                    <i class="fas fa-edit text-primary me-2"></i>{{ __('Edit Testimonial') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTestimonialForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_testimonial_id" name="testimonial_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">{{ __('Name') }} *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name_ar" class="form-label">{{ __('Name (Arabic)') }}</label>
                                <input type="text" class="form-control" id="edit_name_ar" name="name_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_position" class="form-label">{{ __('Position') }} *</label>
                                <input type="text" class="form-control" id="edit_position" name="position" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_position_ar" class="form-label">{{ __('Position (Arabic)') }}</label>
                                <input type="text" class="form-control" id="edit_position_ar" name="position_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_company" class="form-label">{{ __('Company') }} *</label>
                                <input type="text" class="form-control" id="edit_company" name="company" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_company_ar" class="form-label">{{ __('Company (Arabic)') }}</label>
                                <input type="text" class="form-control" id="edit_company_ar" name="company_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_content" class="form-label">{{ __('Content') }} *</label>
                                <textarea class="form-control" id="edit_content" name="content" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_content_ar" class="form-label">{{ __('Content (Arabic)') }}</label>
                                <textarea class="form-control" id="edit_content_ar" name="content_ar" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_rating" class="form-label">{{ __('Rating') }} *</label>
                                <select class="form-select" id="edit_rating" name="rating" required>
                                    <option value="5">5 {{ __('Stars') }}</option>
                                    <option value="4">4 {{ __('Stars') }}</option>
                                    <option value="3">3 {{ __('Stars') }}</option>
                                    <option value="2">2 {{ __('Stars') }}</option>
                                    <option value="1">1 {{ __('Star') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_order" class="form-label">{{ __('Order') }}</label>
                                <input type="number" class="form-control" id="edit_order" name="order"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_avatar" class="form-label">{{ __('Avatar') }}</label>
                                <input type="file" class="form-control" id="edit_avatar" name="avatar"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Current Avatar') }}</label>
                        <div id="edit_current_avatar"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">
                                {{ __('Active') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ __('Update Testimonial') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Edit testimonial form submission
        $('#editTestimonialForm').on('submit', function(e) {
            e.preventDefault();

            const testimonialId = $('#edit_testimonial_id').val();
            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Disable button and show loading
            submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('Updating...') }}');

            $.ajax({
                url: `/admin/settings/testimonials/${testimonialId}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#editTestimonialModal').modal('hide');
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
                            '{{ __('An error occurred while updating the testimonial') }}'
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
