<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentSettings;
use Illuminate\Http\JsonResponse;

class PaymentSettingsController extends Controller
{
    /**
     * Return public payment settings (bank transfer details for display in the app).
     */
    public function index(): JsonResponse
    {
        $settings = PaymentSettings::getSettings();

        return response()->json([
            'success' => true,
            'data' => [
                'bank_transfer_enabled'      => $settings->bank_transfer_enabled,
                'bank_transfer_bank_name'    => $settings->bank_transfer_bank_name,
                'bank_transfer_account_name' => $settings->bank_transfer_account_name,
                'bank_transfer_account_number' => $settings->bank_transfer_account_number,
                'bank_transfer_iban'         => $settings->bank_transfer_iban,
                'bank_transfer_instructions' => $settings->bank_transfer_instructions,
            ],
        ]);
    }
}
