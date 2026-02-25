<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchManualCampaignJob;
use App\Models\ManualNotificationCampaign;
use App\Models\Course;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationCampaignController extends Controller
{
    public function index()
    {
        $campaigns = ManualNotificationCampaign::with('admin')->latest()->paginate(15);
        return view('admin.notification-campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $courses = Course::orderBy('name')->get(['id', 'name']);
        $blogs = Blog::where('status', 'published')->orderBy('title')->get(['id', 'title', 'slug']);
        return view('admin.notification-campaigns.create', compact('courses', 'blogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audience_type' => 'required|in:single,segment,broadcast',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'body_en' => 'required|string',
            'body_ar' => 'required|string',
            'action_type' => 'nullable|in:url,course,blog,none',
            'action_value' => 'nullable|string|max:500',
            'action_entity_id' => 'nullable|integer',
            'priority' => 'nullable|in:low,normal,high',
            'delivery_channel' => 'nullable|in:notification,email,both',
            'scheduled_at' => 'nullable|date',
            'email' => 'required_if:audience_type,single|nullable|email',
            'enrolled_in_course_id' => 'nullable|exists:courses,id',
        ]);

        $filter = [];
        if ($validated['audience_type'] === 'single') {
            $filter['email'] = $validated['email'];
        }
        if ($validated['audience_type'] === 'segment' && !empty($validated['enrolled_in_course_id'])) {
            $filter['enrolled_in_course_id'] = (int) $validated['enrolled_in_course_id'];
        }

        $actionType = $validated['action_type'] ?? 'none';
        $actionValue = '';
        $entity = null;
        if ($actionType === 'url' && !empty($validated['action_value'])) {
            $actionValue = $validated['action_value'];
        }
        if ($actionType === 'course' && !empty($validated['action_entity_id'])) {
            $course = Course::find($validated['action_entity_id']);
            if ($course) {
                $actionValue = route('courses.show', $course);
                $entity = ['model' => 'course', 'id' => $course->id];
            }
        }
        if ($actionType === 'blog' && !empty($validated['action_entity_id'])) {
            $blog = Blog::find($validated['action_entity_id']);
            if ($blog) {
                $actionValue = url('/blog/' . $blog->slug);
                $entity = ['model' => 'blog', 'id' => $blog->id];
            }
        }

        $action = [
            'type' => ($actionType !== 'none' && $actionValue !== '') ? 'url' : 'none',
            'value' => $actionValue,
            'meta' => $entity ?? [],
        ];

        $campaign = ManualNotificationCampaign::create([
            'admin_id' => auth('admin')->id(),
            'audience_type' => $validated['audience_type'],
            'audience_filter' => $filter,
            'title_en' => $validated['title_en'],
            'title_ar' => $validated['title_ar'],
            'body_en' => $validated['body_en'],
            'body_ar' => $validated['body_ar'],
            'action_json' => $action,
            'entity_json' => $entity,
            'priority' => $validated['priority'] ?? 'normal',
            'delivery_channel' => $validated['delivery_channel'] ?? 'notification',
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
        ]);

        if (empty($validated['scheduled_at'])) {
            DispatchManualCampaignJob::dispatch($campaign);
            return redirect()->route('admin.notification-campaigns.index')->with('success', 'Notification campaign sent.');
        }

        DispatchManualCampaignJob::dispatch($campaign)->delay(now()->parse($validated['scheduled_at']));
        return redirect()->route('admin.notification-campaigns.index')->with('success', 'Notification campaign scheduled.');
    }
}
