<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InstructorResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InstructorController extends Controller
{
    /**
     * Display a listing of instructors.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min($perPage, 50); // Max 50 per page

        // Get admins who are instructors (have courses)
        $instructors = Admin::whereHas('courses')
            ->withCount('courses')
            ->with('courses:id,instructor_id,enrolled_students,average_rating')
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
        $instructor = Admin::whereHas('courses')
            ->withCount('courses')
            ->with('courses:id,instructor_id,enrolled_students,average_rating')
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

        $instructors = Admin::whereHas('courses')
            ->withCount('courses')
            ->with('courses:id,instructor_id,enrolled_students,average_rating')
            ->orderByDesc('courses_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => InstructorResource::collection($instructors),
        ]);
    }
}
