<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء مستخدم جديد (مؤسسة)
        $user = User::create([
            'full_name' => 'مسؤول شركة التقنية',
            'email' => 'company@test.com',
            'password' => Hash::make('password123'), // تشفير كلمة المرور
            'user_type' => 'institution',
            'status' => 'active'
        ]);

        // 2. إنشاء ملف المؤسسة وربطه بالمستخدم
        Institution::create([
            'user_id' => $user->user_id, // نأخذ الآيدي من المستخدم الذي أنشأناه للتو
            'name' => 'شركة التقنية الحديثة',
            'address' => 'الرياض - طريق الملك فهد',
            'description' => 'شركة رائدة في مجال البرمجيات',
            'contact_phone' => '0500000000'
        ]);
    }
}