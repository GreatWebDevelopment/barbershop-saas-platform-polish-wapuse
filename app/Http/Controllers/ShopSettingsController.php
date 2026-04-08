<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ShopSettingsController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Settings/Shop', [
            'shop' => $request->user()->shop,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'hours' => 'nullable|array',
            'logo' => 'nullable|image|max:2048',
        ]);

        $shop = $request->user()->shop;

        if ($request->hasFile('logo')) {
            if ($shop->logo_path) {
                Storage::disk('public')->delete($shop->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);
        $shop->update($validated);

        return redirect()->back()->with('success', 'Shop settings updated.');
    }
}
