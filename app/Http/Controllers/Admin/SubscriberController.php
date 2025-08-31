<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subscriber::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        // Filter by years of experience
        if ($request->filled('years_of_experience')) {
            $query->where('years_of_experience', $request->years_of_experience);
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        $subscribers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.subscribers.index', compact('subscribers'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriber $subscriber)
    {
        return view('admin.subscribers.show', compact('subscriber'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Subscriber deleted successfully.');
    }

    /**
     * Export subscribers to CSV
     */
    public function export(Request $request)
    {
        $query = Subscriber::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->filled('years_of_experience')) {
            $query->where('years_of_experience', $request->years_of_experience);
        }

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        $subscribers = $query->orderBy('created_at', 'desc')->get();

        $filename = 'subscribers_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'WhatsApp Number',
                'Country',
                'Years of Experience',
                'Language',
                'Notes',
                'Created At'
            ]);

            // Add data rows
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->id,
                    $subscriber->name,
                    $subscriber->email,
                    $subscriber->phone,
                    $subscriber->whatsapp_number,
                    $subscriber->country,
                    $subscriber->years_of_experience,
                    $subscriber->language,
                    $subscriber->notes,
                    $subscriber->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
