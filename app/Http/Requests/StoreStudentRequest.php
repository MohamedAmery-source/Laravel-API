<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,user_id|unique:students,user_id',
            'student_number' => 'required|string|max:20|unique:students,student_number',
            'department' => 'required|string|max:100',
            'level' => 'required|string|max:10',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
