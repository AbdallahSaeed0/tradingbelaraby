@extends('admin.layout')

@section('title', 'Live Class Registrations')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Registrations for: {{ $liveClass->title }}
                            </h4>
                            <div>
                                <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Live Classes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Live Class Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Live Class Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Title:</strong></td>
                                        <td>{{ $liveClass->title }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Course:</strong></td>
                                        <td>{{ $liveClass->course->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Instructor:</strong></td>
                                        <td>{{ $liveClass->instructor->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date & Time:</strong></td>
                                        <td>{{ $liveClass->scheduled_at ? $liveClass->scheduled_at->format('F j, Y g:i A') : 'TBD' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Duration:</strong></td>
                                        <td>{{ $liveClass->duration ?? 'N/A' }} minutes</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if ($liveClass->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($liveClass->status == 'completed')
                                                <span class="badge bg-secondary">Completed</span>
                                            @elseif($liveClass->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-warning">Draft</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Registration Statistics</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h3>{{ $registrations->count() }}</h3>
                                                <p class="mb-0">Total Registrations</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h3>{{ $registrations->where('attended', true)->count() }}</h3>
                                                <p class="mb-0">Attended</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registrations Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Attended</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($registrations as $index => $registration)
                                        @if ($registration->user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($registration->user->profile_photo)
                                                            <img src="{{ $registration->user->profile_photo_url }}"
                                                                alt="{{ $registration->user->name }}"
                                                                class="rounded-circle me-2" width="40" height="40">
                                                        @else
                                                            <div
                                                                class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2 w-40 h-40">
                                                                <i class="fas fa-user text-white"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <strong>{{ $registration->user->name }}</strong>
                                                            @if ($registration->user->id == $liveClass->instructor_id)
                                                                <span class="badge bg-info ms-1">Instructor</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $registration->user->email }}</td>
                                                <td>{{ $registration->created_at->format('M j, Y g:i A') }}</td>
                                                <td>
                                                    @if ($registration->status == 'confirmed')
                                                        <span class="badge bg-success">Confirmed</span>
                                                    @elseif($registration->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($registration->status == 'cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst($registration->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($registration->attended)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Yes
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times me-1"></i>No
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            Actions
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="toggleAttendance({{ $registration->id }})">
                                                                    <i class="fas fa-user-check me-2"></i>
                                                                    {{ $registration->attended ? 'Mark as Not Attended' : 'Mark as Attended' }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="updateStatus({{ $registration->id }}, 'confirmed')">
                                                                    <i class="fas fa-check-circle me-2"></i>Confirm
                                                                    Registration
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="updateStatus({{ $registration->id }}, 'cancelled')">
                                                                    <i class="fas fa-times-circle me-2"></i>Cancel
                                                                    Registration
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#"
                                                                    onclick="deleteRegistration({{ $registration->id }})">
                                                                    <i class="fas fa-trash me-2"></i>Delete Registration
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2 w-40 h-40">
                                                            <i class="fas fa-user-slash text-white"></i>
                                                        </div>
                                                        <div>
                                                            <strong class="text-danger">Student Deleted</strong>
                                                            <small class="text-muted d-block">Registration ID:
                                                                {{ $registration->id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-muted">N/A</td>
                                                <td>{{ $registration->created_at->format('M j, Y g:i A') }}</td>
                                                <td>
                                                    @if ($registration->status == 'confirmed')
                                                        <span class="badge bg-success">Confirmed</span>
                                                    @elseif($registration->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($registration->status == 'cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst($registration->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($registration->attended)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Yes
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times me-1"></i>No
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            Actions
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#"
                                                                    onclick="deleteRegistration({{ $registration->id }})">
                                                                    <i class="fas fa-trash me-2"></i>Delete Registration
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-users fa-3x mb-3"></i>
                                                    <h5>No registrations found</h5>
                                                    <p>No students have registered for this live class yet.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Export Options -->
                        @if ($registrations->count() > 0)
                            <div class="mt-4">
                                <h5>Export Options</h5>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.live-classes.registrations', $liveClass) }}?export=csv"
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-download me-2"></i>Export as CSV
                                    </a>
                                    <a href="{{ route('admin.live-classes.registrations', $liveClass) }}?export=pdf"
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-file-pdf me-2"></i>Export as PDF
                                    </a>
                                    <a href="{{ route('admin.live-classes.registrations', $liveClass) }}?export=excel"
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-file-excel me-2"></i>Export as Excel
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentAction = null;
        let currentRegistrationId = null;

        function toggleAttendance(registrationId) {
            currentAction = 'attendance';
            currentRegistrationId = registrationId;
            document.getElementById('confirmationMessage').textContent =
                'Are you sure you want to toggle the attendance status for this registration?';
            new bootstrap.Modal(document.getElementById('confirmationModal')).show();
        }

        function updateStatus(registrationId, status) {
            currentAction = 'status';
            currentRegistrationId = registrationId;
            const statusText = status === 'confirmed' ? 'confirm' : 'cancel';
            document.getElementById('confirmationMessage').textContent =
                `Are you sure you want to ${statusText} this registration?`;
            new bootstrap.Modal(document.getElementById('confirmationModal')).show();
        }

        function deleteRegistration(registrationId) {
            currentAction = 'delete';
            currentRegistrationId = registrationId;
            document.getElementById('confirmationMessage').textContent =
                'Are you sure you want to delete this registration? This action cannot be undone.';
            new bootstrap.Modal(document.getElementById('confirmationModal')).show();
        }

        document.getElementById('confirmAction').addEventListener('click', function() {
            if (!currentAction || !currentRegistrationId) return;

            let url = '';
            let method = 'POST';
            let data = {};

            switch (currentAction) {
                case 'attendance':
                    url = `/admin/live-classes/registrations/${currentRegistrationId}/toggle-attendance`;
                    break;
                case 'status':
                    url = `/admin/live-classes/registrations/${currentRegistrationId}/update-status`;
                    data = {
                        status: 'confirmed'
                    }; // You might want to pass the actual status
                    break;
                case 'delete':
                    url = `/admin/live-classes/registrations/${currentRegistrationId}`;
                    method = 'DELETE';
                    break;
            }

            fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: method !== 'DELETE' ? JSON.stringify(data) : null
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Something went wrong'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request.');
                })
                .finally(() => {
                    bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
                });
        });
    </script>
@endpush
