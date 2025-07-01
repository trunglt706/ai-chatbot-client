<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class RoleService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAllChildRoleIds(Role $role): array
    {
        $childIds = $role->children()->pluck('id')->toArray();

        foreach ($role->children as $child) {
            $childIds = array_merge($childIds, $this->getAllChildRoleIds($child));
        }

        return $childIds;
    }

    public function getViewableUserIds(Role $role): array
    {
        if (!$role->can_view_children_data) {
            return User::role($role->name)->pluck('id')->toArray();
        }

        $childRoleIds = $this->getAllChildRoleIds($role);

        return User::whereHas('roles', function ($query) use ($role, $childRoleIds) {
            $query->whereIn('id', array_merge([$role->id], $childRoleIds));
        })->pluck('id')->toArray();
    }
}
