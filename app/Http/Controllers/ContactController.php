<?php

namespace App\Http\Controllers;

use App\Models\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Store contact form submission.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contactForm = ContactForm::create($request->all());

            return response()->json([
                'success' => true,
                'message' => __('Thank you for your message. We will get back to you soon!'),
                'contact_form' => $contactForm
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while sending your message. Please try again.')
            ], 500);
        }
    }
}
