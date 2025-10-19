@extends('admin.layout')

@section('title', __('Testimonials Management'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('Testimonials Management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Testimonials') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addTestimonialModal">
                            <i class="fas fa-plus me-2"></i>{{ __('Add Testimonial') }}
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
                            <label for="search" class="form-label">{{ __('Search') }}</label>
                            <input type="text" class="form-control" id="search"
                                placeholder="{{ __('Search testimonials...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="status">
                                <option value="">{{ __('All') }}</option>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sort" class="form-label">{{ __('Sort By') }}</label>
                            <select class="form-select" id="sort">
                                <option value="order">{{ __('Order') }}</option>
                                <option value="name">{{ __('Name') }}</option>
                                <option value="created_at">{{ __('Created Date') }}</option>
                                <option value="rating">{{ __('Rating') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="direction" class="form-label">{{ __('Direction') }}</label>
                            <select class="form-select" id="direction">
                                <option value="asc">{{ __('Ascending') }}</option>
                                <option value="desc">{{ __('Descending') }}</option>
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
                            <i class="fas fa-quote-left text-primary me-2"></i>{{ __('All Testimonials') }}
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success bulk-action-btn" data-action="activate"
                                disabled>
                                <i class="fas fa-check me-1"></i>{{ __('Activate') }}
                            </button>
                            <button type="button" class="btn btn-outline-warning bulk-action-btn" data-action="deactivate"
                                disabled>
                                <i class="fas fa-pause me-1"></i>{{ __('Deactivate') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger bulk-action-btn" data-action="delete"
                                disabled>
                                <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
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
                                <th width="80">{{ __('Order') }}</th>
                                <th width="80">{{ __('Avatar') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Name (AR)') }}</th>
                                <th>{{ __('Position') }}</th>
                                <th>{{ __('Company') }}</th>
                                <th>{{ __('Content') }}</th>
                                <th width="80">{{ __('Rating') }}</th>
                                <th width="100">{{ __('Status') }}</th>
                                <th width="120">{{ __('Actions') }}</th>
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
                                            <p>{{ __('No testimonials found') }}</p>
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

@push('styles')
    <style>
        /* Enhanced Page Header */
        .page-title-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .page-title-box .page-title {
            color: white;
            margin-bottom: 0.5rem;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item.active {
            color: white;
        }

        /* Content Cards */
        .content-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .content-card .card-header {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px 15px 0 0;
        }

        .content-card .card-body {
            background: white;
            border-radius: 0 0 15px 15px;
        }

        /* Table Styling */
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        /* Button Styling */
        .btn {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Badge Styling */
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
        }

        /* Form Styling */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
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
                    toastr.warning('{{ __('Please select testimonials first') }}');
                    return;
                }

                let confirmMessage = '';
                switch (action) {
                    case 'activate':
                        confirmMessage =
                            '{{ __('Are you sure you want to activate the selected testimonials?') }}';
                        break;
                    case 'deactivate':
                        confirmMessage =
                            '{{ __('Are you sure you want to deactivate the selected testimonials?') }}';
                        break;
                    case 'delete':
                        confirmMessage =
                            '{{ __('Are you sure you want to delete the selected testimonials? This action cannot be undone.') }}';
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
                                '{{ __('An error occurred while performing the bulk action') }}'
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
                            '{{ __('An error occurred while updating the status') }}');
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Delete testimonial
            $(document).on('click', '.delete-testimonial-btn', function() {
                const testimonialId = $(this).data('id');

                if (confirm('{{ __('Are you sure you want to delete this testimonial?') }}')) {
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
                                '{{ __('An error occurred while deleting the testimonial') }}'
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
                        '<p class="text-muted">{{ __('No avatar uploaded') }}</p>');
                }

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
                                    '{{ __('An error occurred while updating the order') }}'
                                );
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush
