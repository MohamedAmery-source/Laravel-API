<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'internship_id' => 'required|exists:internships,internship_id',
            'title' => 'nullable|string|max:200',
            'content' => 'nullable|string',
            'report_file' => 'nullable|string|max:255',
            'submitted_by' => 'required|in:student,institution,supervisor',
            'submission_date' => 'nullable|date',
            'is_approved' => 'sometimes|boolean',
            'supervisor_comments' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
