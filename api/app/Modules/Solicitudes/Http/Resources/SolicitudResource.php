<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_documento' => $this->nombre_documento,
            'estado' => [
                'value' => $this->estado->value,
                'label' => $this->estado->label(),
                'color' => $this->estado->color(),
            ],
            'fecha_creacion' => $this->created_at->toIso8601String(),
            'fecha_actualizacion' => $this->updated_at->toIso8601String(),
        ];
    }
}
