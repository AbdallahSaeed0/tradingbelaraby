@extends('layouts.app')

@section('title', 'Notifications - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('notifications') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('manage_your_notifications') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Notifications Content -->
    <section class="notifications-content py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Header Actions -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-0">{{ custom_trans('all_notifications') }}</h4>
                            <small class="text-muted">{{ $notifications->total() }}
                                {{ custom_trans('notifications_found') }}</small>
                        </div>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <button class="btn btn-outline-primary mark-all-read-btn">
                                <i class="fa fa-check-double me-2"></i>{{ custom_trans('mark_all_read') }}
                            </button>
                        @endif
                    </div>

                    @if ($notifications->count() > 0)
                        <div class="notifications-list">
                            @foreach ($notifications as $notification)
                                <div class="notification-item bg-white rounded-3 shadow-sm p-4 mb-3 {{ $notification->read_at ? '' : 'border-start border-primary border-4' }}"
                                    data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                @if (!$notification->read_at)
                                                    <span class="badge bg-success me-2">{{ custom_trans('new') }}</span>
                                                @endif
                                                <small
                                                    class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-2">
                                                {{ $notification->data['message'] ?? 'Notification message' }}</p>

                                            @if (isset($notification->data['course_id']))
                                                <a href="{{ route('courses.show', $notification->data['course_id']) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye me-1"></i>{{ custom_trans('view_course') }}
                                                </a>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <button class="btn btn-sm btn-outline-danger delete-notification-btn"
                                                data-notification-id="{{ $notification->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($notifications->hasPages())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <nav aria-label="Notifications pagination">
                                        {{ $notifications->links() }}
                                    </nav>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-bell fa-4x text-muted mb-4"></i>
                            <h3 class="fw-bold mb-3">{{ custom_trans('no_notifications') }}</h3>
                            <p class="text-muted mb-4">{{ custom_trans('no_notifications_message') }}</p>
                            <a href="{{ route('categories') }}" class="btn btn-primary">
                                <i class="fa fa-search me-2"></i>{{ custom_trans('browse_courses') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Mark all as read functionality
        document.addEventListener('DOMContentLoaded', function() {
            const markAllReadBtn = document.querySelector('.mark-all-read-btn');
            const deleteButtons = document.querySelectorAll('.delete-notification-btn');

            // Mark all as read
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function() {
                    fetch('{{ route('notifications.markAllRead') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove "new" badges and border
                                document.querySelectorAll('.notification-item').forEach(item => {
                                    item.classList.remove('border-start', 'border-primary',
                                        'border-4');
                                    const badge = item.querySelector('.badge');
                                    if (badge) badge.remove();
                                });

                                // Hide the mark all read button
                                markAllReadBtn.style.display = 'none';

                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });
                });
            }

            // Delete notification
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('{{ custom_trans('delete_notification_confirm') }}')) {
                        const notificationId = this.dataset.notificationId;

                        fetch(`/notifications/${notificationId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove the notification item from the page
                                    this.closest('.notification-item').remove();

                                    // Check if no more notifications
                                    if (document.querySelectorAll('.notification-item')
                                        .length === 0) {
                                        location.reload(); // Reload to show empty state
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                            });
                    }
                });
            });
        });
    </script>
@endpush

