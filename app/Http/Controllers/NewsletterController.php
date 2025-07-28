<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
        ]);

        Newsletter::create([
            'email' => $request->email,
            'status' => 'active',
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Thank you for subscribing to our newsletter!')
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:newsletters,email',
        ]);

        $newsletter = Newsletter::where('email', $request->email)->first();

        if ($newsletter) {
            $newsletter->unsubscribe();

            return response()->json([
                'success' => true,
                'message' => __('You have been unsubscribed from our newsletter.')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Email not found in our newsletter list.')
        ], 404);
    }
}
