<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(Request $request, QueueService $queue)
    {
        $user = $request->user();
        if (! $user->hasPermission('queue.view')) {
            abort(403);
        }

        $counter = $user->counter()->with('service')->first();
        $today = Carbon::today()->toDateString();

        if (! $counter && $user->role === \App\Models\User::ROLE_OPERATOR) {
            return view('queue.index', [
                'counter' => null, 'current' => null, 'previous' => null,
                'waiting' => collect(), 'done' => collect(),
            ])->with('error', 'No counter assigned. Contact admin.');
        }

        $serviceId = $counter?->service_id ?? $user->service_id;
        $counterId = $counter?->id;

        try {
            $current = $counter ? $queue->current($user) : null;
            $previous = ($counter && $user->hasPermission('queue.previous')) ? $queue->previous($user) : null;
        } catch (\Throwable $e) {
            $current = null;
            $previous = null;
        }

        $waitingQuery = Token::with(['patient', 'doctor'])
            ->whereDate('token_date', $today)
            ->where('status', Token::WAITING);
        if ($serviceId) {
            $waitingQuery->where('service_id', $serviceId);
        }
        if ($user->doctor_id) {
            $waitingQuery->where('doctor_id', $user->doctor_id);
        }
        $waiting = $waitingQuery->orderBy('seq')->limit(20)->get();

        $done = $counterId ? Token::with(['patient'])
            ->whereDate('token_date', $today)
            ->where('counter_id', $counterId)
            ->whereIn('status', [Token::COMPLETED, Token::SKIPPED, Token::CANCELLED, Token::CALLING, Token::SERVING])
            ->orderByDesc('updated_at')->limit(10)->get() : collect();

        return view('queue.index', compact('counter', 'current', 'previous', 'waiting', 'done'));
    }

    public function next(Request $request, QueueService $queue)
    {
        try {
            $token = $queue->next($request->user());
            return back()->with('success', 'Calling '.$token->token_no);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    public function previous(Request $request, QueueService $queue)
    {
        try {
            $token = $queue->previous($request->user());
            if (! $token) {
                return back()->with('error', 'No previous token today.');
            }

            return back()->with('success', 'Previous token: '.$token->token_no.' ('.$token->status.')');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    public function action(Request $request, QueueService $queue, Token $token, string $action)
    {
        $required = match ($action) {
            'start', 'complete' => 'queue.complete',
            'skip' => 'queue.skip',
            'recall' => 'queue.recall',
            'cancel' => 'queue.cancel',
            default => null,
        };
        if (! $required || ! $request->user()->hasPermission($required)) {
            abort(403, 'Unauthorized.');
        }

        try {
            $token = match ($action) {
                'start' => $queue->startServing($request->user(), $token->id),
                'complete' => $queue->complete($request->user(), $token->id),
                'skip' => $queue->skip($request->user(), $token->id),
                'recall' => $queue->recall($request->user(), $token->id),
                'cancel' => $queue->cancel($request->user(), $token->id),
                default => throw new \Exception('Invalid action'),
            };

            return back()->with('success', ucfirst($action).' done: '.$token->token_no);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
