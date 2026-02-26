<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // نحتاجها للعمليات المركبة

class AuthController extends Controller
{
    // دالة إنشاء حساب جديد
    public function register(Request $request)
    {
        // 1. التحقق من البيانات الأساسية للمستخدم
        $fields = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed', // يجب إرسال password_confirmation
            'user_type' => 'required|in:student,institution', // نقبل فقط طالب أو مؤسسة حالياً
            // بيانات إضافية حسب النوع يمكن التحقق منها هنا
        ]);

        // نستخدم DB::transaction لضمان أن يتم إنشاء الجدولين معاً أو لا شيء (للحفاظ على سلامة البيانات)
        return DB::transaction(function () use ($fields, $request) {
            
            // 2. إنشاء المستخدم في جدول users
            $user = User::create([
                'full_name' => $fields['full_name'],
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
                'user_type' => $fields['user_type'],
                'status' => 'active'
            ]);

            // 3. التحقق من النوع وإنشاء الملف الشخصي المناسب
            if ($fields['user_type'] === 'student') {
                Student::create([
                    'user_id' => $user->user_id,
                    // سنضع قيم افتراضية أو نطلبها من الريكويست
                    'student_number' => $request->student_number ?? 'STU-' . rand(1000,9999), 
                    'department' => $request->department ?? 'General',
                    'level' => $request->level ?? 'Level 1',
                ]);
            } elseif ($fields['user_type'] === 'institution') {
                Institution::create([
                    'user_id' => $user->user_id,
                    'name' => $request->institution_name ?? $fields['full_name'], // إذا لم يرسل اسم مؤسسة نستخدم الاسم الكامل
                ]);
            }

            // 4. إنشاء التوكن (Token) - هذا هو "مفتاح الدخول" الرقمي
            $token = $user->createToken('auth_token')->plainTextToken;

            // 5. إرجاع الرد
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحساب بنجاح',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
        });
    }

    // دالة تسجيل الدخول
    public function login(Request $request)
    {
        // 1. التحقق من المدخلات
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // 2. البحث عن المستخدم
        $user = User::where('email', $fields['email'])->first();

        // 3. التحقق من الباسورد
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        // 4. إنشاء توكن جديد
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }
    
    // دالة تسجيل الخروج
    public function logout(Request $request)
    {
        // حذف التوكن الحالي
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}