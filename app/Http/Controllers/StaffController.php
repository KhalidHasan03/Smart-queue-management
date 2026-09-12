<?php

namespace App\Http\Controllers;

use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    private function scope(Request $request): array
    {
        $user = $request->user();

        return [$user->service_id, $user->doctor_id];
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user->hasPermission('queue.view')) {
            abort(403);
        }
        [$serviceId, $doctorId] = $this->scope($request);
        $today = Carbon::today()->toDateString();

        $tokens = Token::with(['patient', 'service', 'doctor', 'counter'])
            ->whereDate('token_date', $today)
            ->when($serviceId, fn ($q) => $q->where('service_id', $serviceId))
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->when(! $serviceId && ! $doctorId, fn ($q) => $q->whereRaw('1 = 0'))
            ->orderBy('seq')
            ->limit(50)
            ->get();

        return view('staff.index', compact('tokens'));
    }

    public function process(Request $request, Token $token)
    {
        $user = $request->user();
        [$serviceId, $doctorId] = $this->scope($request);

        try {
            if ($serviceId && (int) $token->service_id !== (int) $serviceId) {
                throw ValidationException::withMessages(['token' => 'Not your assigned service.']);
            }
            if ($doctorId && (int) $token->doctor_id !== (int) $doctorId) {
                throw ValidationException::withMessages(['token' => 'Not your assigned doctor.']);
            }

            $token = DB::transaction(function () use ($token) {
                $fresh = Token::lockForUpdate()->find($token->id);
                if ($fresh->status === Token::CALLING) {
                    $fresh->update(['status' => Token::SERVING, 'started_at' => now()]);
                } elseif ($fresh->status === Token::SERVING) {
                    $fresh->update(['status' => Token::COMPLETED, 'finished_at' => now()]);
                } else {
                    throw ValidationException::withMessages(['token' => 'Only calling/serving tokens can be processed.']);
                }

                return $fresh->fresh();
            });

            return back()->with('success', 'Updated: '.$token->token_no.' → '.$token->status);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }
}
