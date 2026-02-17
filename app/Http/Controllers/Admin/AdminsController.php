<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminsController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::with('adminType');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->whereHas('adminType', function($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Apply sorting
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $admins = $query->paginate(15);

        // Calculate stats
        $stats = [
            'total_admins' => Admin::count(),
            'system_admins' => Admin::whereHas('adminType', function($q) { $q->where('name', 'admin'); })->count(),
            'instructors' => Admin::whereHas('adminType', function($q) { $q->where('name', 'instructor'); })->count(),
            'active_admins' => Admin::where('is_active', true)->count(),
        ];

        $adminTypes = AdminType::active()->ordered()->get();

        return view('admin.admins.index', compact('admins', 'stats', 'adminTypes'));
    }

    public function data(Request $request)
    {
        $search = $request->query('q');
        $perPage = $request->query('per_page', 15);
        $admins = Admin::with('adminType')
            ->when($search, function($q) use ($search) {
                $q->where('name','like',"%{$search}%")->orWhere('email','like',"%{$search}%");
            })
            ->latest()->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.admins.partials.table', compact('admins'))->render();
        }

        abort(404);
    }

    public function create()
    {
        $adminTypes = AdminType::active()->ordered()->get();
        return view('admin.admins.create', compact('adminTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|min:6',
            'admin_type_id' => 'required|exists:admin_types,id',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'cover' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'about_me' => 'nullable|string',
            'about_me_ar' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('admins/avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Handle cover upload
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('admins/covers', 'public');
            $data['cover'] = $coverPath;
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Handle boolean field
        $data['is_active'] = $request->has('is_active');

        Admin::create($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    public function show(Admin $admin)
    {
        $admin->load('adminType');
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        $adminTypes = AdminType::active()->ordered()->get();
        return view('admin.admins.update', compact('admin', 'adminTypes'));
    }

    public function update(Request $request, Admin $admin)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|min:6',
            'admin_type_id' => 'required|exists:admin_types,id',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about_me' => 'nullable|string',
            'about_me_ar' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $avatarPath = $request->file('avatar')->store('admins/avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Handle cover upload
        if ($request->hasFile('cover')) {
            // Delete old cover if exists
            if ($admin->cover && Storage::disk('public')->exists($admin->cover)) {
                Storage::disk('public')->delete($admin->cover);
            }
            $coverPath = $request->file('cover')->store('admins/covers', 'public');
            $data['cover'] = $coverPath;
        }

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle boolean field
        $data['is_active'] = $request->has('is_active');

        $admin->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        // Delete avatar if exists
        if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
            Storage::disk('public')->delete($admin->avatar);
        }

        // Delete cover if exists
        if ($admin->cover && Storage::disk('public')->exists($admin->cover)) {
            Storage::disk('public')->delete($admin->cover);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }

    public function toggleActive(Admin $admin)
    {
        $admin->update(['is_active' => !$admin->is_active]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin status updated successfully.');
    }

    public function updateStatus(Request $request, Admin $admin)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $admin->update(['is_active' => $request->status === 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Admin status updated successfully'
        ]);
    }

    public function active(Admin $admin)
    {
        $admin->update(['is_active' => true]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin activated successfully.');
    }
}
