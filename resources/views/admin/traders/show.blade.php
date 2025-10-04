@extends('admin.layout')

@section('title', 'Trader Details')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">{{ __('Trader Details') }}</h1>
                        <p class="text-muted">View detailed information about {{ $trader->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.traders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Personal Information -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>{{ __('Personal Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Full Name') }}</label>
                                <p class="mb-0">{{ $trader->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Gender') }}</label>
                                <p class="mb-0">
                                    <span class="badge bg-secondary">{{ ucfirst($trader->sex) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Birth Date') }}</label>
                                <p class="mb-0">{{ $trader->formatted_birthdate }}</p>
                                @if ($trader->age)
                                    <small class="text-muted">({{ $trader->age }} years old)</small>
                                @endif
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Email Address') }}</label>
                                <p class="mb-0">
                                    <a href="mailto:{{ $trader->email }}" class="text-primary">{{ $trader->email }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-phone me-2"></i>{{ __('Contact Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Phone Number') }}</label>
                                <p class="mb-0">
                                    @if ($trader->phone_number)
                                        <a href="tel:{{ $trader->phone_number }}"
                                            class="text-primary">{{ $trader->phone_number }}</a>
                                    @else
                                        <span class="text-muted">{{ __('Not provided') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('WhatsApp Number') }}</label>
                                <p class="mb-0">
                                    @if ($trader->whatsapp_number)
                                        <a href="https://wa.me/{{ $trader->whatsapp_number }}" target="_blank"
                                            class="text-success">
                                            {{ $trader->whatsapp_number }}
                                            <i class="fab fa-whatsapp ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('Not provided') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Trading Community') }}</label>
                                <p class="mb-0">
                                    @if ($trader->trading_community)
                                        <span class="badge bg-success">{{ $trader->trading_community }}</span>
                                    @else
                                        <span class="text-muted">{{ __('Not specified') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-briefcase me-2"></i>{{ __('Professional Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('LinkedIn Profile') }}</label>
                                <p class="mb-0">
                                    @if ($trader->linkedin)
                                        <a href="{{ $trader->linkedin }}" target="_blank" class="text-primary">
                                            {{ $trader->linkedin }}
                                            <i class="fab fa-linkedin ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('Not provided') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Website') }}</label>
                                <p class="mb-0">
                                    @if ($trader->website)
                                        <a href="{{ $trader->website }}" target="_blank" class="text-primary">
                                            {{ $trader->website }}
                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('Not provided') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Primary Language') }}</label>
                                <p class="mb-0">{{ $trader->first_language }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Secondary Language') }}</label>
                                <p class="mb-0">
                                    @if ($trader->second_language)
                                        {{ $trader->second_language }}
                                    @else
                                        <span class="text-muted">{{ __('Not specified') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Experience & Availability -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>{{ __('Experience & Availability') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Available Appointments') }}</label>
                                <p class="mb-0">
                                    @if ($trader->available_appointments)
                                        {{ $trader->available_appointments }}
                                    @else
                                        <span class="text-muted">{{ __('Not specified') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Status') }}</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $trader->is_active ? 'success' : 'danger' }} fs-6">
                                        {{ $trader->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificates -->
            @if ($trader->certificates)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-certificate me-2"></i>{{ __('Certificates') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $trader->certificates }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Trading Experience -->
            @if ($trader->trading_experience)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>{{ __('Trading Experience') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $trader->trading_experience }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Training Experience -->
            @if ($trader->training_experience)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-purple text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chalkboard-teacher me-2"></i>{{ __('Training Experience') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $trader->training_experience }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Comments -->
            @if ($trader->comments)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-comments me-2"></i>{{ __('Comments & Questions') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $trader->comments }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Registration Info -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('Registration Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Registration Date') }}</label>
                                <p class="mb-0">{{ $trader->created_at->format('F d, Y') }} at
                                    {{ $trader->created_at->format('H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Last Updated') }}</label>
                                <p class="mb-0">{{ $trader->updated_at->format('F d, Y') }} at
                                    {{ $trader->updated_at->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <form action="{{ route('admin.traders.toggle-status', $trader) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $trader->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $trader->is_active ? 'ban' : 'check' }} me-1"></i>
                                {{ $trader->is_active ? __('Deactivate') : __('Activate') }}
                            </button>
                        </form>
                    </div>
                    <div>
                        <form action="{{ route('admin.traders.destroy', $trader) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this trader?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i>{{ __('Delete Trader') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }
    </style>
@endsection
