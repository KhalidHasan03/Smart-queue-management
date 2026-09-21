<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['token.patient', 'token.service', 'token.doctor', 'token.counter'])
            ->orderByDesc('created_at');

        // Filters
        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('status')) {
            $query->where('is_approved', $request->boolean('status'));
        }
        if ($request->filled('service_id')) {
            $query->whereHas('token', fn ($q) => $q->where('service_id', $request->integer('service_id')));
        }
        if ($request->filled('doctor_id')) {
            $query->whereHas('token', fn ($q) => $q->where('doctor_id', $request->integer('doctor_id')));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhereHas('token', fn ($tq) => $tq->where('token_no', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        $services = Service::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $doctors = Doctor::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = ['service', 'wait_time', 'staff', 'facility', 'overall'];

        return view('admin.reviews.index', compact('reviews', 'services', 'doctors', 'categories'));
    }

    public function show(Review $review)
    {
        $review->load(['token.patient', 'token.service', 'token.doctor', 'token.counter']);

        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved and published.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review rejected and hidden from public.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        // Reset token review status if needed
        $review->token->update(['review_status' => 'none']);

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted permanently.');
    }

    public function export(Request $request)
    {
        $query = Review::with(['token.patient', 'token.service', 'token.doctor', 'token.counter'])
            ->orderByDesc('created_at');

        // Apply same filters as index
        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('status')) {
            $query->where('is_approved', $request->boolean('status'));
        }

        $reviews = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reviews-export-'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function () use ($reviews) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Token No', 'Date', 'Rating', 'Category', 'Comment',
                'Display Name', 'Anonymous', 'Approved', 'Service', 'Doctor', 'Counter',
                'Patient Name', 'Patient Phone', 'Created At',
            ]);

            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->token->token_no ?? '',
                    $review->token->token_date?->format('Y-m-d') ?? '',
                    $review->rating,
                    $review->category ?? '',
                    $review->comment ?? '',
                    $review->display_name ?? '',
                    $review->is_anonymous ? 'Yes' : 'No',
                    $review->is_approved ? 'Yes' : 'No',
                    $review->token->service->name ?? '',
                    $review->token->doctor->name ?? '',
                    $review->token->counter->name ?? '',
                    $review->token->patient->name ?? '',
                    $review->token->patient->phone ?? '',
                    $review->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
