<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // 1. تحديد اسم المفتاح الأساسي (لأننا لم نستخدم id)
    protected $primaryKey = 'student_id';

    // 2. السماح بتعبئة هذه البيانات
    protected $fillable = [
        'user_id',
        'student_number',
        'department',
        'level',
        'gpa',
        'is_active'
    ];

    // 3. علاقة عكسية (اختياري لكن مفيد): الطالب يتبع لمستخدم
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}