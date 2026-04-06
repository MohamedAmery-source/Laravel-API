<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiFormRequest;

class AdminApproveRequestRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_notes' => 'nullable|string',
        ];
    }
}



