<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Http\Requests;

use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => [
                'required',
                'string',
                Rule::in(EstadoSolicitud::values()),
            ],
        ];
    }

    public function messages(): array
    {
        $estados = implode(', ', EstadoSolicitud::values());
        return [
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => "El estado debe ser uno de: {$estados}.",
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
