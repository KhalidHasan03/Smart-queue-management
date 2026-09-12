<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCounterRequest;
use App\Models\Counter;
use App\Models\Service;
use App\Models\User;

class CounterController extends Controller
{
    public function index()
    {
        $counters = Counter::with(['service', 'currentToken'])->orderBy('name')->paginate(15);
        return view('admin.counters.index', compact('counters'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('admin.counters.create', compact('services'));
    }

    public function store(StoreCounterRequest $request)
    {
        Counter::create($request->validated() + [
            'is_active' => $request->boolean('is_active', true),
            'show_on_display' => $request->boolean('show_on_display', true),
        ]);
        return redirect()->route('admin.counters.index')->with('success', 'Counter created.');
    }

    public function edit(Counter $counter)
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $operators = User::where('role', User::ROLE_OPERATOR)->orderBy('name')->get();
        return view('admin.counters.edit', compact('counter', 'services', 'operators'));
    }

    public function update(StoreCounterRequest $request, Counter $counter)
    {
        $request->validate([
            'operator_ids' => ['nullable', 'array'],
            'operator_ids.*' => ['integer', 'exists:users,id'],
        ]);
        $counter->update($request->validated() + [
            'is_active' => $request->boolean('is_active'),
            'show_on_display' => $request->boolean('show_on_display'),
        ]);
        if ($request->filled('operator_ids')) {
            $ids = User::whereIn('id', $request->input('operator_ids', []))
                ->where('role', User::ROLE_OPERATOR)->pluck('id');
            User::where('counter_id', $counter->id)->update(['counter_id' => null]);
            User::whereIn('id', $ids)->update(['counter_id' => $counter->id]);
        }
        return redirect()->route('admin.counters.index')->with('success', 'Counter updated.');
    }

    public function destroy(Counter $counter)
    {
        if ($counter->operators()->exists()) {
            return back()->with('error', 'Unassign operators first.');
        }
        $counter->delete();
        return redirect()->route('admin.counters.index')->with('success', 'Counter deleted.');
    }
}
