<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Database\Factories;

use App\Modules\Solicitudes\Domain\Entities\Solicitud;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudFactory extends Factory
{
    protected $model = Solicitud::class;

    public function definition(): array
    {
        $documentTypes = [
            'Contrato de Servicios', 'Factura', 'Orden de Compra',
            'Informe Técnico', 'Propuesta Comercial', 'Acta de Reunión',
        ];

        return [
            'nombre_documento' => $this->faker->randomElement($documentTypes)
                . ' - ' . $this->faker->company(),
            'estado' => $this->faker->randomElement(EstadoSolicitud::cases()),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn () => ['estado' => EstadoSolicitud::PENDIENTE]);
    }

    public function aprobado(): static
    {
        return $this->state(fn () => ['estado' => EstadoSolicitud::APROBADO]);
    }

    public function rechazado(): static
    {
        return $this->state(fn () => ['estado' => EstadoSolicitud::RECHAZADO]);
    }

    public function modificar(): static
    {
        return $this->state(fn () => ['estado' => EstadoSolicitud::MODIFICAR]);
    }
}
