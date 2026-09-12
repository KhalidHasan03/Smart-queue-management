<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Token;
use Carbon\Carbon;

class DisplayController extends Controller
{
    public function screen()
    {
        return view('display.screen');
    }

    public function manage()
    {
        $counters = Counter::with('service')->orderBy('name')->get();
        $settings = [
            'refresh_secs' => \App\Models\Setting::get('display.refresh_secs', '4'),
            'ticker' => \App\Models\Setting::get('display.ticker', ''),
            'show_patient' => \App\Models\Setting::get('display.show_patient', '1'),
        ];

        return view('display.manage', compact('counters', 'settings'));
    }

    public function updateManage(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'visible' => ['nullable', 'array'],
            'visible.*' => ['integer', 'exists:counters,id'],
            'refresh_secs' => ['required', 'integer', 'min:2', 'max:30'],
            'ticker' => ['nullable', 'string', 'max:500'],
            'show_patient' => ['sometimes', 'boolean'],
        ]);

        $visible = collect($request->input('visible', []))->map(fn ($v) => (int) $v);
        Counter::query()->update(['show_on_display' => false]);
        if ($visible->isNotEmpty()) {
            Counter::whereIn('id', $visible)->update(['show_on_display' => true]);
        }

        \App\Models\Setting::set('display.refresh_secs', (string) $request->input('refresh_secs'));
        \App\Models\Setting::set('display.ticker', $request->input('ticker', ''));
        \App\Models\Setting::set('display.show_patient', $request->boolean('show_patient') ? '1' : '0');

        return back()->with('success', 'Display settings saved.');
    }

    public function api()
    {
        $today = Carbon::today()->toDateString();
        $showPatient = \App\Models\Setting::get('display.show_patient', '1') === '1';
        $counters = Counter::with(['service', 'currentToken.patient', 'currentToken.doctor'])
            ->where('is_active', true)->where('show_on_display', true)->orderBy('name')->get();

        $now = $counters->map(function ($c) use ($showPatient) {
            $live = $c->currentToken && in_array($c->currentToken->status, [Token::CALLING, Token::SERVING], true)
                && $c->currentToken->token_date->isSameDay(Carbon::today());

            return [
                'counter' => $c->name,
                'room' => $c->room_no,
                'service' => $c->service->name ?? '',
                'token_no' => $live ? $c->currentToken->token_no : null,
                'patient' => $live && $showPatient ? $c->currentToken->patient->name ?? null : null,
                'doctor' => $live ? $c->currentToken->doctor->name ?? null : null,
                'status' => $live ? $c->currentToken->status : null,
            ];
        });

        $upcoming = Token::with(['service', 'counter'])
            ->whereDate('token_date', $today)
            ->where('status', Token::WAITING)
            ->orderBy('created_at')->limit(8)->get()
            ->map(fn ($t) => ['token_no' => $t->token_no, 'service' => $t->service->name, 'counter' => $t->counter->name]);

        return response()->json([
            'date' => $today,
            'time' => now()->format('h:i:s A'),
            'clinic' => \App\Models\Setting::get('clinic_name', config('app.name')),
            'refresh_secs' => (int) \App\Models\Setting::get('display.refresh_secs', '4'),
            'ticker' => \App\Models\Setting::get('display.ticker', ''),
            'now' => $now,
            'upcoming' => $upcoming,
        ]);
    }
}
