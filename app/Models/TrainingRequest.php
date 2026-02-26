<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'student_id',
        'opportunity_id',
        'submission_date',
        'status',
        'student_notes',
        'admin_notes',
        'institution_notes',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(TrainingOpportunity::class, 'opportunity_id', 'opportunity_id');
    }

    public function internship()
    {
        return $this->hasOne(Internship::class, 'request_id', 'request_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'request_id', 'request_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_request_id', 'request_id');
    }
}
