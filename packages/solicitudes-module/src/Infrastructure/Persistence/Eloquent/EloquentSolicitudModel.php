<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla solicitudes.
 * Este es el único lugar donde usamos dependencias de Laravel.
 *
 * @property int $id
 * @property string $nombre_documento
 * @property string $estado
 * @property string $created_at
 * @property string $updated_at
 */
class EloquentSolicitudModel extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'nombre_documento',
        'estado',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
