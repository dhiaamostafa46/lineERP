<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public $table = 'roles';

    public $fillable = [
        'name',
        'org_id',
        'guard_name'
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'guard_name' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
    ];
}
