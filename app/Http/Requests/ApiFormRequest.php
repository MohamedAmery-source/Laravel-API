<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiFormRequest extends FormRequest
{
    /**
     * Always treat request validation responses as JSON for API consumers.
     */
    public function expectsJson(): bool
    {
        return true;
    }

    /**
     * Always treat request validation responses as JSON for API consumers.
     */
    public function wantsJson(): bool
    {
        return true;
    }

    /**
     * Return a JSON response instead of redirecting on validation failure.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'data' => $validator->errors(),
        ], 422, [], JSON_UNESCAPED_UNICODE));
    }

    /**
     * Return a JSON response instead of redirecting on authorization failure.
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Forbidden.',
            'data' => null,
        ], 403, [], JSON_UNESCAPED_UNICODE));
    }
}


