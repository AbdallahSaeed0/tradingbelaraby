<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationSettingsController extends Controller
{
    public function index(): View
    {
        $settings = VerificationSettings::getSettings();
        return view('admin.settings.verification.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'method'                   => ['required', 'in:whatsapp,email,both'],
            'whatsapp_token'           => ['nullable', 'string', 'max:2048'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:100'],
            'whatsapp_otp_template'    => ['nullable', 'string', 'max:100'],
            'whatsapp_api_version'     => ['nullable', 'string', 'max:20'],
        ]);

        $settings = VerificationSettings::getSettings();

        $settings->method = $request->input('method');

        if ($request->filled('whatsapp_token')) {
            $settings->whatsapp_token = trim($request->input('whatsapp_token'));
        }
        if ($request->filled('whatsapp_phone_number_id')) {
            $settings->whatsapp_phone_number_id = trim($request->input('whatsapp_phone_number_id'));
        }
        if ($request->filled('whatsapp_otp_template')) {
            $settings->whatsapp_otp_template = trim($request->input('whatsapp_otp_template'));
        }
        if ($request->filled('whatsapp_api_version')) {
            $settings->whatsapp_api_version = trim($request->input('whatsapp_api_version'));
        }

        $settings->save();

        return redirect()->route('admin.settings.verification.index')
            ->with('success', __('Verification settings saved successfully.'));
    }
}
