<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        // نجعلها true مؤقتاً، في المستوى القادم سنربطها بصلاحيات المؤسسة فقط
        return true; 
    }

    public function rules(): array
    {
        // هنا نكتب الشروط بناءً على "قاموس البيانات" الخاص بك
        return [
            'institution_id' => 'required|exists:institutions,institution_id', // يجب أن يكون مطلوباً وموجوداً في جدول المؤسسات
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'required_skills' => 'nullable|string',
            'available_seats' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date', // تاريخ النهاية يجب أن يكون بعد تاريخ البداية
            'application_deadline' => 'required|date|before:start_date', // آخر موعد للتقديم يجب أن يكون قبل بدء التدريب
        ];
    }
}



  