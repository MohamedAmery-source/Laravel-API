<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingOpportunity extends Model
{
    use HasFactory;

    // 1. تحديد اسم الجدول (لأن اسم الجدول لدينا بصيغة الجمع واسم المودل مفرد)
    protected $table = 'training_opportunities';

    // 2. تحديد المفتاح الأساسي (Primary Key) لأننا لم نستخدم id الافتراضي
    protected $primaryKey = 'opportunity_id';

    // 3. تحديد الأعمدة المسموح تعبئتها (حماية من ثغرات Mass Assignment)
    protected $fillable = [
        'institution_id',
        'title',
        'description',
        'required_skills',
        'available_seats',
        'start_date',
        'end_date',
        'application_deadline',
        'is_active',
        'created_by',
        'updated_by'
    ];

    // 4. (خطوة متقدمة) ربط الفرصة بالمؤسسة التي طرحتها
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id', 'institution_id');
    }
}