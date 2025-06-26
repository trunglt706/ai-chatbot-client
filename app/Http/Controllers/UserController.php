<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view users'])->only('index', 'show');
        $this->middleware(['auth', 'permission:create users'])->only('create', 'store');
        $this->middleware(['auth', 'permission:edit users'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:delete users'])->only('destroy');
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'description' => $request->description
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles ?? []);
        }

        return redirect()->route('users.index')->with('success', __('User created successfully!'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        $activities = Activity::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->latest()
            ->take(10)
            ->with('causer')
            ->get();
        return view('users.edit', compact('user', 'roles', 'userRoles', 'activities'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);
        $blocked_account = request('blocked_account', 0);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'status' => $blocked_account == 1 ? User::BLOCKED : $user->status,
            'description' => $request->description
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles ?? []);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')->with('success', __('User updated successfully!'));
    }

    public function destroy(User $user)
    {
        if (!$user || $user == Auth::user() || $user->root) abort(403);
        $user->delete();
        return redirect()->route('users.index')->with('success', __('User deleted successfully!'));
    }
}
