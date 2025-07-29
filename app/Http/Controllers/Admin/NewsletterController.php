<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = Newsletter::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by email
        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $newsletters = $query->paginate(15);

        // Statistics
        $totalSubscribers = Newsletter::count();
        $activeSubscribers = Newsletter::active()->count();
        $inactiveSubscribers = Newsletter::inactive()->count();
        $thisMonthSubscribers = Newsletter::whereMonth('created_at', now()->month)->count();

        return view('admin.settings.newsletters.index', compact('newsletters', 'totalSubscribers', 'activeSubscribers', 'inactiveSubscribers', 'thisMonthSubscribers'));
    }

    public function show(Newsletter $newsletter)
    {
        return view('admin.settings.newsletters.show', compact('newsletter'));
    }

    public function updateStatus(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->status === 'active') {
            $newsletter->subscribe();
            $message = __('Newsletter subscription activated!');
        } else {
            $newsletter->unsubscribe();
            $message = __('Newsletter subscription deactivated!');
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return response()->json([
            'success' => true,
            'message' => __('Newsletter subscription deleted successfully!')
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'newsletters' => 'required|array',
            'newsletters.*' => 'exists:newsletters,id',
        ]);

        $newsletters = Newsletter::whereIn('id', $request->newsletters);

        switch ($request->action) {
            case 'activate':
                $newsletters->get()->each(function ($newsletter) {
                    $newsletter->subscribe();
                });
                $message = __('Newsletter subscriptions activated successfully!');
                break;
            case 'deactivate':
                $newsletters->get()->each(function ($newsletter) {
                    $newsletter->unsubscribe();
                });
                $message = __('Newsletter subscriptions deactivated successfully!');
                break;
            case 'delete':
                $newsletters->delete();
                $message = __('Newsletter subscriptions deleted successfully!');
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function export(Request $request)
    {
        $query = Newsletter::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $newsletters = $query->get();

        $filename = 'newsletters_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($newsletters) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['ID', 'Email', 'Status', 'Subscribed At', 'Unsubscribed At', 'Created At']);

            // Add data
            foreach ($newsletters as $newsletter) {
                fputcsv($file, [
                    $newsletter->id,
                    $newsletter->email,
                    $newsletter->status,
                    $newsletter->subscribed_at ? $newsletter->subscribed_at->format('Y-m-d H:i:s') : '',
                    $newsletter->unsubscribed_at ? $newsletter->unsubscribed_at->format('Y-m-d H:i:s') : '',
                    $newsletter->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
