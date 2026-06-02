<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSettings;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    /**
     * Display the payment settings form.
     */
    public function index()
    {
        $settings = PaymentSettings::getSettings();

        return view('admin.settings.payment', compact('settings'));
    }

    /**
     * Update payment settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'bank_transfer_enabled'        => 'nullable|boolean',
            'bank_transfer_bank_name'      => 'nullable|string|max:255',
            'bank_transfer_account_name'   => 'nullable|string|max:255',
            'bank_transfer_account_number' => 'nullable|string|max:255',
            'bank_transfer_iban'           => 'nullable|string|max:255',
            'bank_transfer_instructions'   => 'nullable|string|max:2000',
        ]);

        $settings = PaymentSettings::getSettings();

        $settings->update([
            'bank_transfer_enabled'        => $request->has('bank_transfer_enabled'),
            'bank_transfer_bank_name'      => $request->bank_transfer_bank_name,
            'bank_transfer_account_name'   => $request->bank_transfer_account_name,
            'bank_transfer_account_number' => $request->bank_transfer_account_number,
            'bank_transfer_iban'           => $request->bank_transfer_iban,
            'bank_transfer_instructions'   => $request->bank_transfer_instructions,
        ]);

        return redirect()->route('admin.settings.payment.index')
            ->with('success', 'Payment settings updated successfully.');
    }
}
