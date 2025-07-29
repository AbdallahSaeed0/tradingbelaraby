<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // Check if user is authenticated as admin
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = auth('admin')->user();

        // If no specific permission is required, just check if admin is active
        if (!$permission) {
            if (!$admin->is_active) {
                return redirect()->route('admin.login')->with('error', 'Your account has been deactivated.');
            }
            return $next($request);
        }

        // Handle multiple permissions (comma-separated)
        $permissions = array_map('trim', explode(',', $permission));

        // Check if admin has ANY of the required permissions
        $hasPermission = false;
        foreach ($permissions as $perm) {
            if ($admin->hasPermission($perm)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied. You do not have permission to perform this action.',
                    'permission_required' => $permissions
                ], 403);
            }

            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this page.');
        }

        return $next($request);
    }
}
