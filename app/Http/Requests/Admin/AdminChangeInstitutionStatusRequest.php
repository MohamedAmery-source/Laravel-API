<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiFormRequest;

class AdminChangeInstitutionStatusRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:active,suspended',
        ];
    }
}



