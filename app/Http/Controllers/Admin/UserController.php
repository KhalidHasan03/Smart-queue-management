<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = User::assignableRoles(auth()->user());
        $counters = \App\Models\Counter::with('service')->orderBy('name')->get();
        $services = \App\Models\Service::orderBy('name')->get();
        $doctors = \App\Models\Doctor::orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'counters', 'services', 'doctors'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = User::assignableRoles(auth()->user());
        $counters = \App\Models\Counter::with('service')->orderBy('name')->get();
        $services = \App\Models\Service::orderBy('name')->get();
        $doctors = \App\Models\Doctor::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles', 'counters', 'services', 'doctors'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['is_active'] = $request->boolean('is_active');
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
