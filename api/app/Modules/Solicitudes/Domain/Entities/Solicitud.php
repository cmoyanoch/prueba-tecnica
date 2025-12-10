<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Domain\Entities;

use App\Modules\Solicitudes\Database\Factories\SolicitudFactory;
use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return SolicitudFactory::new();
    }

    protected $table = 'solicitudes';

    protected $fillable = [
        'nombre_documento',
        'estado',
    ];

    protected $casts = [
        'estado' => EstadoSolicitud::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Determina si la solicitud puede ser aprobada o rechazada.
     * Solo las solicitudes en estado pendiente o modificar pueden ser aprobadas.
     */
    public function puedeSerAprobada(): bool
    {
        return in_array($this->estado, [
            EstadoSolicitud::PENDIENTE,
            EstadoSolicitud::MODIFICAR,
        ], true);
    }

    /**
     * Determina si la solicitud puede ser eliminada.
     * Por defecto, todas las solicitudes pueden ser eliminadas.
     */
    public function puedeSerEliminada(): bool
    {
        return true;
    }

    /**
     * Determina si la solicitud puede ser modificada.
     * Solo las solicitudes aprobadas o rechazadas pueden ser modificadas.
     */
    public function puedeSerModificada(): bool
    {
        return in_array($this->estado, [
            EstadoSolicitud::APROBADO,
            EstadoSolicitud::RECHAZADO,
        ], true);
    }
}
