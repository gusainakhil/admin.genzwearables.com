<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'razorpay_key_id' => Setting::get('razorpay_key_id', ''),
            'razorpay_key_secret' => Setting::get('razorpay_key_secret', ''),
            'razorpay_enabled' => Setting::get('razorpay_enabled', '1'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'razorpay_key_id' => 'nullable|string|max:100',
            'razorpay_key_secret' => 'nullable|string|max:100',
            'razorpay_enabled' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'Settings updated successfully');
    }
}
