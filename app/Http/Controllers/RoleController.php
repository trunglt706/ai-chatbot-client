<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view roles'])->only('index', 'show');
        $this->middleware(['auth', 'permission:create roles'])->only('create', 'store');
        $this->middleware(['auth', 'permission:edit roles'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:delete roles'])->only('destroy');
    }

    public function index()
    {
        $roles = Role::paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        $user = Auth::user();

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log("Role {$role->name} was stored");

        return redirect()->route('roles.index')->with('success', __('Role created successfully!'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . '|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }
        $user = Auth::user();
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log("Role {$role->name} was updated");

        return redirect()->route('roles.index')->with('success', __('Role updated successfully!'));
    }

    public function destroy(Role $role)
    {
        $role->delete();
        $user = Auth::user();
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log("Role {$role->name} was destroyed");
        return redirect()->route('roles.index')->with('success', __('Role deleted successfully!'));
    }
}
