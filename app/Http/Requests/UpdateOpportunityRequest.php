<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id' => 'sometimes|exists:institutions,institution_id',
            'title' => 'sometimes|string|max:200',
            'description' => 'nullable|string',
            'required_skills' => 'nullable|string',
            'available_seats' => 'sometimes|integer|min:1',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'application_deadline' => 'sometimes|date|before:start_date',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
