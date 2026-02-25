<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchManualCampaignJob;
use App\Models\ManualNotificationCampaign;
use App\Models\Course;
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
        return view('admin.notification-campaigns.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audience_type' => 'required|in:single,segment,broadcast',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'body_en' => 'required|string',
            'body_ar' => 'required|string',
            'action_type' => 'nullable|in:deeplink,url,none',
            'action_value' => 'nullable|string|max:500',
            'priority' => 'nullable|in:low,normal,high',
            'scheduled_at' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email',
            'enrolled_in_course_id' => 'nullable|exists:courses,id',
            'language' => 'nullable|string|max:10',
        ]);

        $filter = [];
        if ($validated['audience_type'] === 'single') {
            if (!empty($validated['user_id'])) {
                $filter['user_id'] = (int) $validated['user_id'];
            }
            if (!empty($validated['email'])) {
                $filter['email'] = $validated['email'];
            }
        }
        if ($validated['audience_type'] === 'segment') {
            if (!empty($validated['enrolled_in_course_id'])) {
                $filter['enrolled_in_course_id'] = (int) $validated['enrolled_in_course_id'];
            }
            if (!empty($validated['language'])) {
                $filter['language'] = $validated['language'];
            }
        }

        $action = [
            'type' => $validated['action_type'] ?? 'none',
            'value' => $validated['action_value'] ?? '',
            'meta' => [],
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
            'entity_json' => null,
            'priority' => $validated['priority'] ?? 'normal',
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
        ]);

        if (empty($validated['scheduled_at'])) {
            DispatchManualCampaignJob::dispatch($campaign);
            return redirect()->route('admin.notification-campaigns.index')->with('success', 'Notification campaign sent.');
        }

        // Optional: schedule the job for later via Scheduler or a cron that checks scheduled_at
        DispatchManualCampaignJob::dispatch($campaign)->delay(now()->parse($validated['scheduled_at']));
        return redirect()->route('admin.notification-campaigns.index')->with('success', 'Notification campaign scheduled.');
    }
}
