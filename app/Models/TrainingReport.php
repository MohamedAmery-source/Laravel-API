<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingReport extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'internship_id',
        'title',
        'content',
        'report_file',
        'week_number',
        'submitted_by',
        'submission_date',
        'is_approved',
        'supervisor_comments',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id', 'internship_id');
    }
}
