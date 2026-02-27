<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student') ?? $this->route('id');

        return [
            'user_id' => [
                'sometimes',
                'exists:users,user_id',
                Rule::unique('students', 'user_id')->ignore($studentId, 'student_id'),
            ],
            'student_number' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('students', 'student_number')->ignore($studentId, 'student_id'),
            ],
            'department' => 'sometimes|string|max:100',
            'level' => 'sometimes|string|max:10',
            'gpa' => 'sometimes|numeric|min:0|max:5',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
