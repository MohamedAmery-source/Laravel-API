<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'internship_id' => 'required|exists:internships,internship_id',
            'evaluator_type' => 'required|string|max:50',
            'technical_skills' => 'required|integer|min:1|max:5',
            'commitment' => 'required|integer|min:1|max:5',
            'teamwork' => 'required|integer|min:1|max:5',
            'attendance' => 'required|integer|min:1|max:5',
            'overall_rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
            'evaluation_date' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
