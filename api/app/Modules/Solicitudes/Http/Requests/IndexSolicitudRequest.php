<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'El parámetro per_page debe ser un número entero.',
            'per_page.min' => 'El parámetro per_page debe ser al menos 1.',
            'per_page.max' => 'El parámetro per_page no puede exceder 100.',
            'page.integer' => 'El parámetro page debe ser un número entero.',
            'page.min' => 'El parámetro page debe ser al menos 1.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
