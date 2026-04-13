<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreTrainingRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,student_id',
            'opportunity_id' => 'required|exists:training_opportunities,opportunity_id',
            'submission_date' => 'nullable|date',
            'student_notes' => 'nullable|string',
            'student_answers_block' => 'nullable|string',
        ];
    }
}



