<?php

namespace App\Http\Requests\Admin;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('id');
        $userId = Student::query()->where('student_id', $studentId)->value('user_id');

        return [
            'full_name' => 'sometimes|string|max:150',
            'email' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId, 'user_id'),
            ],
            'phone' => 'nullable|string|max:20',
            'department' => 'sometimes|string|max:100',
            'level' => 'sometimes|string|max:10',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'status' => 'sometimes|in:active,suspended',
        ];
    }
}
