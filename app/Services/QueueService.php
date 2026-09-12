<?php

namespace App\Services;

use App\Models\Counter;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QueueService
{
    private function operatorCounter(User $operator): Counter
    {
        if (! $operator->counter_id) {
            throw ValidationException::withMessages(['counter' => 'No counter assigned to your account.']);
        }
        $counter = Counter::where('is_active', true)->find($operator->counter_id)
            ?? throw ValidationException::withMessages(['counter' => 'Assigned counter is inactive.']);

        return $counter;
    }

    private function today(): string
    {
        return Carbon::today()->toDateString();
    }

    public function current(User $operator): ?Token
    {
        $counter = $this->operatorCounter($operator);

        return Token::with(['patient', 'service', 'doctor'])
            ->whereDate('token_date', $this->today())
            ->where('counter_id', $counter->id)
            ->whereIn('status', [Token::CALLING, Token::SERVING])
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    public function next(User $operator): Token
    {
        return DB::transaction(function () use ($operator) {
            $counter = $this->operatorCounter($operator);

            $active = Token::whereDate('token_date', $this->today())
                ->where('counter_id', $counter->id)
                ->whereIn('status', [Token::CALLING, Token::SERVING])
                ->lockForUpdate()
                ->first();
            if ($active) {
                throw ValidationException::withMessages(['queue' => 'Complete or skip '.$active->token_no.' first.']);
            }

            $next = Token::whereDate('token_date', $this->today())
                ->where('service_id', $counter->service_id)
                ->where('status', Token::WAITING)
                ->orderBy('seq')
                ->lockForUpdate()
                ->first() ?? throw ValidationException::withMessages(['queue' => 'No waiting tokens.']);

            $next->update([
                'status' => Token::CALLING,
                'counter_id' => $counter->id,
                'called_at' => now(),
            ]);
            $counter->update(['current_token_id' => $next->id]);

            return $next->fresh();
        });
    }

    private function ownedToken(User $operator, int $tokenId): Token
    {
        $counter = $this->operatorCounter($operator);

        $token = Token::whereDate('token_date', $this->today())->lockForUpdate()->find($tokenId)
            ?? throw ValidationException::withMessages(['token' => 'Token not found.']);

        if ((int) $token->counter_id !== (int) $counter->id && $token->status !== Token::WAITING) {
            throw ValidationException::withMessages(['token' => 'This token belongs to another counter.']);
        }

        return $token;
    }

    public function startServing(User $operator, int $tokenId): Token
    {
        return DB::transaction(function () use ($operator, $tokenId) {
            $token = $this->ownedToken($operator, $tokenId);
            if ($token->status !== Token::CALLING) {
                throw ValidationException::withMessages(['token' => 'Only calling tokens can start serving.']);
            }
            $token->update(['status' => Token::SERVING, 'started_at' => now()]);

            return $token->fresh();
        });
    }

    public function complete(User $operator, int $tokenId): Token
    {
        return DB::transaction(function () use ($operator, $tokenId) {
            $counter = $this->operatorCounter($operator);
            $token = $this->ownedToken($operator, $tokenId);
            if (! in_array($token->status, [Token::CALLING, Token::SERVING], true)) {
                throw ValidationException::withMessages(['token' => 'Only calling/serving tokens can complete.']);
            }
            $token->update(['status' => Token::COMPLETED, 'finished_at' => now()]);
            if ((int) $counter->current_token_id === (int) $token->id) {
                $counter->update(['current_token_id' => null]);
            }

            return $token->fresh();
        });
    }

    public function skip(User $operator, int $tokenId): Token
    {
        return DB::transaction(function () use ($operator, $tokenId) {
            $counter = $this->operatorCounter($operator);
            $token = $this->ownedToken($operator, $tokenId);
            if (! in_array($token->status, [Token::CALLING, Token::SERVING, Token::WAITING], true)) {
                throw ValidationException::withMessages(['token' => 'Cannot skip this token.']);
            }
            $token->update(['status' => Token::SKIPPED, 'finished_at' => now()]);
            if ((int) $counter->current_token_id === (int) $token->id) {
                $counter->update(['current_token_id' => null]);
            }

            return $token->fresh();
        });
    }

    public function previous(User $operator): ?Token
    {
        $counter = $this->operatorCounter($operator);

        return Token::with(['patient', 'service', 'doctor'])
            ->whereDate('token_date', $this->today())
            ->where('counter_id', $counter->id)
            ->whereIn('status', [Token::COMPLETED, Token::SKIPPED, Token::CANCELLED])
            ->orderByDesc('finished_at')
            ->orderByDesc('updated_at')
            ->first();
    }

    public function recall(User $operator, int $tokenId): Token
    {
        return DB::transaction(function () use ($operator, $tokenId) {
            $token = $this->ownedToken($operator, $tokenId);
            if ($token->status !== Token::CALLING) {
                throw ValidationException::withMessages(['token' => 'Only calling tokens can be recalled.']);
            }
            $token->update(['called_at' => now()]);

            return $token->fresh();
        });
    }

    public function cancel(User $operator, int $tokenId): Token
    {
        return DB::transaction(function () use ($operator, $tokenId) {
            $counter = $this->operatorCounter($operator);
            $token = Token::whereDate('token_date', $this->today())->lockForUpdate()->find($tokenId)
                ?? throw ValidationException::withMessages(['token' => 'Token not found.']);
            if (! in_array($token->status, [Token::WAITING, Token::CALLING, Token::SERVING], true)) {
                throw ValidationException::withMessages(['token' => 'Cannot cancel this token.']);
            }
            if ($operator->role === User::ROLE_OPERATOR && (int) $token->counter_id !== (int) $counter->id
                && $token->service_id !== $counter->service_id) {
                throw ValidationException::withMessages(['token' => 'Not your queue.']);
            }
            $token->update(['status' => Token::CANCELLED, 'finished_at' => now()]);
            if ((int) $counter->current_token_id === (int) $token->id) {
                $counter->update(['current_token_id' => null]);
            }

            return $token->fresh();
        });
    }
}
