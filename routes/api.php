<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OpportunityController; // استدعاء المتحكم
use App\Http\Controllers\Api\AuthController;

// مسارات عامة (لا تتطلب تسجيل دخول)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// مسارات محمية (تتطلب توكن) - سننقل مسار إضافة الفرص هنا لاحقاً
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // هنا سنضع المسارات التي تحتاج حماية مستقبلاً
});

// هذا الرابط سيعرض جميع الفرص
Route::get('/opportunities', [OpportunityController::class, 'index']);
Route::post('/opportunities', [OpportunityController::class, 'store']);
