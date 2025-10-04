<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TraderController extends Controller
{
    public function index(Request $request)
    {
        $query = Trader::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('trading_community', 'like', "%{$search}%")
                  ->orWhere('first_language', 'like', "%{$search}%");
            });
        }


        $traders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.traders.index', compact('traders'));
    }

    public function show(Trader $trader)
    {
        return view('admin.traders.show', compact('trader'));
    }

    public function destroy(Trader $trader)
    {
        $trader->delete();

        return redirect()->route('admin.traders.index')
            ->with('success', 'Trader registration deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = Trader::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('trading_community', 'like', "%{$search}%")
                  ->orWhere('first_language', 'like', "%{$search}%");
            });
        }


        $traders = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];

        // CSV Headers
        $csvData[] = [
            'Name',
            'Gender',
            'Birth Date',
            'Email',
            'Phone Number',
            'WhatsApp Number',
            'LinkedIn',
            'Website',
            'Trading Community',
            'Primary Language',
            'Secondary Language',
            'Certificates',
            'Trading Experience',
            'Training Experience',
            'Available Appointments',
            'Comments',
            'Registration Date'
        ];

        // CSV Data
        foreach ($traders as $trader) {
            $csvData[] = [
                $trader->name,
                ucfirst($trader->sex),
                $trader->birthdate ? $trader->birthdate->format('Y-m-d') : '',
                $trader->email,
                $trader->phone_number ?? '',
                $trader->whatsapp_number ?? '',
                $trader->linkedin ?? '',
                $trader->website ?? '',
                $trader->trading_community ?? '',
                $trader->first_language,
                $trader->second_language ?? '',
                $trader->certificates ?? '',
                $trader->trading_experience ?? '',
                $trader->training_experience ?? '',
                $trader->available_appointments ?? '',
                $trader->comments ?? '',
                $trader->created_at->format('Y-m-d H:i:s')
            ];
        }

        $filename = 'traders_export_' . date('Y-m-d_H-i-s') . '.csv';

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'trader_ids' => 'required|array',
            'trader_ids.*' => 'exists:traders,id'
        ]);

        $count = Trader::whereIn('id', $request->trader_ids)->delete();

        return redirect()->back()
            ->with('success', "Successfully deleted {$count} trader(s).");
    }
}
