@extends('admin.layout')

@section('title', 'Payment Settings')

@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <div class="page-title-content">
                <h4 class="page-title">Payment Settings</h4>
                <p class="text-muted mb-0">Configure payment methods available to students</p>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                    <li class="breadcrumb-item active">Payment Settings</li>
                </ol>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-university me-2 text-primary"></i>
                        <h5 class="card-title mb-0">Bank Transfer Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.payment.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Enable / disable --}}
                            <div class="mb-4">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" id="bank_transfer_enabled"
                                        name="bank_transfer_enabled" value="1"
                                        {{ $settings->bank_transfer_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="bank_transfer_enabled">
                                        Enable Bank Transfer Payment
                                    </label>
                                </div>
                                <div class="form-text text-muted">
                                    When enabled, students can choose bank transfer as a payment option. Their enrollment
                                    will be <strong>pending</strong> until you confirm the transfer.
                                </div>
                            </div>

                            <hr>

                            {{-- Bank name --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="bank_transfer_bank_name">
                                    Bank Name
                                </label>
                                <input type="text" class="form-control @error('bank_transfer_bank_name') is-invalid @enderror"
                                    id="bank_transfer_bank_name" name="bank_transfer_bank_name"
                                    value="{{ old('bank_transfer_bank_name', $settings->bank_transfer_bank_name) }}"
                                    placeholder="e.g. Al Rajhi Bank">
                                @error('bank_transfer_bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Account name --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="bank_transfer_account_name">
                                    Account Holder Name
                                </label>
                                <input type="text" class="form-control @error('bank_transfer_account_name') is-invalid @enderror"
                                    id="bank_transfer_account_name" name="bank_transfer_account_name"
                                    value="{{ old('bank_transfer_account_name', $settings->bank_transfer_account_name) }}"
                                    placeholder="e.g. Tadawul Bel Araby Academy">
                                @error('bank_transfer_account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Account number --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="bank_transfer_account_number">
                                    Account Number
                                </label>
                                <input type="text" class="form-control @error('bank_transfer_account_number') is-invalid @enderror"
                                    id="bank_transfer_account_number" name="bank_transfer_account_number"
                                    value="{{ old('bank_transfer_account_number', $settings->bank_transfer_account_number) }}"
                                    placeholder="e.g. 1234567890">
                                @error('bank_transfer_account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- IBAN --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="bank_transfer_iban">
                                    IBAN <span class="text-muted fw-normal">(optional)</span>
                                </label>
                                <input type="text" class="form-control @error('bank_transfer_iban') is-invalid @enderror"
                                    id="bank_transfer_iban" name="bank_transfer_iban"
                                    value="{{ old('bank_transfer_iban', $settings->bank_transfer_iban) }}"
                                    placeholder="e.g. SA0380000000608010167519">
                                @error('bank_transfer_iban')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Instructions --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold" for="bank_transfer_instructions">
                                    Additional Instructions <span class="text-muted fw-normal">(optional)</span>
                                </label>
                                <textarea class="form-control @error('bank_transfer_instructions') is-invalid @enderror"
                                    id="bank_transfer_instructions" name="bank_transfer_instructions"
                                    rows="3"
                                    placeholder="Any extra notes for students about the transfer process...">{{ old('bank_transfer_instructions', $settings->bank_transfer_instructions) }}</textarea>
                                @error('bank_transfer_instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Settings
                                </button>
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Preview card --}}
            <div class="col-xl-4">
                <div class="card border-info">
                    <div class="card-header bg-info bg-opacity-10 border-info">
                        <h6 class="card-title mb-0 text-info">
                            <i class="fas fa-eye me-1"></i> How it looks to students
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            When a student selects <strong>Bank Transfer</strong> and clicks Place Order, they will see:
                        </p>
                        <div class="border rounded p-3 bg-light small">
                            <p class="fw-semibold mb-1"><i class="fas fa-university me-1"></i> Bank Transfer Details</p>
                            <table class="table table-sm table-borderless mb-2">
                                <tr>
                                    <td class="text-muted ps-0">Bank</td>
                                    <td class="fw-semibold" id="preview_bank_name">—</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Account Name</td>
                                    <td class="fw-semibold" id="preview_account_name">—</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Account No.</td>
                                    <td class="fw-semibold" id="preview_account_number">—</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">IBAN</td>
                                    <td class="fw-semibold" id="preview_iban">—</td>
                                </tr>
                            </table>
                            <p class="text-muted small mb-0" id="preview_instructions">—</p>
                        </div>
                        <p class="small text-muted mt-3 mb-0">
                            The student must enter their <strong>transaction reference number</strong> before submitting.
                            The enrollment stays <span class="badge bg-warning text-dark">Pending</span> until you
                            confirm it in <a href="{{ route('admin.enrollments.index') }}">Enrollments</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function updatePreview() {
        document.getElementById('preview_bank_name').textContent    = document.getElementById('bank_transfer_bank_name').value    || '—';
        document.getElementById('preview_account_name').textContent = document.getElementById('bank_transfer_account_name').value || '—';
        document.getElementById('preview_account_number').textContent = document.getElementById('bank_transfer_account_number').value || '—';
        document.getElementById('preview_iban').textContent         = document.getElementById('bank_transfer_iban').value         || '—';
        document.getElementById('preview_instructions').textContent = document.getElementById('bank_transfer_instructions').value || '—';
    }

    ['bank_transfer_bank_name', 'bank_transfer_account_name', 'bank_transfer_account_number', 'bank_transfer_iban', 'bank_transfer_instructions']
        .forEach(id => document.getElementById(id).addEventListener('input', updatePreview));

    updatePreview();
</script>
@endpush
