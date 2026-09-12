<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'date'],
            'service_id' => ['nullable', 'exists:services,id'],
            'status' => ['nullable', 'in:waiting,calling,serving,completed,skipped,cancelled'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $date = $request->input('date', Carbon::today()->toDateString());
        $tokens = Token::with(['patient', 'service', 'doctor', 'counter'])
            ->whereDate('token_date', $date)
            ->when($request->service_id, fn ($q, $v) => $q->where('service_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->q, fn ($q, $v) => $q->where(function ($w) use ($v) {
                $w->where('token_no', 'like', "%{$v}%")
                    ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%"));
            }))
            ->orderBy('seq')->paginate(20)->withQueryString();

        $services = Service::orderBy('name')->get();

        return view('reports.index', compact('tokens', 'services', 'date'));
    }
}
