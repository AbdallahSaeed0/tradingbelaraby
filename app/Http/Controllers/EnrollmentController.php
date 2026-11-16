<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\EnrollmentFlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Auth::user()->enrollments()->with('course')->paginate(20);
        return view('enrollments.index', compact('enrollments'));
    }

    public function enroll(Request $request, Course $course)
    {
        try {
            Log::info('Enrollment request received', [
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'expects_json' => $request->expectsJson(),
                'accept_header' => $request->header('Accept'),
                'content_type' => $request->header('Content-Type')
            ]);

            $user = Auth::user();

            if (!$user) {
                // Remember the course so sign-up / sign-in finishes the enrollment
                app(EnrollmentFlowService::class)->rememberIntent($course->id);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not authenticated'
                    ], 401);
                }
                return redirect()->route('register');
            }

        // Check if user is already enrolled
        if ($user->blocksCoursePurchase($course->id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => custom_trans('You are already enrolled in this course!', 'front'),
                ]);
            }
            return redirect()->route('courses.show', $course->id)->with('info', custom_trans('You are already enrolled in this course!', 'front'));
        }

        // Prevent direct enrollment for paid courses - they must go through checkout/cart
        if (!$course->is_free && $course->requiresPayment()) {
            $message = custom_trans('This is a paid course. Please add it to your cart and complete checkout.', 'front');

            if ($request->expectsJson()) {
                // Return 200 with success=false so frontend can handle gracefully without JS error
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ]);
            }

            return redirect()->route('cart.index')->with('error', $message);
        }

        // Free course: enroll right away, then show the "you're enrolled" page
        app(EnrollmentFlowService::class)->enrollFree($user, $course);

        $successMessage = custom_trans('Enrolled successfully', 'front');
        $redirectUrl = route('courses.enrolled', $course);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl)->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('Enrollment error', [
                'error' => $e->getMessage(),
                'course_id' => $course->id,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during enrollment'
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred during enrollment');
        }
    }

    /**
     * "You're enrolled" page: answers "when is it and how do I get in?"
     * Shows the next live session in the live class timezone, add-to-calendar
     * buttons, a WhatsApp contact and (if missing) a field to add a WhatsApp number.
     */
    public function enrolled(Course $course)
    {
        $user = Auth::user();

        if (!$user->hasCourseAccess($course->id)) {
            return redirect()->route('courses.show', $course);
        }

        $nextClass = $course->nextLiveClass();
        $startsAt = $nextClass?->startsAt();
        // Join link is shown here (and sent by reminder) from one hour before the start
        $canJoinNow = $startsAt && $nextClass->link && now()->gte($startsAt->copy()->subHour());

        $contactSettings = \App\Models\ContactSettings::getActive();
        $whatsappPhone = $contactSettings && $contactSettings->phone
            ? preg_replace('/[^0-9]/', '', $contactSettings->phone)
            : null;

        return view('courses.enrolled', [
            'course' => $course,
            'nextClass' => $nextClass,
            'startsAt' => $startsAt,
            'canJoinNow' => $canJoinNow,
            'whatsappPhone' => $whatsappPhone,
            'needsPhone' => empty(trim((string) $user->phone)),
            'defaultCountry' => $user->country ?: app(\App\Services\VisitorCountryService::class)->detect(request()),
        ]);
    }

    /**
     * Save the WhatsApp number from the "you're enrolled" page (used for reminders).
     */
    public function saveWhatsapp(Request $request, Course $course)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+[1-9][0-9]{7,14}$/'],
        ], [
            'phone.required' => custom_trans('Please enter your phone number.', 'front'),
            'phone.regex' => custom_trans('Please enter a valid phone number for the selected country.', 'front'),
        ]);

        $request->user()->update(['phone' => trim($data['phone'])]);

        return redirect()->route('courses.enrolled', $course)
            ->with('success', custom_trans('Done! We will send session reminders to your WhatsApp.', 'front'));
    }

    public function export()
    {
        // Export logic here
        return response()->json(['message' => 'Export not implemented.']);
    }
}
