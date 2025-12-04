@extends('admin.layout')

@section('title', custom_trans('Testimonials Management', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Testimonials Management', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Testimonials', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addTestimonialModal">
                            <i class="fas fa-plus me-2"></i>{{ custom_trans('Add Testimonial', 'admin') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card content-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="search" class="form-label">{{ custom_trans('Search', 'admin') }}</label>
                            <input type="text" class="form-control" id="search"
                                placeholder="{{ custom_trans('Search testimonials...', 'admin') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ custom_trans('Status', 'admin') }}</label>
                            <select class="form-select" id="status">
                                <option value="">{{ custom_trans('All', 'admin') }}</option>
                                <option value="active">{{ custom_trans('Active', 'admin') }}</option>
                                <option value="inactive">{{ custom_trans('Inactive', 'admin') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sort" class="form-label">{{ custom_trans('Sort By', 'admin') }}</label>
                            <select class="form-select" id="sort">
                                <option value="order">{{ custom_trans('Order', 'admin') }}</option>
                                <option value="name">{{ custom_trans('Name', 'admin') }}</option>
                                <option value="created_at">{{ custom_trans('Created Date', 'admin') }}</option>
                                <option value="rating">{{ custom_trans('Rating', 'admin') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="direction" class="form-label">{{ custom_trans('Direction', 'admin') }}</label>
                            <select class="form-select" id="direction">
                                <option value="asc">{{ custom_trans('Ascending', 'admin') }}</option>
                                <option value="desc">{{ custom_trans('Descending', 'admin') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonials Table -->
        <div class="card content-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-quote-left text-primary me-2"></i>{{ custom_trans('All Testimonials', 'admin') }}
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success bulk-action-btn" data-action="activate"
                                disabled>
                                <i class="fas fa-check me-1"></i>{{ custom_trans('Activate', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-warning bulk-action-btn" data-action="deactivate"
                                disabled>
                                <i class="fas fa-pause me-1"></i>{{ custom_trans('Deactivate', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger bulk-action-btn" data-action="delete"
                                disabled>
                                <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete', 'admin') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="testimonialsTable">
                        <thead>
                            <tr>
                                <th width="30">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                    </div>
                                </th>
                                <th width="80">{{ custom_trans('Order', 'admin') }}</th>
                                <th width="80">{{ custom_trans('Avatar', 'admin') }}</th>
                                <th>{{ custom_trans('Name', 'admin') }}</th>
                                <th>{{ custom_trans('Name (AR)', 'admin') }}</th>
                                <th>{{ custom_trans('Position', 'admin') }}</th>
                                <th>{{ custom_trans('Company', 'admin') }}</th>
                                <th>{{ custom_trans('Content', 'admin') }}</th>
                                <th width="80">{{ custom_trans('Rating', 'admin') }}</th>
                                <th width="100">{{ custom_trans('Status', 'admin') }}</th>
                                <th width="120">{{ custom_trans('Actions', 'admin') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($testimonials as $testimonial)
                                <tr data-id="{{ $testimonial->id }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input testimonial-checkbox" type="checkbox"
                                                value="{{ $testimonial->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $testimonial->order }}</span>
                                    </td>
                                    <td>
                                        <img src="{{ $testimonial->avatar_url }}" alt="{{ $testimonial->name }}"
                                            class="rounded-circle w-40 h-40 img-h-60">
                                    </td>
                                    <td>{{ $testimonial->name }}</td>
                                    <td>{{ $testimonial->name_ar ?: '-' }}</td>
                                    <td>{{ $testimonial->position }}</td>
                                    <td>{{ $testimonial->company }}</td>
                                    <td>{{ Str::limit($testimonial->content, 50) }}</td>
                                    <td>
                                        <div class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $testimonial->rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                {{ $testimonial->is_active ? 'checked' : '' }}
                                                data-id="{{ $testimonial->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-info edit-testimonial-btn"
                                                data-testimonial="{{ json_encode($testimonial->toArray()) }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-testimonial-btn"
                                                data-id="{{ $testimonial->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-quote-left fa-3x mb-3"></i>
                                            <p>{{ custom_trans('No testimonials found', 'admin') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($testimonials->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $testimonials->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Testimonial Modal -->
    @include('admin.settings.testimonials.partials.add-modal')

    <!-- Edit Testimonial Modal -->
    @include('admin.settings.testimonials.partials.edit-modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Wait for jQuery to be available
        (function() {
            function initTestimonials() {
                if (typeof jQuery === 'undefined') {
                    setTimeout(initTestimonials, 100);
                    return;
                }
                
                jQuery(document).ready(function($) {
            let selectedTestimonials = [];

            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Apply filters
            function applyFilters() {
                const search = $('#search').val();
                const status = $('#status').val();
                const sort = $('#sort').val();
                const direction = $('#direction').val();

                let url = '{{ route('admin.settings.testimonials.index') }}?';
                if (search) url += `search=${encodeURIComponent(search)}&`;
                if (status) url += `status=${status}&`;
                if (sort) url += `sort=${sort}&`;
                if (direction) url += `direction=${direction}&`;

                window.location.href = url;
            }

            // Attach filter events
            $('#search, #status, #sort, #direction').on('input change', debounce(applyFilters, 500));

            // Select all functionality
            $('#select_all').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.testimonial-checkbox').prop('checked', isChecked);
                updateSelectedTestimonials();
            });

            // Individual checkbox functionality
            $(document).on('change', '.testimonial-checkbox', function() {
                updateSelectedTestimonials();
            });

            function updateSelectedTestimonials() {
                selectedTestimonials = $('.testimonial-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                const hasSelection = selectedTestimonials.length > 0;
                $('.bulk-action-btn').prop('disabled', !hasSelection);

                // Update select all checkbox
                const totalCheckboxes = $('.testimonial-checkbox').length;
                const checkedCheckboxes = $('.testimonial-checkbox:checked').length;
                $('#select_all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes <
                    totalCheckboxes);
                $('#select_all').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
            }

            // Bulk actions
            $('.bulk-action-btn').on('click', function() {
                const action = $(this).data('action');

                if (selectedTestimonials.length === 0) {
                    toastr.warning('{{ custom_trans('Please select testimonials first', 'admin') }}');
                    return;
                }

                let confirmMessage = '';
                switch (action) {
                    case 'activate':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to activate the selected testimonials?', 'admin') }}';
                        break;
                    case 'deactivate':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to deactivate the selected testimonials?', 'admin') }}';
                        break;
                    case 'delete':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to delete the selected testimonials? This action cannot be undone.', 'admin') }}';
                        break;
                }

                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: '{{ route('admin.settings.testimonials.bulk-action') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            action: action,
                            testimonials: selectedTestimonials
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error(
                                '{{ custom_trans('An error occurred while performing the bulk action', 'admin') }}'
                            );
                        }
                    });
                }
            });

            // Status toggle
            $(document).on('change', '.status-toggle', function() {
                const testimonialId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/settings/testimonials/${testimonialId}/toggle-status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ custom_trans('An error occurred while updating the status', 'admin') }}');
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Delete testimonial
            $(document).on('click', '.delete-testimonial-btn', function() {
                const testimonialId = $(this).data('id');

                if (confirm('{{ custom_trans('Are you sure you want to delete this testimonial?', 'admin') }}')) {
                    $.ajax({
                        url: `/admin/settings/testimonials/${testimonialId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error(
                                '{{ custom_trans('An error occurred while deleting the testimonial', 'admin') }}'
                            );
                        }
                    });
                }
            });

            // Edit testimonial
            $(document).on('click', '.edit-testimonial-btn', function() {
                const testimonial = $(this).data('testimonial');

                // Populate edit modal
                $('#edit_testimonial_id').val(testimonial.id);
                $('#edit_name').val(testimonial.name);
                $('#edit_name_ar').val(testimonial.name_ar);
                $('#edit_position').val(testimonial.position);
                $('#edit_position_ar').val(testimonial.position_ar);
                $('#edit_company').val(testimonial.company);
                $('#edit_company_ar').val(testimonial.company_ar);
                $('#edit_content').val(testimonial.content);
                $('#edit_content_ar').val(testimonial.content_ar);
                $('#edit_rating').val(testimonial.rating);
                $('#edit_order').val(testimonial.order);
                $('#edit_is_active').prop('checked', testimonial.is_active);

                // Show current avatar
                if (testimonial.avatar) {
                    $('#edit_current_avatar').html(`
                        <img src="/storage/${testimonial.avatar}" alt="Current avatar"
                             class="img-thumbnail max-h-100">
                    `);
                } else {
                    $('#edit_current_avatar').html(
                        '<p class="text-muted">{{ custom_trans('No avatar uploaded', 'admin') }}</p>');
                }

                // Show current voice (file or URL)
                if (testimonial.voice || testimonial.voice_url) {
                    $('#edit_current_voice_container').show();
                    if (testimonial.voice_url) {
                        // Google Drive or external URL
                        $('#edit_current_voice_player').attr('src', testimonial.voice_url);
                        $('#edit_voice_url').val(testimonial.voice_url);
                    } else if (testimonial.voice) {
                        // Uploaded file
                        $('#edit_current_voice_player').attr('src', `/storage/${testimonial.voice}`);
                    }
                    $('#edit_remove_voice').prop('checked', false);
                } else {
                    $('#edit_current_voice_container').hide();
                    $('#edit_current_voice_player').attr('src', '');
                }

                // Clear file inputs
                $('#edit_voice').val('');
                $('#edit_voice_url').val('');
                $('#edit_avatar').val('');

                $('#editTestimonialModal').modal('show');
            });

            // Drag and drop reordering
            const tbody = document.querySelector('#testimonialsTable tbody');
            if (tbody) {
                new Sortable(tbody, {
                    handle: 'td:nth-child(2)',
                    animation: 150,
                    onEnd: function(evt) {
                        const testimonials = [];
                        $('#testimonialsTable tbody tr').each(function(index) {
                            const id = $(this).data('id');
                            if (id) {
                                testimonials.push({
                                    id: id,
                                    order: index + 1
                                });
                            }
                        });

                        $.ajax({
                            url: '{{ route('admin.settings.testimonials.update-order') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                testimonials: testimonials
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error(
                                    '{{ custom_trans('An error occurred while updating the order', 'admin') }}'
                                );
                            }
                        });
                    }
                });
            }

            // Add testimonial form submission
            $('#addTestimonialForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Debug: Log form data
                console.log('Submitting testimonial form...');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
                }

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
                                toastr.error(response.message || '{{ custom_trans('An error occurred while saving the testimonial', 'admin') }}');
                            }
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        console.error('Testimonial save error:', xhr);
                        
                        // Handle validation errors (422 status)
                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                    toastr.error(xhr.responseJSON.errors[key][0]);
                                });
                            } else if (xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            }
                        } 
                        // Handle custom validation errors
                        else if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        }
                        // Handle Laravel validation errors
                        else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        } 
                        else {
                            toastr.error(
                                '{{ custom_trans('An error occurred while saving the testimonial', 'admin') }}'
                            );
                        }
                        submitBtn.prop('disabled', false).html(originalText);
                    },
                    complete: function() {
                        // Re-enable button and restore original text if not already done
                        if (!submitBtn.prop('disabled')) {
                            submitBtn.html(originalText);
                        }
                    }
                });
            });

            // Edit testimonial form submission
            $('#editTestimonialForm').on('submit', function(e) {
                e.preventDefault();

                const testimonialId = $('#edit_testimonial_id').val();
                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ custom_trans('Updating...', 'admin') }}');

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
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        console.error('Testimonial update error:', xhr);
                        
                        // Handle validation errors (422 status)
                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                    toastr.error(xhr.responseJSON.errors[key][0]);
                                });
                            } else if (xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            }
                        } 
                        // Handle custom validation errors
                        else if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        }
                        // Handle Laravel validation errors
                        else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        } 
                        else {
                            toastr.error(
                                '{{ custom_trans('An error occurred while updating the testimonial', 'admin') }}'
                            );
                        }
                        submitBtn.prop('disabled', false).html(originalText);
                    },
                    complete: function() {
                        // Re-enable button and restore original text if not already done
                        if (!submitBtn.prop('disabled')) {
                            submitBtn.html(originalText);
                        }
                    }
                });
            });
                });
            }
            initTestimonials();
        })();
    </script>
@endpush
