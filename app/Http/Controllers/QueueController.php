<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Token;
use App\Models\User;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        if (! $counter && $user->role === User::ROLE_OPERATOR) {
            return view('queue.index', [
                'counter' => null, 'current' => null, 'previous' => null,
                'waiting' => collect(), 'done' => collect(), 'pendingReviews' => collect(),
                'counterOpen' => false, 'hiddenWaiting' => 0,
            ])->with('error', 'No counter assigned. Contact admin.');
        }

        $serviceId = $counter?->service_id ?? $user->service_id;
        $counterId = $counter?->id;
        $counterOpen = (bool) $counter?->isOpen();

        try {
            $current = $counter ? $queue->current($user) : null;
            $previous = ($counter && $user->hasPermission('queue.previous')) ? $queue->previous($user) : null;
        } catch (\Throwable $e) {
            $current = null;
            $previous = null;
        }

        // A closed counter has no visible waiting list: those patients are held
        // back from the display, so showing them here would be misleading.
        $waitingQuery = Token::with(['patient', 'doctor'])
            ->whereDate('token_date', $today)
            ->where('status', Token::WAITING);
        if ($serviceId) {
            $waitingQuery->where('service_id', $serviceId);
        }
        if ($user->doctor_id) {
            $waitingQuery->where('doctor_id', $user->doctor_id);
        }
        $waiting = $counterOpen
            ? $waitingQuery->atOpenCounter()->orderBy('seq')->limit(20)->get()
            : collect();

        $hiddenWaiting = $counter && $user->hasPermission('queue.next')
            ? $queue->hiddenWaitingCount($user)
            : 0;

        $done = $counterId ? Token::with(['patient', 'review'])
            ->whereDate('token_date', $today)
            ->where('counter_id', $counterId)
            ->whereIn('status', [Token::COMPLETED, Token::SKIPPED, Token::CANCELLED, Token::CALLING, Token::SERVING])
            ->orderByDesc('updated_at')->limit(10)->get() : collect();

        // Counter operators moderate feedback for their own counter only; admins
        // see the full list in the moderation dashboard instead.
        $pendingReviews = ($counterId && $user->hasPermission('reviews.manage') && ! $user->isAdmin())
            ? Review::with(['patient', 'token'])
                ->where('status', Review::STATUS_PENDING)
                ->whereHas('token', fn ($q) => $q->where('counter_id', $counterId))
                ->orderByDesc('created_at')->limit(3)->get()
            : collect();

        return view('queue.index', compact(
            'counter', 'current', 'previous', 'waiting', 'done',
            'pendingReviews', 'counterOpen', 'hiddenWaiting',
        ));
    }

    /**
     * Open or close the operator's own counter. Closing hides its waiting
     * patients from the display and blocks new calls; anything already being
     * served can still be completed.
     */
    public function toggleCounter(Request $request, QueueService $queue)
    {
        if (! $request->user()->hasPermission('queue.next')) {
            abort(403, 'Unauthorized.');
        }

        try {
            $counter = $queue->toggleCounter($request->user());
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('success', $counter->isOpen()
            ? $counter->name.' is open — patients are visible as waiting again.'
            : $counter->name.' is closed — its waiting patients are hidden from the display.');
    }

    public function next(Request $request, QueueService $queue)
    {
        try {
            $token = $queue->next($request->user());

            return back()->with('success', 'Calling '.$token->token_no);
        } catch (ValidationException $e) {
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
        } catch (ValidationException $e) {
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
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
