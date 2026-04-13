<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiFormRequest extends FormRequest
{
    public function expectsJson(): bool
    {
        return true;
    }

    public function wantsJson(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'فشل التحقق من صحة البيانات المرسلة.',
            'data' => $validator->errors(),
        ], 422, [], JSON_UNESCAPED_UNICODE));
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'غير مصرح لك بتنفيذ هذا الإجراء.',
            'data' => null,
        ], 403, [], JSON_UNESCAPED_UNICODE));
    }
}
