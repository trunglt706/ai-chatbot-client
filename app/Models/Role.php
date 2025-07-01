<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;

class Role extends SpatieRole
{
    // Thêm các thuộc tính mới vào $fillable để có thể gán hàng loạt
    protected $fillable = [
        'name',
        'guard_name',
        'parent_id',        // Thuộc tính mới
        'is_root',          // Thuộc tính mới
        'is_active',        // Thuộc tính mới
        'can_view_child_data', // Thuộc tính mới
    ];

    // --- Quan hệ cha-con (cho Nestable) ---

    public function parent()
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Role::class, 'parent_id');
    }

    // Lấy tất cả các con cháu (recursive)
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // --- Scopes và hàm hỗ trợ ---

    /**
     * Scope to get root roles (roles with no parent).
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Determine if the role is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Determine if the role is a root role (cannot be deleted).
     */
    public function isRootRole(): bool
    {
        return (bool) $this->is_root;
    }

    /**
     * Determine if the role can view data of its child roles.
     */
    public function canViewChildData(): bool
    {
        return (bool) $this->can_view_child_data;
    }

    /**
     * Get all active roles.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
