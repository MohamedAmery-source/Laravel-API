<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingOpportunity; 
use App\Http\Requests\StoreOpportunityRequest; // ضروري جداً لاستقبال ملف التحقق

class OpportunityController extends Controller
{
    /**
     * جلب جميع الفرص التدريبية الفعالة
     */
    public function index()
    {
        $opportunities = TrainingOpportunity::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب فرص التدريب الحقيقية بنجاح',
            'data' => $opportunities
        ], 200, [], JSON_UNESCAPED_UNICODE); 
    }

    /**
     * إضافة فرصة تدريبية جديدة
     */
    public function store(StoreOpportunityRequest $request)
    {
        // 1. استلام البيانات التي تم التحقق منها تلقائياً عبر StoreOpportunityRequest
        $validatedData = $request->validated();

        // 2. إنشاء الفرصة في قاعدة البيانات
        $opportunity = TrainingOpportunity::create($validatedData);

        // 3. إرجاع رد نجاح
        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الفرصة التدريبية بنجاح!',
            'data' => $opportunity
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }
}