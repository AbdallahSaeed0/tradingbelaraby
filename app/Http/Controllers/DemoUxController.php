<?php

namespace App\Http\Controllers;

class DemoUxController extends Controller
{
    /**
     * Static demo: post-registration messaging (no backend).
     */
    public function postRegistration()
    {
        return view('demo.post-registration');
    }

    /**
     * Static non-interactive chart chrome for external / API review (illustrative only).
     */
    public function charts()
    {
        return view('demo.charts');
    }
}
