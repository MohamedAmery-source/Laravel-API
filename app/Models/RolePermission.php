<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'role_id',
        'permission_id',
        'granted',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'permission_id');
    }
}
