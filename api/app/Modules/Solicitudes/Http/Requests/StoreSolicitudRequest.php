<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_documento' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_documento.required' => 'El nombre del documento es obligatorio.',
            'nombre_documento.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre_documento.max' => 'El nombre no puede exceder 255 caracteres.',
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
