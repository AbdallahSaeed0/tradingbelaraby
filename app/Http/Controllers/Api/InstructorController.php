<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InstructorPublicResource;
use App\Http\Resources\InstructorResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InstructorController extends Controller
{
    /**
     * Public listing (no auth) — safe fields only (name, bio, no email).
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min($perPage, 50);

        $instructors = Admin::whereHas('courses', fn($q) => $q->where('status', '!=', 'draft'))
            ->withCount(['courses' => fn($q) => $q->where('status', '!=', 'draft')])
            ->with('courses:id,instructor_id,enrolled_students,average_rating,status')
            ->orderByDesc('courses_count')
            ->paginate($perPage);

        return response()->json([
            'data' => InstructorPublicResource::collection($instructors->items()),
            'meta' => [
                'current_page' => $instructors->currentPage(),
                'last_page' => $instructors->lastPage(),
                'per_page' => $instructors->perPage(),
                'total' => $instructors->total(),
            ],
        ]);
    }

    /**
     * Public single instructor (no auth) — safe fields only.
     */
    public function showPublic(string $id): JsonResponse
    {
        $instructor = Admin::whereHas('courses', fn($q) => $q->where('status', '!=', 'draft'))
            ->withCount(['courses' => fn($q) => $q->where('status', '!=', 'draft')])
            ->with('courses:id,instructor_id,enrolled_students,average_rating,status')
            ->findOrFail($id);

        return response()->json([
            'data' => new InstructorPublicResource($instructor),
        ]);
    }

    /**
     * Public top instructors (no auth) — safe fields only.
     */
    public function topPublic(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $limit = min($limit, 20);

        $instructors = Admin::whereHas('courses', fn($q) => $q->where('status', '!=', 'draft'))
            ->withCount(['courses' => fn($q) => $q->where('status', '!=', 'draft')])
            ->with('courses:id,instructor_id,enrolled_students,average_rating,status')
            ->orderByDesc('courses_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => InstructorPublicResource::collection($instructors),
        ]);
    }

    /**
     * Display a listing of instructors.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min($perPage, 50); // Max 50 per page

        // Get admins who are instructors (have at least one non-draft course)
        $instructors = Admin::whereHas('courses', fn($q) => $q->where('status', '!=', 'draft'))
            ->withCount(['courses' => fn($q) => $q->where('status', '!=', 'draft')])
            ->with('courses:id,instructor_id,enrolled_students,average_rating,status')
            ->orderByDesc('courses_count')
            ->paginate($perPage);

        return response()->json([
            'data' => InstructorResource::collection($instructors->items()),
            'meta' => [
                'current_page' => $instructors->currentPage(),
                'last_page' => $instructors->lastPage(),
                'per_page' => $instructors->perPage(),
                'total' => $instructors->total(),
            ],
        ]);
    }

    /**
     * Display the specified instructor.
     */
    public function show(string $id): JsonResponse
    {
        $instructor = Admin::whereHas('courses', fn($q) => $q->where('status', '!=', 'draft'))
            ->withCount(['courses' => fn($q) => $q->where('status', '!=', 'draft')])
            ->with('courses:id,instructor_id,enrolled_students,average_rating,status')
            ->findOrFail($id);

        return response()->json([
            'data' => new InstructorResource($instructor),
        ]);
    }

    /**
     * Get top instructors (for home page slider).
     */
    public function top(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $limit = min($limit, 20); // Max 20

        $instructors = Admin::whereHas('courses', fn($q) => $q->where('status', '!=', 'draft'))
            ->withCount(['courses' => fn($q) => $q->where('status', '!=', 'draft')])
            ->with('courses:id,instructor_id,enrolled_students,average_rating,status')
            ->orderByDesc('courses_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => InstructorResource::collection($instructors),
        ]);
    }
}
