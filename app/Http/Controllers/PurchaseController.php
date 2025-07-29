<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user's purchase history
     */
    public function history()
    {
        $purchases = auth()->user()->enrollments()
            ->with(['course', 'course.instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('purchases.history', compact('purchases'));
    }

    /**
     * Show a specific purchase
     */
    public function show($purchase)
    {
        $enrollment = auth()->user()->enrollments()
            ->with(['course', 'course.instructor', 'course.sections.lectures'])
            ->findOrFail($purchase);

        return view('purchases.show', compact('enrollment'));
    }
}
