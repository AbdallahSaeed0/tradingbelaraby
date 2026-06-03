@extends('layouts.app')

@section('title', 'Notifications - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@push('styles')
<style>
/* ── Notifications Page ──────────────────────────────────────────────────── */

/* Hero */
.notif-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 0 48px;
    color: white;
    text-align: center;
}

.notif-hero h1 {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 8px;
}

.notif-hero p {
    font-size: 1rem;
    opacity: 0.85;
    margin: 0;
}

/* Page header row */
.notif-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
}

.notif-page-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 2px;
}

.notif-page-count {
    font-size: 0.85rem;
    color: #6b7280;
}

/* Mark all button */
.btn-mark-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 10px;
    border: 2px solid #7c3aed;
    color: #7c3aed;
    background: transparent;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-mark-all:hover {
    background: #7c3aed;
    color: white;
}

/* ── Notification Card ───────────────────────────────────────────────────── */
.notif-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1.5px solid #f3f4f6;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    padding: 18px 20px;
    margin-bottom: 14px;
    transition: box-shadow 0.2s, opacity 0.35s, transform 0.35s;
    position: relative;
    overflow: hidden;
}

.notif-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,0.09);
}

/* Unread accent line */
.notif-card.unread::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;          /* RTL: right side */
    width: 4px;
    background: linear-gradient(180deg, #667eea, #764ba2);
    border-radius: 0 14px 14px 0;
}

.notif-card.is-removing {
    opacity: 0;
    transform: scale(0.96) translateY(-6px);
    pointer-events: none;
}

/* Card inner layout */
.notif-card-body {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

/* Type icon */
.notif-type-icon {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

.notif-type-icon.t-primary  { background: #ede9fe; color: #7c3aed; }
.notif-type-icon.t-danger   { background: #fee2e2; color: #dc2626; }
.notif-type-icon.t-warning  { background: #fef9c3; color: #ca8a04; }
.notif-type-icon.t-success  { background: #dcfce7; color: #16a34a; }
.notif-type-icon.t-info     { background: #e0f2fe; color: #0284c7; }

/* Text block */
.notif-text {
    flex: 1;
    min-width: 0;
}

.notif-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 5px;
    flex-wrap: wrap;
}

.notif-badge-new {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #7c3aed;
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
}

.notif-time {
    font-size: 0.78rem;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 4px;
}

.notif-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
    line-height: 1.4;
}

.notif-body {
    font-size: 0.875rem;
    color: #4b5563;
    line-height: 1.55;
    margin-bottom: 10px;
}

/* Action row */
.notif-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-notif-view {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    border: 1.5px solid #7c3aed;
    color: #7c3aed;
    background: transparent;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.18s;
}

.btn-notif-view:hover {
    background: #7c3aed;
    color: white;
    text-decoration: none;
}

.btn-notif-read {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1.5px solid #e5e7eb;
    color: #6b7280;
    background: transparent;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.18s;
}

.btn-notif-read:hover {
    border-color: #16a34a;
    color: #16a34a;
}

/* Right-side controls */
.notif-controls {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.btn-notif-delete {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    border: 1.5px solid #fee2e2;
    color: #dc2626;
    background: #fff5f5;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.18s;
}

.btn-notif-delete:hover {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}

/* Unread dot on controls side */
.notif-unread-pill {
    width: 10px;
    height: 10px;
    background: #7c3aed;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Empty State ─────────────────────────────────────────────────────────── */
.notif-empty {
    text-align: center;
    padding: 80px 20px;
}

.notif-empty-icon {
    width: 90px;
    height: 90px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 2.2rem;
    color: #d1d5db;
}

.notif-empty h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
}

.notif-empty p {
    color: #9ca3af;
    font-size: 0.9rem;
    margin-bottom: 24px;
}

/* ── Pagination ──────────────────────────────────────────────────────────── */
.notif-pagination {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}
</style>
@endpush

@section('content')

{{-- ── Hero ──────────────────────────────────────────────────────────────── --}}
<section class="notif-hero">
    <div class="container">
        <h1>{{ custom_trans('notifications', 'front') }}</h1>
        <p>{{ custom_trans('manage_your_notifications', 'front') }}</p>
    </div>
</section>

{{-- ── Content ──────────────────────────────────────────────────────────── --}}
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @php
                    $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
                    $unreadTotal = auth()->user()->unreadNotifications->count();
                @endphp

                {{-- Page header row --}}
                <div class="notif-page-header">
                    <div>
                        <div class="notif-page-title">{{ custom_trans('all_notifications', 'front') }}</div>
                        <div class="notif-page-count">{{ $notifications->total() }} {{ custom_trans('notifications_found', 'front') }}</div>
                    </div>
                    @if ($unreadTotal > 0)
                        <button class="btn-mark-all" id="markAllReadBtn">
                            <i class="fa fa-check-double"></i>
                            {{ custom_trans('mark_all_read', 'front') }}
                        </button>
                    @endif
                </div>

                {{-- Notification list --}}
                @if ($notifications->count() > 0)
                    <div id="notifList">
                        @foreach ($notifications as $notification)
                            @php
                                $nTitle = \App\Support\NotificationPayload::titleForLocale($notification->data, $locale);
                                $nBody  = \App\Support\NotificationPayload::bodyForLocale($notification->data, $locale)
                                          ?: ($notification->data['message'] ?? '');
                                $nType  = $notification->type;

                                $action      = $notification->data['action'] ?? null;
                                $actionValue = $action['value'] ?? null;
                                $courseId    = $notification->data['course_id']
                                              ?? $notification->data['meta']['course_id']
                                              ?? null;
                                $actionUrl = null;
                                if ($actionValue && in_array($action['type'] ?? '', ['deeplink','url'])) {
                                    $actionUrl = str_starts_with($actionValue, 'http')
                                        ? $actionValue
                                        : url($actionValue);
                                } elseif ($courseId) {
                                    $actionUrl = route('courses.show', $courseId);
                                }

                                if (str_contains($nType, 'Wishlist')) {
                                    $nIcon = 'fas fa-heart'; $nIconClass = 't-danger';
                                } elseif (str_contains($nType, 'Payment') || str_contains($nType, 'Order')) {
                                    $nIcon = 'fas fa-credit-card'; $nIconClass = 't-success';
                                } elseif (str_contains($nType, 'Quiz')) {
                                    $nIcon = 'fas fa-question-circle'; $nIconClass = 't-warning';
                                } elseif (str_contains($nType, 'Live') || str_contains($nType, 'Broadcast')) {
                                    $nIcon = 'fas fa-broadcast-tower'; $nIconClass = 't-info';
                                } elseif (str_contains($nType, 'Course') || str_contains($nType, 'Enrollment')) {
                                    $nIcon = 'fas fa-graduation-cap'; $nIconClass = 't-primary';
                                } else {
                                    $nIcon = 'fas fa-bell'; $nIconClass = 't-info';
                                }
                            @endphp

                            <div class="notif-card {{ $notification->read_at ? '' : 'unread' }}"
                                 data-notification-id="{{ $notification->id }}"
                                 id="notif-{{ $notification->id }}">
                                <div class="notif-card-body">

                                    {{-- Type icon --}}
                                    <div class="notif-type-icon {{ $nIconClass }}">
                                        <i class="{{ $nIcon }}"></i>
                                    </div>

                                    {{-- Main text --}}
                                    <div class="notif-text">
                                        <div class="notif-meta">
                                            @if (!$notification->read_at)
                                                <span class="notif-badge-new">
                                                    <i class="fas fa-circle" style="font-size:6px"></i>
                                                    {{ custom_trans('new', 'front') }}
                                                </span>
                                            @endif
                                            <span class="notif-time">
                                                <i class="far fa-clock"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        @if ($nTitle)
                                            <div class="notif-title">{{ $nTitle }}</div>
                                        @endif

                                        @if ($nBody)
                                            <div class="notif-body">{{ $nBody }}</div>
                                        @endif

                                        <div class="notif-actions">
                                            @if ($actionUrl)
                                                <a href="{{ $actionUrl }}" class="btn-notif-view"
                                                   @if(str_starts_with($actionUrl, 'http')) target="_blank" rel="noopener" @endif>
                                                    <i class="fas fa-external-link-alt"></i>
                                                    {{ custom_trans('view_course', 'front') }}
                                                </a>
                                            @endif

                                            @if (!$notification->read_at)
                                                <button class="btn-notif-read mark-one-read-btn"
                                                        data-notification-id="{{ $notification->id }}">
                                                    <i class="fas fa-check"></i>
                                                    {{ custom_trans('mark_as_read', 'front') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Controls --}}
                                    <div class="notif-controls">
                                        <button class="btn-notif-delete delete-notification-btn"
                                                data-notification-id="{{ $notification->id }}"
                                                title="{{ custom_trans('delete', 'front') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @if (!$notification->read_at)
                                            <div class="notif-unread-pill"></div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($notifications->hasPages())
                        <div class="notif-pagination">
                            {{ $notifications->links() }}
                        </div>
                    @endif

                @else
                    {{-- Empty state --}}
                    <div class="notif-empty">
                        <div class="notif-empty-icon">
                            <i class="far fa-bell-slash"></i>
                        </div>
                        <h3>{{ custom_trans('no_notifications', 'front') }}</h3>
                        <p>{{ custom_trans('no_notifications_message', 'front') }}</p>
                        <a href="{{ route('categories.index') }}" class="btn-notif-view" style="display:inline-flex">
                            <i class="fas fa-search"></i>
                            {{ custom_trans('browse_courses', 'front') }}
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
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function apiFetch(url, method) {
        return fetch(url, {
            method: method || 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
        }).then(r => r.json());
    }

    function removeCard(card) {
        card.classList.add('is-removing');
        setTimeout(() => card.remove(), 380);
    }

    function checkEmpty() {
        if (document.querySelectorAll('.notif-card').length === 0) {
            // Reload to show empty state via server
            location.reload();
        }
    }

    // ── Mark individual as read ─────────────────────────────────────────────
    document.querySelectorAll('.mark-one-read-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id   = this.dataset.notificationId;
            const card = document.getElementById('notif-' + id);

            apiFetch(`/notifications/${id}/mark-as-read`)
                .then(data => {
                    if (data.success) {
                        card.classList.remove('unread');
                        // Remove badge, mark-read btn, unread dot
                        card.querySelectorAll('.notif-badge-new, .mark-one-read-btn, .notif-unread-pill')
                            .forEach(el => el.remove());
                        // Sync bell badge count in header if present
                        syncBellCount(-1);
                        if (typeof toastr !== 'undefined') toastr.success('{{ custom_trans('marked_as_read', 'front') }}');
                    }
                })
                .catch(() => { if (typeof toastr !== 'undefined') toastr.error('{{ custom_trans('error', 'front') }}'); });
        });
    });

    // ── Mark all as read ────────────────────────────────────────────────────
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            apiFetch('{{ route('notifications.markAllRead') }}')
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notif-card.unread').forEach(card => {
                            card.classList.remove('unread');
                            card.querySelectorAll('.notif-badge-new, .mark-one-read-btn, .notif-unread-pill')
                                .forEach(el => el.remove());
                        });
                        markAllBtn.remove();
                        syncBellCount(0, true);
                        if (typeof toastr !== 'undefined') toastr.success('{{ custom_trans('mark_all_read', 'front') }}');
                    }
                })
                .catch(() => { if (typeof toastr !== 'undefined') toastr.error('{{ custom_trans('error', 'front') }}'); });
        });
    }

    // ── Delete notification ─────────────────────────────────────────────────
    document.querySelectorAll('.delete-notification-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id   = this.dataset.notificationId;
            const card = document.getElementById('notif-' + id);

            confirmAction('{{ custom_trans('delete_notification_confirm', 'front') }}', function () {
                apiFetch(`/notifications/${id}`, 'DELETE')
                    .then(data => {
                        if (data.success) {
                            removeCard(card);
                            setTimeout(checkEmpty, 400);
                            if (typeof toastr !== 'undefined') toastr.info('{{ custom_trans('notification_deleted', 'front') }}');
                        }
                    })
                    .catch(() => { if (typeof toastr !== 'undefined') toastr.error('{{ custom_trans('error', 'front') }}'); });
            });
        });
    });

    // ── Sync bell badge in header ───────────────────────────────────────────
    function syncBellCount(delta, reset) {
        const bellBadge   = document.querySelector('.notification-badge');
        const headerBadge = document.querySelector('.notif-count-badge');

        if (reset) {
            if (bellBadge)   bellBadge.style.display = 'none';
            if (headerBadge) headerBadge.style.display = 'none';
            return;
        }

        if (bellBadge) {
            let count = parseInt(bellBadge.textContent) || 0;
            count = Math.max(0, count + delta);
            if (count === 0) {
                bellBadge.style.display = 'none';
            } else {
                bellBadge.textContent = count > 99 ? '99+' : count;
            }
        }
    }
});
</script>
@endpush
