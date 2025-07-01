<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view roles'])->only('index', 'show', 'order');
        $this->middleware(['auth', 'permission:create roles'])->only('create', 'store');
        $this->middleware(['auth', 'permission:edit roles'])->only('edit', 'update', 'updateNestableOrder');
        $this->middleware(['auth', 'permission:delete roles'])->only('destroy');
    }

    public function index()
    {
        $search = request('search', '');
        $pageSize = request('pageSize', 10);

        $roles = Role::query();
        $roles = $search ? $roles->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        }) : $roles;
        $roles = $roles->paginate($pageSize);
        return view('roles.index', compact('roles', 'search'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $roles = Role::active()->get(['id', 'name']);
        return view('roles.create', compact('permissions', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'parent_id' => ['nullable', 'exists:roles,id'],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
            'is_active' => ['boolean'],
            'can_view_child_data' => ['boolean'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'is_root' => $request->boolean('is_root'),
            'is_active' => $request->boolean('is_active'),
            'can_view_child_data' => $request->boolean('can_view_child_data'),
        ]);
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
        $roles = Role::active()->where('id', '<>', $role->id)->get(['id', 'name']);
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'roles'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . '|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
            'parent_id' => ['nullable', 'exists:roles,id'],
            'is_active' => ['boolean'],
            'can_view_child_data' => ['boolean'],
        ]);

        $role->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'is_active' => $request->boolean('is_active'),
            'can_view_child_data' => $request->boolean('can_view_child_data'),
        ]);
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
        if (!$role || $role->isRootRole() || $role->children()->exists()) abort(404);

        DB::transaction(function () use ($role) {
            // Ngắt kết nối vai trò này với tất cả người dùng
            $role->users()->detach();

            // Xóa vai trò
            $role->delete();
        });

        $user = Auth::user();
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log("Role {$role->name} was destroyed");
        return redirect()->route('roles.index')->with('success', __('Role deleted successfully!'));
    }

    /**
     * Display a listing of the roles.
     */
    public function order(Request $request)
    {
        // Lấy tất cả các vai trò, sắp xếp để dễ xây dựng cấu trúc trong view
        // Bạn cần sắp xếp theo parent_id và sau đó là tên/order nếu có.
        // Hoặc chỉ lấy các vai trò gốc và load quan hệ descendants.
        $roles = Role::orderBy('parent_id')->orderBy('name')->get()->keyBy('id');

        // Hàm để xây dựng cây phân cấp (đệ quy)
        $buildTree = function ($parentId = null) use (&$buildTree, $roles) {
            $branch = collect();
            foreach ($roles as $role) {
                if ($role->parent_id === $parentId) {
                    $role->children = $buildTree($role->id);
                    $branch->add($role);
                }
            }
            return $branch;
        };

        $nestedRoles = $buildTree(null); // Bắt đầu xây dựng từ các vai trò gốc

        return view('roles.order', compact('nestedRoles'));
    }

    /**
     * Update the order and parent_id of roles using Nestable.
     * This method will receive the nested data from the frontend.
     */
    public function updateNestableOrder(Request $request)
    {
        $request->validate([
            'nested_roles' => 'required|array',
            'nested_roles.*.id' => 'required|exists:roles,id',
            'nested_roles.*.parent_id' => 'nullable|exists:roles,id',
        ]);

        // Bắt đầu một transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::transaction(function () use ($request) {
            foreach ($request->input('nested_roles') as $roleData) {
                $role = Role::find($roleData['id']);
                if ($role) {
                    // Không cho phép vai trò root thay đổi parent_id của nó
                    if ($role->isRootRole() && !is_null($roleData['parent_id'])) {
                        // Bạn có thể log lỗi hoặc bỏ qua nó
                        continue; // Bỏ qua nếu vai trò root đang cố gắng có parent
                    }

                    // Không cho phép đặt chính nó làm cha của nó
                    if ($role->id == $roleData['parent_id']) {
                        continue;
                    }

                    // Không cho phép đặt vai trò con làm cha của vai trò cha (tránh vòng lặp)
                    if (!is_null($roleData['parent_id'])) {
                        $parentRole = Role::find($roleData['parent_id']);
                        if ($parentRole && $parentRole->descendants->contains($role)) {
                            continue; // Bỏ qua nếu tạo vòng lặp
                        }
                    }

                    $role->parent_id = $roleData['parent_id'];
                    $role->save();
                }
            }
        });

        $user = Auth::user();
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log("Updated role order");

        return response()->json(['message' => __('Role order updated successfully.')]);
    }
}
