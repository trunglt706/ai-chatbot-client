<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $table = 'permission_groups';

    protected $fillable = [
        'name',
    ];
}
