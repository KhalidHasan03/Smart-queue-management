<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'clinic_address' => ['nullable', 'string', 'max:500'],
            'token_header' => ['nullable', 'string', 'max:500'],
            'token_footer' => ['nullable', 'string', 'max:500'],
        ]);
        foreach ($request->only(['clinic_name', 'clinic_address', 'token_header', 'token_footer']) as $k => $v) {
            Setting::set($k, $v);
        }

        return back()->with('success', 'Settings saved.');
    }
}
