@extends('admin.layout')

@section('title', 'Send Notification')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Send Notification</h3>
            <a href="{{ route('admin.notification-campaigns.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.notification-campaigns.store') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Audience</label>
                        <select name="audience_type" id="audience_type" class="form-select" required>
                            <option value="single">Single user (by ID or email)</option>
                            <option value="segment">Segment (e.g. enrolled in course)</option>
                            <option value="broadcast">Broadcast (all users)</option>
                        </select>
                    </div>
                    <div id="single-fields" class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="number" name="user_id" class="form-control" placeholder="User ID">
                        <label class="form-label mt-2">Or Email</label>
                        <input type="email" name="email" class="form-control" placeholder="user@example.com">
                    </div>
                    <div id="segment-fields" class="mb-3" style="display:none;">
                        <label class="form-label">Enrolled in course</label>
                        <select name="enrolled_in_course_id" class="form-select">
                            <option value="">-- Select course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                        <label class="form-label mt-2">Language</label>
                        <input type="text" name="language" class="form-control" placeholder="ar or en" maxlength="10">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (EN) *</label>
                            <input type="text" name="title_en" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (AR) *</label>
                            <input type="text" name="title_ar" class="form-control" required maxlength="255">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (EN) *</label>
                            <textarea name="body_en" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (AR) *</label>
                            <textarea name="body_ar" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Action type</label>
                            <select name="action_type" class="form-select">
                                <option value="none">None</option>
                                <option value="deeplink">Deeplink</option>
                                <option value="url">URL</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Action value</label>
                            <input type="text" name="action_value" class="form-control" placeholder="/courses/1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Schedule (optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control">
                        <small class="text-muted">Leave empty to send now.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Send / Schedule</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('audience_type').addEventListener('change', function() {
            var v = this.value;
            document.getElementById('single-fields').style.display = v === 'single' ? 'block' : 'none';
            document.getElementById('segment-fields').style.display = v === 'segment' ? 'block' : 'none';
        });
    </script>
@endsection
