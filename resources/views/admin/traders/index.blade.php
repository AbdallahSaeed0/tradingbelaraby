@extends('admin.layout')

@section('title', 'Traders Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">{{ custom_trans('Traders Management', 'admin') }}</h1>
                        <p class="text-muted">Manage trader registrations and view their information.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary fs-6">{{ $traders->total() }} {{ custom_trans('Total Traders', 'admin') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.traders.index') }}" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">{{ custom_trans('Search', 'admin') }}</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search', 'admin') }}"
                                    placeholder="{{ custom_trans('Search by name, email, phone...', 'admin') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>{{ custom_trans('Filter', 'admin') }}
                                </button>
                                <a href="{{ route('admin.traders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>{{ custom_trans('Clear', 'admin') }}
                                </a>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a href="{{ route('admin.traders.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                    class="btn btn-success">
                                    <i class="fas fa-download me-1"></i>{{ custom_trans('Export CSV', 'admin') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-4 d-none-initially" id="bulkActions">
            <div class="col-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-warning" id="selectedCount">0</span> {{ custom_trans('traders selected', 'admin') }}
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                                    <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete Selected', 'admin') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="clearSelection">
                                    <i class="fas fa-times me-1"></i>{{ custom_trans('Clear Selection', 'admin') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traders Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        @if ($traders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>{{ custom_trans('Name', 'admin') }}</th>
                                            <th>{{ custom_trans('Email', 'admin') }}</th>
                                            <th>{{ custom_trans('Phone', 'admin') }}</th>
                                            <th>{{ custom_trans('Gender', 'admin') }}</th>
                                            <th>{{ custom_trans('Trading Community', 'admin') }}</th>
                                            <th>{{ custom_trans('Languages', 'admin') }}</th>
                                            <th>{{ custom_trans('Registered', 'admin') }}</th>
                                            <th class="text-center">{{ custom_trans('Actions', 'admin') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($traders as $trader)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input trader-checkbox"
                                                        value="{{ $trader->id }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center w-40 h-40">
                                                            <i class="fa fa-user text-white"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $trader->name }}</h6>
                                                            <small class="text-muted">{{ $trader->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $trader->email }}</td>
                                                <td>
                                                    @if ($trader->phone_number)
                                                        <span class="badge bg-info">{{ $trader->phone_number }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst($trader->sex) }}</span>
                                                </td>
                                                <td>
                                                    @if ($trader->trading_community)
                                                        <span
                                                            class="badge bg-success">{{ $trader->trading_community }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <small class="fw-bold">{{ $trader->first_language }}</small>
                                                        @if ($trader->second_language)
                                                            <br><small
                                                                class="text-muted">{{ $trader->second_language }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $trader->created_at->format('M d, Y', 'admin') }}<br>
                                                        {{ $trader->created_at->format('H:i', 'admin') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.traders.show', $trader) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="{{ custom_trans('View Details', 'admin') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form action="{{ route('admin.traders.destroy', $trader) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('{{ custom_trans('Are you sure you want to delete this trader?', 'admin') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                title="{{ custom_trans('Delete', 'admin') }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">{{ custom_trans('No traders found', 'admin') }}</h4>
                                <p class="text-muted">{{ custom_trans('No trader registrations match your search criteria.', 'admin') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($traders->hasPages())
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-center">
                    {{ $traders->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" action="{{ route('admin.traders.bulk-delete') }}" method="POST" class="d-none-initially">
        @csrf
        @method('DELETE')
        <input type="hidden" name="trader_ids" id="selectedTraderIds">
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Select All functionality
            $('#selectAll').on('change', function() {
                $('.trader-checkbox').prop('checked', this.checked);
                updateBulkActions();
            });

            // Individual checkbox change
            $('.trader-checkbox').on('change', function() {
                updateBulkActions();
                updateSelectAll();
            });

            // Update bulk actions visibility and state
            function updateBulkActions() {
                const selectedCount = $('.trader-checkbox:checked').length;
                const bulkActions = $('#bulkActions');
                const bulkDeleteBtn = $('#bulkDeleteBtn');
                const selectedCountSpan = $('#selectedCount');

                if (selectedCount > 0) {
                    bulkActions.show();
                    bulkDeleteBtn.prop('disabled', false);
                    selectedCountSpan.text(selectedCount);
                } else {
                    bulkActions.hide();
                    bulkDeleteBtn.prop('disabled', true);
                }
            }

            // Update select all checkbox state
            function updateSelectAll() {
                const totalCheckboxes = $('.trader-checkbox').length;
                const checkedCheckboxes = $('.trader-checkbox:checked').length;
                const selectAll = $('#selectAll');

                if (checkedCheckboxes === 0) {
                    selectAll.prop('indeterminate', false).prop('checked', false);
                } else if (checkedCheckboxes === totalCheckboxes) {
                    selectAll.prop('indeterminate', false).prop('checked', true);
                } else {
                    selectAll.prop('indeterminate', true);
                }
            }

            // Clear selection
            $('#clearSelection').on('click', function() {
                $('.trader-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false).prop('indeterminate', false);
                updateBulkActions();
            });

            // Bulk delete
            $('#bulkDeleteBtn').on('click', function() {
                const selectedIds = $('.trader-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    alert('{{ custom_trans('Please select traders to delete.', 'admin') }}');
                    return;
                }

                if (confirm(
                        '{{ custom_trans('Are you sure you want to delete the selected traders? This action cannot be undone.', 'admin') }}'
                    )) {
                    $('#selectedTraderIds').val(JSON.stringify(selectedIds));
                    $('#bulkDeleteForm').submit();
                }
            });
        });
    </script>
@endpush
