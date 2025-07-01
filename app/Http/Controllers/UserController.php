<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware(['auth', 'permission:view users'])->only('index', 'show');
        $this->middleware(['auth', 'permission:create users'])->only('create', 'store');
        $this->middleware(['auth', 'permission:edit users'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:delete users'])->only('destroy');
    }

    public function index()
    {
        $search = request('search', '');
        $pageSize = request('pageSize', 10);

        $users = User::query();
        $users = $search ? $users->search($search) : $users;
        $users = $users->paginate($pageSize);
        $users->appends(['search' => $search]);

        $onlineStatuses = $this->userService->getOnlineStatusesForUsers($users);

        return view('users.index', compact('users', 'onlineStatuses', 'search'));
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

    /**
     * Xóa một phiên đăng nhập cụ thể của người dùng.
     */
    public function destroySession(Request $request, User $user, $sessionId)
    {
        $currentSessionId = session()->getId();

        // Đảm bảo chỉ xóa phiên của người dùng đang được chỉnh sửa
        DB::table('sessions')->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->delete();

        if ($sessionId === $currentSessionId && Auth::id() === $user->id) {
            // Nếu admin đang tự xóa phiên hiện tại của mình, cần đăng xuất
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('success', __('You have been logged out from this session.'));
        }

        return back()->with('success', __('The login session has been deleted.'));
    }

    /**
     * Đăng xuất người dùng khỏi tất cả các thiết bị.
     */
    public function logoutAllDevices(Request $request, User $user)
    {
        DB::table('sessions')->where('user_id', $user->id)
            ->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return back()->with('success', __('You have been logged out from all other devices.'));
    }
}
