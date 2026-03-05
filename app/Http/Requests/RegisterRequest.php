<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            // Institution accounts are created by admin through InstitutionController@store
            'user_type' => 'required|in:student',
            'student_number' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:10',
        ];
    }
}
