<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'document_id';

    protected $fillable = [
        'user_id',
        'request_id',
        'title',
        'file_url',
        'file_type',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function trainingRequest()
    {
        return $this->belongsTo(TrainingRequest::class, 'request_id', 'request_id');
    }
}
