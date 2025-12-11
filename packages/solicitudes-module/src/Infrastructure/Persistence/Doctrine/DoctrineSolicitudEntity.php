<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Persistence\Doctrine;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidad Doctrine para la tabla solicitudes.
 * Este es el único lugar donde usamos dependencias de Symfony/Doctrine.
 */
#[ORM\Entity]
#[ORM\Table(name: 'solicitudes')]
class DoctrineSolicitudEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(name: 'nombre_documento', type: 'string', length: 255)]
    public string $nombreDocumento;

    #[ORM\Column(type: 'string', length: 50)]
    public string $estado;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    public DateTimeImmutable $updatedAt;
}
