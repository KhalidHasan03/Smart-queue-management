<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Counter;
use App\Models\Setting;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function screen()
    {
        return view('display.screen', [
            'advertPosition' => Setting::get('advert.position', 'right'),
        ]);
    }

    public function manage()
    {
        $counters = Counter::with('service')->orderBy('name')->get();
        $settings = [
            'refresh_secs' => Setting::get('display.refresh_secs', '4'),
            'ticker' => Setting::get('display.ticker', ''),
            'show_patient' => Setting::get('display.show_patient', '1'),
        ];

        return view('display.manage', compact('counters', 'settings'));
    }

    public function updateManage(Request $request)
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

        Setting::set('display.refresh_secs', (string) $request->input('refresh_secs'));
        Setting::set('display.ticker', $request->input('ticker', ''));
        Setting::set('display.show_patient', $request->boolean('show_patient') ? '1' : '0');

        return back()->with('success', 'Display settings saved.');
    }

    public function api()
    {
        $today = Carbon::today();
        $showPatient = Setting::get('display.show_patient', '1') === '1';
        $counters = Counter::with(['service', 'currentToken.patient', 'currentToken.doctor'])
            ->where('is_active', true)->where('show_on_display', true)->orderBy('name')->get();

        $now = $counters->map(function ($c) use ($showPatient, $today) {
            $live = $c->currentToken && in_array($c->currentToken->status, [Token::CALLING, Token::SERVING], true)
                && $c->currentToken->token_date->isSameDay($today);

            // A closed counter shows nothing, so the wall never displays a
            // stale number for a desk that is not taking patients. An in-flight
            // visit is the exception: that patient is physically at the desk
            // and must still see (and be seen by staff) their own number.
            $token = $live
                ? $c->currentToken
                : ($c->isOpen()
                    ? Token::where('counter_id', $c->id)->whereDate('token_date', $today)
                        ->orderByDesc('created_at')->orderByDesc('id')->first()
                    : null);

            return [
                'counter' => $c->name,
                'room' => $c->room_no,
                'service' => $c->service->name ?? '',
                'is_open' => (bool) $c->is_open,
                'token_no' => $token?->token_no,
                'status' => $token?->status,
                'status_label' => $token ? self::statusLabel($token->status) : ($c->isOpen() ? null : 'Closed'),
                'patient' => $live && $showPatient ? $c->currentToken->patient->name ?? null : null,
                'doctor' => $live ? $c->currentToken->doctor->name ?? null : null,
                'is_live' => $live,
            ];
        });

        // Waiting patients only appear while a counter for their service is
        // switched on — a closed desk holds its queue back from the wall.
        $upcoming = Token::with(['service', 'counter'])
            ->whereDate('token_date', $today)
            ->where('status', Token::WAITING)
            ->atOpenCounter()
            ->orderBy('created_at')->limit(12)->get()
            ->map(fn ($t) => ['token_no' => $t->token_no, 'service' => $t->service->name, 'counter' => $t->counter->name]);

        // How many are held back right now, so the wall can explain an empty
        // "Upcoming" panel instead of claiming the queue is clear.
        $paused = Token::whereDate('token_date', $today)
            ->where('status', Token::WAITING)
            ->whereNotIn(
                'service_id',
                Counter::query()->serving()->distinct()->select('service_id')
            )
            ->count();

        $advertMode = Setting::get('advert.mode', 'cycle');
        $advertQuery = Advertisement::active()->ordered();
        if ($advertMode === 'single') {
            $advertQuery->where('is_live', true);
        }
        $advertItems = $advertQuery->limit(12)->get()->map(fn ($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'description' => $a->description,
            'media_type' => $a->media_type,
            'image' => $a->media_type === Advertisement::TYPE_IMAGE ? $a->media_url : null,
            'video' => $a->media_type === Advertisement::TYPE_VIDEO ? $a->media_url : null,
            'media_mime' => $a->media_type === Advertisement::TYPE_VIDEO ? $a->media_mime : null,
            'youtube' => $a->media_type === Advertisement::TYPE_YOUTUBE ? $a->youtube_embed_url : null,
            'youtube_embed' => $a->has_embed ? $a->youtube_embed : null,
            'has_embed' => $a->has_embed,
            'text' => $a->media_type === Advertisement::TYPE_TEXT ? $a->description : null,
            'duration_secs' => $a->duration_secs,
        ]);

        return response()->json([
            'date' => Carbon::today()->format('d M Y'),
            'time' => now()->format('h:i:s A'),
            'clinic' => Setting::get('clinic_name', config('app.name')),
            'refresh_secs' => (int) Setting::get('display.refresh_secs', '4'),
            'ticker' => Setting::get('display.ticker', ''),
            'now' => $now,
            'upcoming' => $upcoming,
            'paused' => $paused,
            'advert' => [
                'enabled' => Setting::get('advert.enabled', '1') === '1',
                'mode' => $advertMode,
                'duration_secs' => (int) Setting::get('advert.duration_secs', '15'),
                'position' => Setting::get('advert.position', 'right'),
                'items' => $advertItems,
            ],
        ]);
    }

    protected static function statusLabel(string $status): string
    {
        return match ($status) {
            Token::WAITING => 'Waiting',
            Token::CALLING => 'Calling',
            Token::SERVING => 'Serving',
            Token::COMPLETED => 'Completed',
            Token::SKIPPED => 'Skipped',
            Token::CANCELLED => 'Cancelled',
            default => ucfirst($status),
        };
    }
}
