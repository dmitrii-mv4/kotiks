<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $guarded = false;

    protected $fillable = [
        'name',
        'access_admin',
        'users_viewAny',
        'users_view',
        'users_create',
        'users_update',
        'users_delete',
        'roles_viewAny',
        'roles_create',
        'roles_update',
        'roles_delete',
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id'
        );
    }
}
