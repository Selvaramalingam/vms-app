<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'currency' => 'nullable|string',
            'date_format' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'logo') {
                if ($request->hasFile('logo')) {
                    $path = $request->file('logo')->store('public/logo');
                    Setting::set('logo', Storage::url($path));
                }
                continue;
            }
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }
}
