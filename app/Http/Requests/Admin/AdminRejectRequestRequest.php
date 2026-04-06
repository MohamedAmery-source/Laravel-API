<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiFormRequest;

class AdminRejectRequestRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_notes' => 'required|string|min:3',
        ];
    }
}



