@extends('admin.layout')

@section('title', 'Traders Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">{{ __('Traders Management') }}</h1>
                        <p class="text-muted">Manage trader registrations and view their information.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary fs-6">{{ $traders->total() }} {{ __('Total Traders') }}</span>
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
                                <label for="search" class="form-label">{{ __('Search') }}</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="{{ __('Search by name, email, phone...') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>{{ __('Filter') }}
                                </button>
                                <a href="{{ route('admin.traders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>{{ __('Clear') }}
                                </a>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a href="{{ route('admin.traders.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                    class="btn btn-success">
                                    <i class="fas fa-download me-1"></i>{{ __('Export CSV') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-4" id="bulkActions" style="display: none;">
            <div class="col-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-warning" id="selectedCount">0</span> {{ __('traders selected') }}
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                                    <i class="fas fa-trash me-1"></i>{{ __('Delete Selected') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="clearSelection">
                                    <i class="fas fa-times me-1"></i>{{ __('Clear Selection') }}
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
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Gender') }}</th>
                                            <th>{{ __('Trading Community') }}</th>
                                            <th>{{ __('Languages') }}</th>
                                            <th>{{ __('Registered') }}</th>
                                            <th class="text-center">{{ __('Actions') }}</th>
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
                                                        <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px;">
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
                                                        {{ $trader->created_at->format('M d, Y') }}<br>
                                                        {{ $trader->created_at->format('H:i') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.traders.show', $trader) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="{{ __('View Details') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form action="{{ route('admin.traders.destroy', $trader) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this trader?') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                title="{{ __('Delete') }}">
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
                                <h4 class="text-muted">{{ __('No traders found') }}</h4>
                                <p class="text-muted">{{ __('No trader registrations match your search criteria.') }}</p>
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
    <form id="bulkDeleteForm" action="{{ route('admin.traders.bulk-delete') }}" method="POST" style="display: none;">
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
                    alert('{{ __('Please select traders to delete.') }}');
                    return;
                }

                if (confirm(
                        '{{ __('Are you sure you want to delete the selected traders? This action cannot be undone.') }}'
                    )) {
                    $('#selectedTraderIds').val(JSON.stringify(selectedIds));
                    $('#bulkDeleteForm').submit();
                }
            });
        });
    </script>
@endpush
