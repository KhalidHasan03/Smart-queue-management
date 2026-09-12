<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $base = Token::whereDate('token_date', $today);
        $stats = [
            'waiting' => (clone $base)->where('status', Token::WAITING)->count(),
            'calling' => (clone $base)->where('status', Token::CALLING)->count(),
            'serving' => (clone $base)->where('status', Token::SERVING)->count(),
            'completed' => (clone $base)->where('status', Token::COMPLETED)->count(),
            'skipped' => (clone $base)->where('status', Token::SKIPPED)->count(),
            'cancelled' => (clone $base)->where('status', Token::CANCELLED)->count(),
            'total' => (clone $base)->count(),
        ];
        $recent = Token::with(['patient', 'service', 'counter'])
            ->whereDate('token_date', $today)->orderByDesc('id')->limit(10)->get();

        $staffTokens = collect();
        if ($user->role === \App\Models\User::ROLE_STAFF) {
            $staffTokens = Token::with(['patient', 'service', 'doctor', 'counter'])
                ->whereDate('token_date', $today)
                ->when($user->service_id, fn ($q) => $q->where('service_id', $user->service_id))
                ->when($user->doctor_id, fn ($q) => $q->where('doctor_id', $user->doctor_id))
                ->when(! $user->service_id && ! $user->doctor_id, fn ($q) => $q->whereRaw('1 = 0'))
                ->orderBy('seq')->limit(10)->get();
        }

        $displayCounters = collect();
        if ($user->hasPermission('display.manage')) {
            $displayCounters = Counter::with('service')->orderBy('name')->get();
        }

        return view('dashboard', compact('stats', 'recent', 'staffTokens', 'displayCounters'));
    }
}
