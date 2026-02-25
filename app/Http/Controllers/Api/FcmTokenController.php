<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Register or update FCM token for push notifications (mobile notification bar).
     * POST /api/fcm-token with { "token": "...", "device_type": "android|ios" }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|max:500',
            'device_type' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        UserFcmToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'token' => $validated['token'],
            ],
            ['device_type' => $validated['device_type'] ?? null]
        );

        return response()->json(['success' => true]);
    }
}
