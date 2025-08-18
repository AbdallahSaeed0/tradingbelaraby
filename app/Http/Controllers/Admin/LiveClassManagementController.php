<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\Course;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiveClassManagementController extends Controller
{
    /**
     * Display live class management dashboard
     */
    public function index(Request $request)
    {
        $query = LiveClass::with(['course', 'instructor', 'registrations']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('course', function($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('instructor')) {
            $query->where('instructor_id', $request->instructor);
        }

        if ($request->filled('date_from')) {
            $query->where('scheduled_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('scheduled_at', '<=', $request->date_to . ' 23:59:59');
        }

        $liveClasses = $query->orderByDesc('scheduled_at')->paginate(15);

        $stats = [
            'total_classes' => LiveClass::count(),
            'scheduled_classes' => LiveClass::where('status', 'scheduled')->count(),
            'live_classes' => LiveClass::where('status', 'live')->count(),
            'completed_classes' => LiveClass::where('status', 'completed')->count(),
            'total_registrations' => \App\Models\LiveClassRegistration::count(),
        ];

        $courses = Course::all();
                    $instructors = Admin::whereHas('adminType', function($query) {
                $query->where('name', 'instructor');
            })->get();

        return view('admin.live-classes.index', compact('liveClasses', 'stats', 'courses', 'instructors'));
    }

    /**
     * Show live class creation form
     */
    public function create()
    {
        $courses = Course::published()->get();
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })->where('is_active', true)->get();

        return view('admin.live-classes.create', compact('courses', 'instructors'));
    }

    /**
     * Store new live class
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:admins,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'required|url',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'max_participants' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'materials' => 'nullable|array',
            'materials.*' => 'nullable|string',
            'material_files.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            // Process materials (links and files)
            $materials = [];
            if ($request->has('materials')) {
                foreach ($request->materials as $index => $material) {
                    if (!empty($material)) {
                        $materials[] = $material;
                    }
                }
            }

            // Handle file uploads
            if ($request->hasFile('material_files')) {
                foreach ($request->file('material_files') as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('live-class-materials', 'public');
                        $materials[] = [
                            'type' => 'file',
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize()
                        ];
                    }
                }
            }

            $liveClass = LiveClass::create([
                'course_id' => $request->course_id,
                'instructor_id' => $request->instructor_id,
                'name' => $request->name,
                'description' => $request->description,
                'link' => $request->link,
                'scheduled_at' => $request->scheduled_at,
                'duration_minutes' => $request->duration_minutes,
                'max_participants' => $request->max_participants,
                'is_free' => false, // Default to false
                'requires_registration' => true, // Default to true
                'instructions' => $request->instructions,
                'materials' => $materials,
                'status' => 'scheduled',
            ]);

            return redirect()->route('admin.live-classes.index')
                ->with('success', 'Live class created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating live class: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating live class. Please try again.');
        }
    }

    /**
     * Show live class edit form
     */
    public function edit(LiveClass $liveClass)
    {
        $courses = Course::published()->get();
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })->where('is_active', true)->get();

        return view('admin.live-classes.edit', compact('liveClass', 'courses', 'instructors'));
    }

    /**
     * Update live class
     */
    public function update(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:admins,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'required|url',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'max_participants' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'materials' => 'nullable|array',
            'materials.*' => 'nullable|string',
            'recording_url' => 'nullable|url',
            'material_files.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            // Process materials (links and files)
            $materials = [];

            // Get existing materials to preserve them
            $existingMaterials = $liveClass->materials ?? [];

            // Process materials from form (both existing and new)
            if ($request->has('materials')) {
                foreach ($request->materials as $index => $material) {
                    if (!empty($material)) {
                        // Check if this is a string (link) or should be preserved as existing file
                        if (is_string($material)) {
                            $materials[] = $material;
                        }
                    }
                }
            }

            // Preserve existing file materials
            if (!empty($existingMaterials)) {
                foreach ($existingMaterials as $material) {
                    if (is_array($material) && isset($material['type']) && $material['type'] === 'file') {
                        $materials[] = $material;
                    }
                }
            }

            // Handle new file uploads
            if ($request->hasFile('material_files')) {
                foreach ($request->file('material_files') as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('live-class-materials', 'public');
                        $materials[] = [
                            'type' => 'file',
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize()
                        ];
                    }
                }
            }

            $liveClass->update([
                'course_id' => $request->course_id,
                'instructor_id' => $request->instructor_id,
                'name' => $request->name,
                'description' => $request->description,
                'link' => $request->link,
                'scheduled_at' => $request->scheduled_at,
                'duration_minutes' => $request->duration_minutes,
                'max_participants' => $request->max_participants,
                'instructions' => $request->instructions,
                'materials' => $materials,
                'recording_url' => $request->recording_url,
            ]);

            return redirect()->route('admin.live-classes.index')
                ->with('success', 'Live class updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating live class: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating live class. Please try again.');
        }
    }

    /**
     * Delete live class
     */
    public function destroy(LiveClass $liveClass)
    {
        try {
            $liveClass->delete();
            return redirect()->route('admin.live-classes.index')
                ->with('success', 'Live class deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting live class: ' . $e->getMessage());
            return back()->with('error', 'Error deleting live class. Please try again.');
        }
    }

    /**
     * Duplicate live class
     */
    public function duplicate(LiveClass $liveClass)
    {
        try {
            $newLiveClass = $liveClass->replicate();
            $newLiveClass->name = $liveClass->name . ' (Copy)';
            $newLiveClass->scheduled_at = now()->addDays(7);
            $newLiveClass->status = 'scheduled';
            $newLiveClass->current_participants = 0;
            $newLiveClass->recording_url = null;
            $newLiveClass->save();

            return redirect()->route('admin.live-classes.index')
                ->with('success', 'Live class duplicated successfully');
        } catch (\Exception $e) {
            Log::error('Error duplicating live class: ' . $e->getMessage());
            return back()->with('error', 'Error duplicating live class. Please try again.');
        }
    }

    /**
     * Bulk delete live classes
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_classes' => 'required|array|min:1',
            'selected_classes.*' => 'exists:live_classes,id'
        ]);

        try {
            LiveClass::whereIn('id', $request->selected_classes)->delete();
            return response()->json([
                'success' => true,
                'message' => count($request->selected_classes) . ' live classes deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting live classes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting live classes'
            ], 500);
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'selected_classes' => 'required|array|min:1',
            'selected_classes.*' => 'exists:live_classes,id',
            'status' => 'required|in:scheduled,live,completed,cancelled'
        ]);

        try {
            LiveClass::whereIn('id', $request->selected_classes)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => count($request->selected_classes) . ' live classes status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk updating live classes status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating live classes status'
            ], 500);
        }
    }

    /**
     * Toggle live class status
     */
    public function toggleStatus(LiveClass $liveClass)
    {
        try {
            $newStatus = 'scheduled';
            switch($liveClass->status) {
                case 'scheduled':
                    $newStatus = 'live';
                    break;
                case 'live':
                    $newStatus = 'completed';
                    break;
                case 'completed':
                    $newStatus = 'scheduled';
                    break;
                default:
                    $newStatus = 'scheduled';
            }

            $liveClass->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Live class status updated successfully',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling live class status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating live class status'
            ], 500);
        }
    }

    /**
     * View live class registrations
     */
    public function registrations(LiveClass $liveClass, Request $request)
    {
        $registrations = $liveClass->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Handle export requests
        if ($request->has('export')) {
            return $this->exportRegistrations($liveClass, $registrations, $request->export);
        }

        // Paginate for normal view
        $registrations = $liveClass->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.live-classes.registrations', compact('liveClass', 'registrations'));
    }

    /**
     * Export registrations
     */
    private function exportRegistrations(LiveClass $liveClass, $registrations, $format)
    {
        $filename = "live-class-{$liveClass->id}-registrations-" . now()->format('Y-m-d') . ".{$format}";

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($registrations, $filename);
            case 'excel':
                return $this->exportToExcel($registrations, $filename);
            case 'pdf':
                return $this->exportToPdf($registrations, $filename, $liveClass);
            default:
                return back()->with('error', 'Invalid export format');
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($registrations, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($registrations) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, ['#', 'Student Name', 'Email', 'Registration Date', 'Status', 'Attended']);

            foreach ($registrations as $index => $registration) {
                $studentName = $registration->user ? $registration->user->name : 'Student Deleted';
                $email = $registration->user ? $registration->user->email : 'N/A';

                fputcsv($file, [
                    $index + 1,
                    $studentName,
                    $email,
                    $registration->created_at->format('M j, Y g:i A'),
                    ucfirst($registration->status ?? 'pending'),
                    $registration->attended ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($registrations, $filename)
    {
        // For now, return CSV as Excel (you can implement proper Excel export later)
        return $this->exportToCsv($registrations, str_replace('.excel', '.csv', $filename));
    }

    /**
     * Export to PDF
     */
    private function exportToPdf($registrations, $filename, $liveClass)
    {
        // For now, return a simple HTML response (you can implement proper PDF export later)
        $html = view('admin.live-classes.export-pdf', compact('registrations', 'liveClass'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Export live class data
     */
    public function export(LiveClass $liveClass)
    {
        // Implementation for exporting live class data
        return response()->json([
            'success' => false,
            'message' => 'Export functionality will be implemented'
        ]);
    }

    /**
     * View live class analytics
     */
    public function analytics(LiveClass $liveClass)
    {
        $stats = [
            'total_registrations' => $liveClass->registrations()->count(),
            'attended_registrations' => $liveClass->registrations()->where('status', 'attended')->count(),
            'attendance_rate' => $liveClass->registrations()->count() > 0
                ? round(($liveClass->registrations()->where('status', 'attended')->count() / $liveClass->registrations()->count()) * 100, 2)
                : 0,
            'capacity_utilization' => $liveClass->max_participants > 0
                ? round(($liveClass->current_participants / $liveClass->max_participants) * 100, 2)
                : 0,
        ];

        return view('admin.live-classes.analytics', compact('liveClass', 'stats'));
    }
}
