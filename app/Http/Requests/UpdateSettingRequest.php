<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateSettingRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => 'sometimes|string|max:255',
            'site_logo' => 'nullable|string|max:255',
            'system_status' => 'nullable|exists:lookup_values,value_id',
            'content_email' => 'nullable|email|max:255',
            'content_phone' => 'nullable|string|max:20',
            'privacy_policy' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ];
    }
}



