<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,pending_admin,pending_institution,under_review,approved,rejected,completed',
            'admin_notes' => 'nullable|string',
            'institution_notes' => 'nullable|string',
        ];
    }
}
