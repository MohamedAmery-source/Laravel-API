<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'phone' => 'nullable|string|max:20',
            'student_number' => 'required|string|max:20|unique:students,student_number',
            'department' => 'required|string|max:100',
            'level' => 'required|string|max:10',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'status' => 'sometimes|in:active,suspended',
        ];
    }
}
