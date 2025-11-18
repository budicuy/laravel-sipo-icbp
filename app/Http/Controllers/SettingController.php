<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fingerprint_enabled' => 'required|in:0,1',
        ]);

        // Convert to integer to ensure proper boolean storage
        $enabled = (int) $request->fingerprint_enabled;

        Setting::setValue(
            'fingerprint_enabled',
            $enabled,
            'boolean',
            'Enable or disable fingerprint verification'
        );

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function getFingerprintStatus()
    {
        $enabled = Setting::getValue('fingerprint_enabled', true);
        return response()->json(['enabled' => $enabled]);
    }
}
