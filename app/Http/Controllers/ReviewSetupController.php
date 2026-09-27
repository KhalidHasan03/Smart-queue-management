<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\ReviewSettings;
use Illuminate\Http\Request;

class ReviewSetupController extends Controller
{
    public function edit()
    {
        abort_unless(auth()->user()->hasPermission('display.manage'), 403);

        $settings = [];
        foreach (array_keys(ReviewSettings::DEFAULTS) as $key) {
            $settings[$key] = ReviewSettings::get($key);
        }

        $kioskUrl = route('review.kiosk');

        return view('review.setup', compact('settings', 'kioskUrl'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('display.manage'), 403);

        $data = $request->validate([
            ReviewSettings::field(ReviewSettings::ENABLED) => ['required', 'boolean'],
            ReviewSettings::field(ReviewSettings::HEADLINE) => ['required', 'string', 'max:120'],
            ReviewSettings::field(ReviewSettings::INSTRUCTION) => ['required', 'string', 'max:255'],
            ReviewSettings::field(ReviewSettings::IDLE_SECS) => ['required', 'integer', 'min:5', 'max:300'],
            ReviewSettings::field(ReviewSettings::AUTO_APPROVE_FROM) => ['required', 'integer', 'min:1', 'max:5'],
            ReviewSettings::field(ReviewSettings::REQUIRE_COMMENT_BELOW) => ['required', 'integer', 'min:0', 'max:4'],
            ReviewSettings::field(ReviewSettings::THROTTLE_PER_MIN) => ['required', 'integer', 'min:3', 'max:60'],
        ]);

        // Map the dot-free field names back to the real setting keys.
        foreach (ReviewSettings::FIELDS as $key => $field) {
            $value = $data[$field];

            if ($key === ReviewSettings::ENABLED) {
                $value = $request->boolean($field) ? '1' : '0';
            }

            // A threshold of 5 is above the top rating, which is how the admin
            // disables auto-approval entirely.
            if ($key === ReviewSettings::AUTO_APPROVE_FROM) {
                $value = min(5, max(1, (int) $value));
            }

            Setting::set($key, (string) $value);
        }

        return back()->with('success', 'Kiosk settings saved.');
    }
}
