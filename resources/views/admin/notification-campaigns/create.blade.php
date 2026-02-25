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
                <form action="{{ route('admin.notification-campaigns.store') }}" method="post" id="campaign-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Audience</label>
                        <select name="audience_type" id="audience_type" class="form-select" required>
                            <option value="single">Single user (by email)</option>
                            <option value="segment">Segment (enrolled in course)</option>
                            <option value="broadcast">Broadcast (all users)</option>
                        </select>
                    </div>
                    <div id="single-fields" class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="single_email" class="form-control" placeholder="user@example.com">
                        @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div id="segment-fields" class="mb-3" style="display:none;">
                        <label class="form-label">Enrolled in course</label>
                        <select name="enrolled_in_course_id" class="form-select">
                            <option value="">-- Select course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
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
                    <div class="mb-3">
                        <label class="form-label">When user clicks the notification, open</label>
                        <select name="action_type" id="action_type" class="form-select">
                            <option value="none">No link</option>
                            <option value="url">Custom URL</option>
                            <option value="course">Course</option>
                            <option value="blog">Blog</option>
                        </select>
                    </div>
                    <div id="action-url-wrap" class="mb-3" style="display:none;">
                        <label class="form-label">URL</label>
                        <input type="text" name="action_value" class="form-control" placeholder="https://example.com/page">
                    </div>
                    <div id="action-course-wrap" class="mb-3" style="display:none;">
                        <label class="form-label">Course</label>
                        <select id="action_entity_course" class="form-select" data-for="course">
                            <option value="">-- Select course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="action-blog-wrap" class="mb-3" style="display:none;">
                        <label class="form-label">Blog</label>
                        <select id="action_entity_blog" class="form-select" data-for="blog">
                            <option value="">-- Select blog --</option>
                            @foreach($blogs as $blog)
                                <option value="{{ $blog->id }}" data-slug="{{ $blog->slug }}">{{ $blog->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <div class="d-flex align-items-center gap-2">
                            <span id="priority-icon" class="fs-4"><i class="fa fa-bell text-info"></i></span>
                            <select name="priority" id="priority_select" class="form-select flex-grow-1">
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High (urgent)</option>
                            </select>
                        </div>
                        <div class="d-flex gap-3 mt-2 small text-muted">
                            <span><i class="fa fa-arrow-down text-secondary"></i> Low</span>
                            <span><i class="fa fa-bell text-info"></i> Normal</span>
                            <span><i class="fa fa-exclamation-triangle text-warning"></i> High / Urgent</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery</label>
                        <select name="delivery_channel" class="form-select">
                            <option value="notification">In-app notification only</option>
                            <option value="email">Email only</option>
                            <option value="both">Both (in-app + email)</option>
                        </select>
                        <small class="text-muted">In-app notifications also appear in the mobile notification bar when the user is not in the app (if push is enabled).</small>
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
            document.getElementById('single_email').required = (v === 'single');
        });
        document.getElementById('action_type').addEventListener('change', function() {
            var v = this.value;
            document.getElementById('action-url-wrap').style.display = v === 'url' ? 'block' : 'none';
            document.getElementById('action-course-wrap').style.display = v === 'course' ? 'block' : 'none';
            document.getElementById('action-blog-wrap').style.display = v === 'blog' ? 'block' : 'none';
            document.getElementById('action_entity_course').removeAttribute('name');
            document.getElementById('action_entity_blog').removeAttribute('name');
            if (v === 'course') document.getElementById('action_entity_course').setAttribute('name', 'action_entity_id');
            if (v === 'blog') document.getElementById('action_entity_blog').setAttribute('name', 'action_entity_id');
        });
        document.getElementById('priority_select').addEventListener('change', function() {
            var icon = document.getElementById('priority-icon');
            var c = this.value;
            if (c === 'low') { icon.innerHTML = '<i class="fa fa-arrow-down text-secondary"></i>'; }
            else if (c === 'high') { icon.innerHTML = '<i class="fa fa-exclamation-triangle text-warning"></i>'; }
            else { icon.innerHTML = '<i class="fa fa-bell text-info"></i>'; }
        });
        if (document.getElementById('audience_type').value === 'single') {
            document.getElementById('single_email').required = true;
        }
    </script>
@endsection
