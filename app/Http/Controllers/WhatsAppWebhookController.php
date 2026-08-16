<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles Meta's WhatsApp Cloud API webhook: the GET verification handshake
 * and the POST event delivery (messages/status updates).
 */
class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && hash_equals((string) config('services.meta_whatsapp.verify_token'), (string) $token)) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        Log::info('WhatsApp webhook event', $request->all());

        return response()->json(['status' => 'ok']);
    }
}
