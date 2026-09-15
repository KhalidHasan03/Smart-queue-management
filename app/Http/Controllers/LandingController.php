<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LandingController extends Controller
{
    protected function shared()
    {
        return [
            'clinicName' => Setting::get('clinic_name', 'Queue-Pro Hospital'),
            'clinicAddress' => Setting::get('clinic_address', ''),
            'services' => Service::where('is_active', true)->withCount(['doctors' => fn ($q) => $q->where('is_active', true)])->get(),
        ];
    }

    public function index()
    {
        $data = $this->shared();

        $today = Carbon::today()->toDateString();
        $base = Token::whereDate('token_date', $today);
        $data['stats'] = [
            'waiting' => (clone $base)->where('status', 'waiting')->count(),
            'calling' => (clone $base)->where('status', 'calling')->count(),
            'serving' => (clone $base)->where('status', 'serving')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'total' => (clone $base)->count(),
        ];

        $data['reviews'] = Review::where('is_approved', true)
            ->with('token')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn (Review $r) => [
                'rating' => $r->rating,
                'comment' => $r->comment,
                'display_name' => $r->display_name,
                'token_no' => $r->token->token_no ?? '',
                'date' => $r->created_at->format('d M Y'),
            ]);

        $data['totalDoctors'] = Doctor::where('is_active', true)->count();
        $data['totalPatients'] = \App\Models\Patient::count();
        $data['totalTokens'] = Token::whereDate('token_date', $today)->count();

        return view('landing.index', $data);
    }

    public function about()
    {
        return view('landing.about', $this->shared());
    }

    public function services()
    {
        $data = $this->shared();
        $data['allServices'] = Service::where('is_active', true)
            ->with(['doctors' => fn ($q) => $q->where('is_active', true)])
            ->get();
        $data['totalDoctors'] = Doctor::where('is_active', true)->count();
        $data['totalCounters'] = Counter::where('is_active', true)->count();
        return view('landing.services', $data);
    }

    public function features()
    {
        return view('landing.features', $this->shared());
    }

    public function contact()
    {
        return view('landing.contact', $this->shared());
    }

    public function pricing()
    {
        return view('landing.pricing', $this->shared());
    }

    public function industry()
    {
        return view('landing.industry', $this->shared());
    }

    public function verifyToken(Request $request)
    {
        $request->validate([
            'token_no' => 'required|string|max:20',
            'patient_name' => 'required|string|max:255',
        ]);

        $token = Token::with(['patient', 'doctor', 'service'])
            ->where('token_no', $request->token_no)
            ->where('status', 'completed')
            ->first();

        if (! $token) {
            return response()->json(['message' => 'Token not found or not yet completed.'], 404);
        }

        $patientName = trim(strtolower($token->patient->name));
        $inputName = trim(strtolower($request->patient_name));

        if (! str_contains($patientName, $inputName) && ! str_contains($inputName, $patientName)) {
            return response()->json(['message' => 'Name does not match this token.'], 404);
        }

        $existing = Review::where('token_id', $token->id)->where('patient_id', $token->patient_id)->first();
        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this visit.'], 422);
        }

        return response()->json([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'token_no' => $token->token_no,
            'doctor' => $token->doctor->name ?? '',
            'service' => $token->service->name ?? '',
            'date' => $token->token_date->format('d M Y'),
        ]);
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'token_id' => 'required|exists:tokens,id',
            'patient_id' => 'required|exists:patients,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'display_name' => 'nullable|string|max:100',
        ]);

        $existing = Review::where('token_id', $request->token_id)
            ->where('patient_id', $request->patient_id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this visit.'], 422);
        }

        $review = Review::create([
            'token_id' => $request->token_id,
            'patient_id' => $request->patient_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'display_name' => $request->display_name,
            'is_approved' => false,
        ]);

        $token = $review->token;

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => [
                'rating' => $review->rating,
                'comment' => $review->comment,
                'display_name' => $review->display_name,
                'token_no' => $token->token_no ?? '',
                'date' => $review->created_at->format('d M Y'),
            ],
        ]);
    }

    public function getReviews()
    {
        $reviews = Review::where('is_approved', true)
            ->with('token')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn (Review $r) => [
                'rating' => $r->rating,
                'comment' => $r->comment,
                'display_name' => $r->display_name,
                'token_no' => $r->token->token_no ?? '',
                'date' => $r->created_at->format('d M Y'),
            ]);

        return response()->json($reviews);
    }
}
