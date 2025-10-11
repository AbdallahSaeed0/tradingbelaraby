<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerLogoController extends Controller
{
    public function index()
    {
        $logos = PartnerLogo::ordered()->get();
        return view('admin.settings.partner-logos.index', compact('logos'));
    }

    public function create()
    {
        return view('admin.settings.partner-logos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('logo');
        $data['is_active'] = $request->has('is_active');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('partner-logos', 'public');
        }

        PartnerLogo::create($data);

        return redirect()->route('admin.partner-logos.index')
            ->with('success', 'Partner logo created successfully.');
    }

    public function edit(PartnerLogo $partnerLogo)
    {
        return view('admin.settings.partner-logos.edit', compact('partnerLogo'));
    }

    public function update(Request $request, PartnerLogo $partnerLogo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('logo');
        $data['is_active'] = $request->has('is_active');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($partnerLogo->logo) {
                Storage::disk('public')->delete($partnerLogo->logo);
            }
            $data['logo'] = $request->file('logo')->store('partner-logos', 'public');
        }

        $partnerLogo->update($data);

        return redirect()->route('admin.partner-logos.index')
            ->with('success', 'Partner logo updated successfully.');
    }

    public function destroy(PartnerLogo $partnerLogo)
    {
        // Delete logo file
        if ($partnerLogo->logo) {
            Storage::disk('public')->delete($partnerLogo->logo);
        }

        $partnerLogo->delete();

        return redirect()->route('admin.partner-logos.index')
            ->with('success', 'Partner logo deleted successfully.');
    }
}
