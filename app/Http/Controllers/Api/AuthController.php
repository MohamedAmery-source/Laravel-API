<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Institution;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $fields = $request->validated();

        return DB::transaction(function () use ($fields, $request) {
            $user = User::create([
                'full_name' => $fields['full_name'],
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
                'user_type' => $fields['user_type'],
                'status' => 'active'
            ]);

            if ($fields['user_type'] === 'student') {
                Student::create([
                    'user_id' => $user->user_id,
                    'student_number' => $request->student_number ?? 'STU-' . rand(1000, 9999),
                    'department' => $request->department ?? 'General',
                    'level' => $request->level ?? 'Level 1',
                ]);
            } elseif ($fields['user_type'] === 'institution') {
                Institution::create([
                    'user_id' => $user->user_id,
                    'name' => $request->institution_name ?? $fields['full_name'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->load(['student', 'institution']);

            return $this->success([
                'user' => new UserResource($user),
                'token' => $token
            ], 'تم إنشاء الحساب بنجاح', 201);
        });
    }

    public function login(LoginRequest $request)
    {
        $fields = $request->validated();

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return $this->error('بيانات الدخول غير صحيحة', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load(['student', 'institution']);

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token
        ], 'تم تسجيل الدخول بنجاح', 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    public function changePassword(Request $request)
    {
        $fields = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|confirmed|min:8',
        ]);

        $user = $request->user();

        if (!$user || !Hash::check($fields['current_password'], $user->password)) {
            return $this->error('كلمة المرور الحالية غير صحيحة', 422);
        }

        $user->password = Hash::make($fields['new_password']);
        $user->save();

        return $this->success(null, 'تم تحديث كلمة المرور بنجاح', 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load(['student', 'institution']);

        return $this->success(new UserResource($user), null, 200);
    }
}
