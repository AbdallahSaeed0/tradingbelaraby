<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AdminType::withCount('admins');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by search term
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by permission
        if ($request->filled('permission')) {
            $permission = $request->permission;
            $query->whereJsonContains('permissions', $permission);
        }

        $perPage = (int) $request->get('per_page', 15);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }
        $adminTypes = $query->ordered()->paginate($perPage)->appends($request->query());

        // Get available permissions for filter dropdown
        $availablePermissions = $this->getAvailablePermissions();

        return view('admin.admin-types.index', compact('adminTypes', 'availablePermissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = $this->getAvailablePermissions();
        return view('admin.admin-types.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:admin_types',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        AdminType::create($data);

        return redirect()->route('admin.admin-types.index')
            ->with('success', 'Admin type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminType $adminType)
    {
        $adminType->load('admins');
        return view('admin.admin-types.show', compact('adminType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminType $adminType)
    {
        // Prevent editing the admin type
        if ($adminType->isAdminType()) {
            return redirect()->route('admin.admin-types.index')
                ->with('error', 'The admin type cannot be edited as it has all permissions by default.');
        }

        $permissions = $this->getAvailablePermissions();
        return view('admin.admin-types.edit', compact('adminType', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminType $adminType)
    {
        // Prevent updating the admin type
        if ($adminType->isAdminType()) {
            return redirect()->route('admin.admin-types.index')
                ->with('error', 'The admin type cannot be modified as it has all permissions by default.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:admin_types,name,' . $adminType->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        $adminType->update($data);

        return redirect()->route('admin.admin-types.index')
            ->with('success', 'Admin type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminType $adminType)
    {
        // Prevent deleting the admin type
        if ($adminType->isAdminType()) {
            return redirect()->route('admin.admin-types.index')
                ->with('error', 'The admin type cannot be deleted as it is required for system functionality.');
        }

        // Check if there are admins using this type
        if ($adminType->admins()->count() > 0) {
            return redirect()->route('admin.admin-types.index')
                ->with('error', 'Cannot delete admin type that has associated admins.');
        }

        $adminType->delete();

        return redirect()->route('admin.admin-types.index')
            ->with('success', 'Admin type deleted successfully.');
    }

    /**
     * Toggle the active status of an admin type
     */
    public function toggleStatus(AdminType $adminType)
    {
        // Prevent toggling the admin type
        if ($adminType->isAdminType()) {
            return response()->json([
                'success' => false,
                'message' => 'The admin type cannot be deactivated as it is required for system functionality.'
            ], 400);
        }

        $adminType->update(['is_active' => !$adminType->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Admin type status updated successfully.',
            'is_active' => $adminType->is_active
        ]);
    }

    /**
     * Update the status of an admin type
     */
    public function updateStatus(Request $request, AdminType $adminType)
    {
        // Prevent updating the admin type
        if ($adminType->isAdminType()) {
            return response()->json([
                'success' => false,
                'message' => 'The admin type cannot be deactivated as it is required for system functionality.'
            ], 400);
        }

        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $adminType->update(['is_active' => $request->status === 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Admin type status updated successfully'
        ]);
    }

    /**
     * Get available permissions for admin types
     */
    private function getAvailablePermissions()
    {
        return [
            'manage_admins' => 'Manage Administrators',
            'manage_users' => 'Manage Users',
            'manage_courses' => 'Manage Courses',
            'manage_categories' => 'Manage Categories',
            'manage_enrollments' => 'Manage Enrollments',
            'manage_quizzes' => 'Manage Quizzes',
            'manage_homework' => 'Manage Homework',
            'manage_live_classes' => 'Manage Live Classes',
            'manage_questions_answers' => 'Manage Q&A',
            'manage_blogs' => 'Manage Blogs',
            'manage_translations' => 'Manage Translations',
            'manage_languages' => 'Manage Languages',
            'view_analytics' => 'View Analytics',
            'export_data' => 'Export Data',
            'import_data' => 'Import Data',
            'manage_own_courses' => 'Manage Own Courses',
            'manage_own_quizzes' => 'Manage Own Quizzes',
            'manage_own_homework' => 'Manage Own Homework',
            'manage_own_live_classes' => 'Manage Own Live Classes',
            'view_own_analytics' => 'View Own Analytics',
            'manage_own_questions_answers' => 'Manage Own Q&A',
        ];
    }
}
