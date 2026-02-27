<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $primaryKey = 'internship_id';

    protected $fillable = [
        'request_id',
        'actual_start_date',
        'actual_end_date',
        'mentor_name',
        'assigned_tasks',
        'status',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function trainingRequest()
    {
        return $this->belongsTo(TrainingRequest::class, 'request_id', 'request_id');
    }

    public function reports()
    {
        return $this->hasMany(TrainingReport::class, 'internship_id', 'internship_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'internship_id', 'internship_id');
    }
}
