<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookupValue extends Model
{
    use HasFactory;

    protected $primaryKey = 'value_id';

    protected $fillable = [
        'lookup_id',
        'value_code',
        'description',
        'value_data',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function master()
    {
        return $this->belongsTo(LookupMaster::class, 'lookup_id', 'lookup_id');
    }
}
