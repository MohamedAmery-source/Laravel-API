<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $primaryKey = 'institution_id'; // لأننا لم نستخدم id الافتراضي

    protected $fillable = [
        'user_id',
        'name',
        'commercial_register',
        'address',
        'description',
        'website',
        'social_links',
        'contact_person',
        'contact_phone',
        'is_active'
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function trainingOpportunities()
    {
        return $this->hasMany(TrainingOpportunity::class, 'institution_id', 'institution_id');
    }
}
