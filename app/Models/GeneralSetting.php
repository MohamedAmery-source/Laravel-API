<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'site_name',
        'site_logo',
        'system_status',
        'content_email',
        'content_phone',
        'privacy_policy',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function systemStatus()
    {
        return $this->belongsTo(LookupValue::class, 'system_status', 'value_id');
    }
}
