<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id' => 'nullable|exists:institutions,institution_id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'required_skills' => 'nullable|string',
            'available_seats' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'application_deadline' => 'required|date|before:start_date',
        ];
    }
}
