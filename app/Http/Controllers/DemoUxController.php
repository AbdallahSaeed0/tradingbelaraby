<?php

namespace App\Http\Controllers;

class DemoUxController extends Controller
{
    /**
     * Static non-interactive chart chrome for external / API review (illustrative only).
     */
    public function charts()
    {
        return view('demo.charts');
    }
}
