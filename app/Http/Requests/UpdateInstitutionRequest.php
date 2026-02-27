<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $institutionId = $this->route('institution') ?? $this->route('id');

        return [
            'user_id' => [
                'sometimes',
                'exists:users,user_id',
                Rule::unique('institutions', 'user_id')->ignore($institutionId, 'institution_id'),
            ],
            'name' => 'sometimes|string|max:150',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
