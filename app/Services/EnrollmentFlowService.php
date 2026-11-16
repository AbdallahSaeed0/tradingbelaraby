<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Notifications\CourseEnrollmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * One place for "visitor wants this course" → enrolled.
 *
 * - enrollFree(): enroll a user in a free course right away (no cart / checkout).
 * - rememberIntent(): a guest clicked "Enroll" — remember the course in the session.
 * - completeIntent(): right after sign-up / sign-in, finish what the guest started
 *   and return where to send them (the "you're enrolled" page for free courses).
 */
class EnrollmentFlowService
{
    public const SESSION_KEY = 'enroll_intent_course_id';

    public function enrollFree(User $user, Course $course): CourseEnrollment
    {
        // A row may already exist (e.g. an expired subscription) because of the
        // unique (course_id, user_id) index, so reuse it instead of inserting.
        $enrollment = $user->enrollments()->updateOrCreate(
            ['course_id' => $course->id],
            [
                'status' => 'active',
                'access_type' => 'lifetime',
                'subscription_plan_id' => null,
                'expires_at' => null,
                'enrolled_at' => now(),
            ]
        );

        ga_event('enroll', [
            'course_id' => $course->id,
            'course_name' => $course->name,
            'value' => 0,
            'currency' => 'SAR',
        ]);

        // Queued notification: never blocks or breaks enrollment
        try {
            $language = Session::get('frontend_locale', config('app.locale'));
            $language = in_array($language, ['ar', 'en']) ? $language : 'en';
            $user->notify(new CourseEnrollmentNotification($course, null, $language));
        } catch (\Throwable $e) {
            Log::error('Failed to send enrollment notification', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $enrollment;
    }

    /**
     * Read ?enroll={courseId} from an auth page request and remember it.
     */
    public function rememberIntentFromRequest(Request $request): void
    {
        $courseId = (int) $request->input('enroll');
        if ($courseId > 0) {
            $this->rememberIntent($courseId);
        }
    }

    public function rememberIntent(int $courseId): void
    {
        $course = Course::find($courseId);
        if (!$course) {
            return;
        }

        session([self::SESSION_KEY => $course->id]);
        // Fallback for paid courses / anything else: come back to the course page
        redirect()->setIntendedUrl(route('courses.show', $course));
    }

    /**
     * Finish a remembered enrollment for a freshly signed-in user.
     * Returns the URL to redirect to, or null when there was nothing to do.
     */
    public function completeIntent(User $user): ?string
    {
        $courseId = session()->pull(self::SESSION_KEY);
        if (!$courseId) {
            return null;
        }

        $course = Course::find($courseId);
        if (!$course) {
            return null;
        }

        session()->forget('url.intended');

        if ($user->blocksCoursePurchase($course->id)) {
            return route('courses.enrolled', $course);
        }

        // Paid course: back to the course page, where the enroll button adds it to the cart
        if ($course->requiresPayment()) {
            return route('courses.show', $course);
        }

        try {
            $this->enrollFree($user, $course);
        } catch (\Throwable $e) {
            Log::error('Auto-enroll after sign-up failed', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            return route('courses.show', $course);
        }

        return route('courses.enrolled', $course);
    }
}
