<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $primaryKey = 'evaluation_id';

    protected $fillable = [
        'internship_id',
        'evaluator_type',
        'technical_skills',
        'commitment',
        'teamwork',
        'attendance',
        'overall_rating',
        'final_score',
        'comments',
        'evaluation_date',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id', 'internship_id');
    }
}
