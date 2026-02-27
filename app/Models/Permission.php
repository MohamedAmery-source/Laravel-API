<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'permission_name',
        'module',
        'description',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id', 'permission_id');
    }
}
