<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTokenRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Token;
use App\Services\TokenService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Token::class);
        $q = $request->input('q');
        $tokens = Token::with(['patient', 'service', 'doctor', 'counter'])
            ->whereDate('token_date', Carbon::today()->toDateString())
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('token_no', 'like', "%{$q}%")
                    ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"));
            }))
            ->orderBy('seq')
            ->paginate(15)->withQueryString();

        return view('tokens.index', compact('tokens', 'q'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Token::class);
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::with('service')->where('is_active', true)->orderBy('name')->get();
        $doctorsJson = $doctors->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'service_id' => $d->service_id,
            'room' => $d->room_no,
        ])->values()->toJson();
        $prefill = null;
        if ($request->filled('phone')) {
            $prefill = Patient::where('phone', Patient::normalizePhone($request->input('phone')))->latest()->first();
        }

        return view('tokens.create', compact('services', 'doctors', 'doctorsJson', 'prefill'));
    }

    public function store(StoreTokenRequest $request, TokenService $service)
    {
        $token = $service->issue(
            $request->only(['name', 'phone', 'age', 'gender', 'address']),
            (int) $request->input('service_id'),
            (int) $request->input('doctor_id'),
            $request->user()->id
        );

        return redirect()->route('tokens.show', $token)->with('success', 'Token '.$token->token_no.' issued.');
    }

    public function show(Token $token)
    {
        $this->authorize('view', $token);
        $token->load(['patient', 'service', 'doctor', 'counter']);

        return view('tokens.show', compact('token'));
    }

    public function print(Token $token)
    {
        $this->authorize('view', $token);
        $token->load(['patient', 'service', 'doctor', 'counter']);

        return view('tokens.print', compact('token'));
    }
}
