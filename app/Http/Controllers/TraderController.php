<?php

namespace App\Http\Controllers;

use App\Models\Trader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TraderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sex' => 'required|in:male,female,other',
            'birthdate' => 'required|date|before:today',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'linkedin' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
            'trading_community' => 'nullable|string|max:255',
            'certificates' => 'nullable|string|max:1000',
            'trading_experience' => 'nullable|string|max:1000',
            'training_experience' => 'nullable|string|max:1000',
            'first_language' => 'required|string|max:100',
            'second_language' => 'nullable|string|max:100',
            'available_appointments' => 'nullable|string|max:500',
            'comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $trader = Trader::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your registration! We will contact you soon.',
                'data' => $trader
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your registration. Please try again later.',
            ], 500);
        }
    }

    public function index()
    {
        $traders = Trader::active()->orderBy('created_at', 'desc')->paginate(15);
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
}
