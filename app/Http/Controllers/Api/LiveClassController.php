<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveClassResource;
use App\Models\LiveClass;
use App\Models\LiveClassRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveClassController extends Controller
{
    /**
     * Display a listing of live classes (upcoming and recent)
     */
    public function index(Request $request)
    {
        $query = LiveClass::query()
            ->with(['course', 'instructor']);

        // Filter by course if specified
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter upcoming only (default)
        if (!$request->filled('status') && !$request->boolean('all', false)) {
            $query->where('scheduled_at', '>', now())
                ->where('status', 'scheduled');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('scheduled_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('scheduled_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $liveClasses = $query->orderBy('scheduled_at')->paginate($request->get('per_page', 20));

        return LiveClassResource::collection($liveClasses);
    }

    /**
     * Display the specified live class
     */
    public function show($id)
    {
        $liveClass = LiveClass::with(['course', 'instructor'])
            ->findOrFail($id);

        return new LiveClassResource($liveClass);
    }

    /**
     * Register for a live class (requires auth)
     */
    public function register(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Please login to register'], 401);
        }

        $liveClass = LiveClass::with(['course', 'instructor'])->findOrFail($id);

        $existing = LiveClassRegistration::where('user_id', $user->id)
            ->where('live_class_id', $liveClass->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Already registered for this class',
                'already_registered' => true,
            ]);
        }

        if ($liveClass->max_participants) {
            $count = $liveClass->registrations()->count();
            if ($count >= $liveClass->max_participants) {
                return response()->json(['error' => 'Class is full'], 400);
            }
        }

        LiveClassRegistration::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $user->id,
            'registered_at' => now(),
            'status' => 'registered',
        ]);

        $liveClass->increment('current_participants');

        return response()->json([
            'success' => true,
            'message' => 'Successfully registered for the live class',
        ]);
    }
}
