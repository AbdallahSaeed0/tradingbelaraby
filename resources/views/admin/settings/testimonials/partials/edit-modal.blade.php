<!-- Edit Testimonial Modal -->
<div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-labelledby="editTestimonialModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTestimonialModalLabel">
                    <i class="fas fa-edit text-primary me-2"></i>{{ custom_trans('Edit Testimonial', 'admin') }}
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
                                <label for="edit_name" class="form-label">{{ custom_trans('Name', 'admin') }} *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name_ar" class="form-label">{{ custom_trans('Name (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_name_ar" name="name_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_position" class="form-label">{{ custom_trans('Position', 'admin') }} *</label>
                                <input type="text" class="form-control" id="edit_position" name="position" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_position_ar" class="form-label">{{ custom_trans('Position (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_position_ar" name="position_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_company" class="form-label">{{ custom_trans('Company', 'admin') }} *</label>
                                <input type="text" class="form-control" id="edit_company" name="company" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_company_ar" class="form-label">{{ custom_trans('Company (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_company_ar" name="company_ar">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_content" class="form-label">{{ custom_trans('Content', 'admin') }}</label>
                                <textarea class="form-control" id="edit_content" name="content" rows="4"></textarea>
                                <small class="text-muted">{{ custom_trans('Either content or voice recording is required', 'admin') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_content_ar" class="form-label">{{ custom_trans('Content (Arabic)', 'admin') }}</label>
                                <textarea class="form-control" id="edit_content_ar" name="content_ar" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_rating" class="form-label">{{ custom_trans('Rating', 'admin') }} *</label>
                                <select class="form-select" id="edit_rating" name="rating" required>
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
                                <label for="edit_order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                <input type="number" class="form-control" id="edit_order" name="order"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_avatar" class="form-label">{{ custom_trans('Avatar', 'admin') }}</label>
                                <input type="file" class="form-control" id="edit_avatar" name="avatar"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ custom_trans('Current Avatar', 'admin') }}</label>
                        <div id="edit_current_avatar"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_voice" class="form-label">{{ custom_trans('Voice Recording', 'admin') }}</label>
                                <div class="mb-2">
                                    <label class="form-label small">{{ custom_trans('Upload Audio File', 'admin') }}</label>
                                    <input type="file" class="form-control" id="edit_voice" name="voice"
                                        accept="audio/*">
                                    <small class="text-muted">{{ custom_trans('Upload an audio testimonial (MP3, WAV, M4A, OGG, AAC, WebM - Max 50MB)', 'admin') }}</small>
                                </div>
                                <div class="text-center my-2">
                                    <strong class="text-muted">{{ custom_trans('OR', 'admin') }}</strong>
                                </div>
                                <div>
                                    <label class="form-label small">{{ custom_trans('Google Drive Link', 'admin') }}</label>
                                    <input type="url" class="form-control" id="edit_voice_url" name="voice_url"
                                        placeholder="https://drive.google.com/file/d/...">
                                    <small class="text-muted">{{ custom_trans('Paste a Google Drive share link for the audio file', 'admin') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="edit_current_voice_container" style="display: none;">
                        <label class="form-label">{{ custom_trans('Current Voice Recording', 'admin') }}</label>
                        <div class="d-flex align-items-center gap-3">
                            <audio id="edit_current_voice_player" controls class="flex-grow-1"></audio>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_remove_voice" name="remove_voice" value="1">
                                <label class="form-check-label text-danger" for="edit_remove_voice">
                                    <i class="fas fa-trash me-1"></i>{{ custom_trans('Remove', 'admin') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">
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
                        <i class="fas fa-save me-2"></i>{{ custom_trans('Update Testimonial', 'admin') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
