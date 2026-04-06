<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreComplaintRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'status' => 'sometimes|in:pending,in_progress,resolved',
            'resolved_at' => 'nullable|date',
        ];
    }
}



