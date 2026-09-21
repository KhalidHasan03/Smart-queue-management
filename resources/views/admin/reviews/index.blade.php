<x-app-layout>
    @section('page-title', 'Reviews Management')
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight">Reviews Management</h2>
    </x-slot>

    <div class="qc-card p-6">
        <!-- Filters -->
        <form method="GET" class="mb-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Rating</label>
                    <select name="rating" class="qc-input w-full">
                        <option value="">All Ratings</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request()->input('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select name="category" class="qc-input w-full">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ request()->input('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status" class="qc-input w-full">
                        <option value="">All Status</option>
                        <option value="1" {{ request()->boolean('status') ? 'selected' : '' }}>Approved</option>
                        <option value="0" {{ request()->has('status') && !request()->boolean('status') ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Service</label>
                    <select name="service_id" class="qc-input w-full">
                        <option value="">All Services</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" {{ request()->input('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Doctor</label>
                    <select name="doctor_id" class="qc-input w-full">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ request()->input('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Search</label>
                    <input type="text" name="search" class="qc-input w-full" placeholder="Token, name, comment..." value="{{ request()->input('search') }}">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="qc-btn-primary">Filter</button>
                <a href="{{ route('admin.reviews.index') }}" class="qc-btn-secondary">Reset</a>
                <a href="{{ route('admin.reviews.export', request()->query()) }}" class="qc-btn-dark ml-auto">Export CSV</a>
            </div>
        </form>

        <!-- Reviews Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-sm font-medium text-slate-500">
                        <th class="pb-3">Token</th>
                        <th class="pb-3">Date</th>
                        <th class="pb-3">Patient</th>
                        <th class="pb-3">Rating</th>
                        <th class="pb-3">Category</th>
                        <th class="pb-3">Comment</th>
                        <th class="pb-3">Service / Doctor</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 font-mono text-sm">{{ $review->token->token_no ?? 'N/A' }}</td>
                            <td class="py-4 text-sm">{{ $review->token->token_date?->format('d M Y') ?? '' }}</td>
                            <td class="py-4 text-sm">
                                <div>{{ $review->is_anonymous ? 'Anonymous' : ($review->display_name ?? $review->token->patient->name ?? 'Unknown') }}</div>
                                @if($review->is_anonymous)
                                    <span class="text-xs text-slate-400">Anonymous</span>
                                @endif
                            </td>
                            <td class="py-4 text-center">
                                <span class="text-lg">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                <span class="text-sm text-slate-500 ml-1">({{ $review->rating }}/5)</span>
                            </td>
                            <td class="py-4 text-sm">
                                @if($review->category)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">
                                        {{ ucfirst(str_replace('_', ' ', $review->category)) }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 text-sm max-w-xs">
                                <div class="truncate">{{ $review->comment ?? '<span class="text-slate-400">No comment</span>' }}</div>
                            </td>
                            <td class="py-4 text-sm">
                                <div>{{ $review->token->service->name ?? 'N/A' }}</div>
                                <div class="text-slate-500">{{ $review->token->doctor->name ?? 'N/A' }}</div>
                            </td>
                            <td class="py-4 text-center">
                                @if($review->is_approved)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Approved</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                                @endif
                            </td>
                            <td class="py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="qc-btn-secondary text-xs py-1 px-2">View</a>

                                    @if(!$review->is_approved)
                                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="qc-btn-success text-xs py-1 px-2"
                                                    onclick="return confirm('Approve this review?')">Approve</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="qc-btn-warning text-xs py-1 px-2"
                                                    onclick="return confirm('Reject this review?')">Reject</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="qc-btn-danger text-xs py-1 px-2"
                                                onclick="return confirm('Delete this review permanently?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    </div>
</x-app-layout>