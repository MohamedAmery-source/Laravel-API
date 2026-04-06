<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class ChangeRequestStatusRequest extends ApiFormRequest
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



