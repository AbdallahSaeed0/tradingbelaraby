<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveClassResource;
use App\Models\LiveClass;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
