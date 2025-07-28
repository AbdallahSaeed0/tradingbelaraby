<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use App\Models\LiveClassRegistration;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LiveClassController extends Controller
{
    /**
     * Display upcoming live classes
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = LiveClass::upcoming()->with(['course', 'instructor']);

        // Filter by course if specified
        if ($request->has('course') && $request->course) {
            $query->where('course_id', $request->course);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('scheduled_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('scheduled_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $liveClasses = $query->orderBy('scheduled_at')->paginate(10);

        // Add registration status for authenticated users
        if ($user) {
            $liveClasses->getCollection()->transform(function($class) use ($user) {
                $class->user_registered = $class->registrations()
                    ->where('user_id', $user->id)
                    ->exists();
                return $class;
            });
        }

        $courses = Course::published()->get();

        return view('live-classes.index', compact('liveClasses', 'courses'));
    }

    /**
     * Display live class details
     */
    public function show(LiveClass $liveClass)
    {
        $user = Auth::user();

        $liveClass->load(['course', 'instructor']);

        $isRegistered = false;
        $registration = null;

        if ($user) {
            $registration = LiveClassRegistration::where('user_id', $user->id)
                ->where('live_class_id', $liveClass->id)
                ->first();
            $isRegistered = (bool) $registration;
        }

        $canRegister = $liveClass->canUserRegister($user);
        $spotsRemaining = $liveClass->max_participants
            ? $liveClass->max_participants - $liveClass->registrations()->count()
            : null;

        return view('live-classes.show', compact(
            'liveClass',
            'isRegistered',
            'registration',
            'canRegister',
            'spotsRemaining'
        ));
    }

    /**
     * Register for a live class
     */
    public function register(Request $request, LiveClass $liveClass)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Please login to register'], 401);
        }

        if (!$liveClass->canUserRegister($user)) {
            return response()->json(['error' => 'Cannot register for this class'], 403);
        }

        // Check if already registered
        $existingRegistration = LiveClassRegistration::where('user_id', $user->id)
            ->where('live_class_id', $liveClass->id)
            ->first();

        if ($existingRegistration) {
            return response()->json(['error' => 'Already registered for this class'], 400);
        }

        // Check capacity
        if ($liveClass->max_participants) {
            $currentRegistrations = $liveClass->registrations()->count();
            if ($currentRegistrations >= $liveClass->max_participants) {
                return response()->json(['error' => 'Class is full'], 400);
            }
        }

        $registration = LiveClassRegistration::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $user->id,
            'registered_at' => now(),
            'status' => 'registered'
        ]);

        // You might want to send email confirmation here

        return response()->json([
            'success' => true,
            'message' => 'Successfully registered for the live class',
            'registration_id' => $registration->id
        ]);
    }

    /**
     * Cancel registration for a live class
     */
    public function cancelRegistration(LiveClass $liveClass)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $registration = LiveClassRegistration::where('user_id', $user->id)
            ->where('live_class_id', $liveClass->id)
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        // Check if cancellation is allowed (e.g., not too close to start time)
        $hoursUntilClass = $liveClass->scheduled_at->diffInHours(now());
        if ($hoursUntilClass < 2) { // 2 hours minimum notice
            return response()->json(['error' => 'Cannot cancel less than 2 hours before class'], 400);
        }

        $registration->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration cancelled successfully'
        ]);
    }

    /**
     * Join live class (get meeting link)
     */
    public function join(LiveClass $liveClass)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to join the class.');
        }

        // Check if user is registered
        $registration = LiveClassRegistration::where('user_id', $user->id)
            ->where('live_class_id', $liveClass->id)
            ->where('status', 'registered')
            ->first();

        if (!$registration) {
            return redirect()->route('live-classes.show', $liveClass)
                ->with('error', 'You must be registered to join this class.');
        }

        // Check if class is starting soon or has started
        $now = now();
        $classStart = $liveClass->scheduled_at;
        $classEnd = $classStart->copy()->addMinutes($liveClass->duration);

        $minutesUntilStart = $classStart->diffInMinutes($now, false);

        // Allow joining 15 minutes before and during the class
        if ($minutesUntilStart > 15) {
            return redirect()->route('live-classes.show', $liveClass)
                ->with('error', 'Class has not started yet. Please wait until 15 minutes before the scheduled time.');
        }

        if ($now->gt($classEnd)) {
            return redirect()->route('live-classes.show', $liveClass)
                ->with('error', 'This class has already ended.');
        }

        // Mark attendance if not already marked
        if (!$registration->joined_at) {
            $registration->update([
                'joined_at' => now(),
                'status' => 'attended'
            ]);
        }

        // In a real implementation, you would integrate with video conferencing
        // For now, we'll redirect to a placeholder
        if ($liveClass->meeting_url) {
            return redirect()->away($liveClass->meeting_url);
        }

        return view('live-classes.join', compact('liveClass', 'registration'));
    }

    /**
     * Get user's registered live classes
     */
    public function myClasses()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $registrations = LiveClassRegistration::where('user_id', $user->id)
            ->with(['liveClass.course', 'liveClass.instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Separate upcoming and past classes
        $upcomingClasses = $registrations->getCollection()->filter(function($reg) {
            return $reg->liveClass->scheduled_at->gt(now()) && $reg->status === 'registered';
        });

        $pastClasses = $registrations->getCollection()->filter(function($reg) {
            return $reg->liveClass->scheduled_at->lt(now()) || $reg->status !== 'registered';
        });

        return view('live-classes.my-classes', compact('registrations', 'upcomingClasses', 'pastClasses'));
    }

    /**
     * Get upcoming classes for dashboard widget
     */
    public function upcoming()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $upcomingClasses = LiveClassRegistration::where('user_id', $user->id)
            ->where('status', 'registered')
            ->whereHas('liveClass', function($query) {
                $query->where('scheduled_at', '>', now())
                      ->where('scheduled_at', '<', now()->addDays(7));
            })
            ->with(['liveClass.course'])
            ->orderBy('created_at')
            ->limit(3)
            ->get();

        return response()->json($upcomingClasses);
    }

    /**
     * Record attendance (for instructor use)
     */
    public function recordAttendance(Request $request, LiveClass $liveClass)
    {
        $user = Auth::user();

        // Check if user is the instructor or admin
        if (!$user || $liveClass->instructor_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*.user_id' => 'required|exists:users,id',
            'attendance.*.attended' => 'required|boolean'
        ]);

        foreach ($request->attendance as $record) {
            $registration = LiveClassRegistration::where('live_class_id', $liveClass->id)
                ->where('user_id', $record['user_id'])
                ->first();

            if ($registration) {
                $status = $record['attended'] ? 'attended' : 'absent';
                $registration->update([
                    'status' => $status,
                    'joined_at' => $record['attended'] ? ($registration->joined_at ?? now()) : null
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Attendance recorded']);
    }

    /**
     * Get class statistics
     */
    public function statistics(LiveClass $liveClass)
    {
        $user = Auth::user();

        // Check if user is the instructor or admin
        if (!$user || $liveClass->instructor_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $registrations = $liveClass->registrations;
        $totalRegistered = $registrations->count();
        $attended = $registrations->where('status', 'attended')->count();
        $absent = $registrations->where('status', 'absent')->count();
        $cancelled = $registrations->where('status', 'cancelled')->count();

        $stats = [
            'total_registered' => $totalRegistered,
            'attended' => $attended,
            'absent' => $absent,
            'cancelled' => $cancelled,
            'attendance_rate' => $totalRegistered > 0 ? ($attended / $totalRegistered) * 100 : 0,
            'capacity_used' => $liveClass->max_participants
                ? ($totalRegistered / $liveClass->max_participants) * 100
                : null
        ];

        return response()->json($stats);
    }
}
