<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookupMaster extends Model
{
    use HasFactory;

    protected $primaryKey = 'lookup_id';

    protected $fillable = [
        'lookup_code',
        'description',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function values()
    {
        return $this->hasMany(LookupValue::class, 'lookup_id', 'lookup_id');
    }
}
