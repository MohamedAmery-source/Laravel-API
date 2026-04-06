<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class UploadDocumentRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'title' => 'nullable|string|max:150',
            'file_type' => 'nullable|string|max:50',
            'request_id' => 'nullable|exists:training_requests,request_id',
        ];
    }
}



