<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Rbac;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Rbac::roles();
        $permissions = Rbac::permissions();
        $effective = [];
        foreach (array_keys($roles) as $key) {
            $effective[$key] = Rbac::rolePermissions($key);
        }

        return view('admin.roles.index', compact('roles', 'permissions', 'effective'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'perms' => ['nullable', 'array'],
            'perms.*' => ['nullable', 'array'],
            'perms.*.*' => ['string'],
        ]);

        Rbac::saveOverrides($request->input('perms', []));

        return back()->with('success', 'Roles & permissions updated.');
    }
}
