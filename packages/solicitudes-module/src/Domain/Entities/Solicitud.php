<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Entities;

use DateTimeImmutable;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\InvalidStateTransitionException;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Entidad de dominio Solicitud.
 * PHP puro - Sin dependencias de framework.
 * Encapsula la lógica de negocio relacionada con una solicitud.
 */
final class Solicitud
{
    private function __construct(
        private ?SolicitudId $id,
        private NombreDocumento $nombreDocumento,
        private EstadoSolicitud $estado,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {}

    /**
     * Factory method para crear una nueva solicitud.
     */
    public static function create(
        NombreDocumento $nombreDocumento,
        ?EstadoSolicitud $estado = null
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            id: null,
            nombreDocumento: $nombreDocumento,
            estado: $estado ?? EstadoSolicitud::PENDIENTE,
            createdAt: $now,
            updatedAt: $now
        );
    }

    /**
     * Factory method para reconstruir una solicitud desde persistencia.
     */
    public static function reconstitute(
        SolicitudId $id,
        NombreDocumento $nombreDocumento,
        EstadoSolicitud $estado,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            id: $id,
            nombreDocumento: $nombreDocumento,
            estado: $estado,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    /**
     * Asigna el ID después de persistir.
     */
    public function assignId(SolicitudId $id): void
    {
        if ($this->id !== null) {
            throw new \LogicException('El ID ya fue asignado');
        }
        $this->id = $id;
    }

    /**
     * Cambia el estado de la solicitud validando las transiciones permitidas.
     *
     * @throws InvalidStateTransitionException
     */
    public function cambiarEstado(EstadoSolicitud $nuevoEstado): void
    {
        if (!$this->estado->canTransitionTo($nuevoEstado)) {
            throw InvalidStateTransitionException::cannotTransition($this->estado, $nuevoEstado);
        }

        $this->estado = $nuevoEstado;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Cambia el estado sin validar transiciones (uso administrativo).
     */
    public function forzarEstado(EstadoSolicitud $nuevoEstado): void
    {
        $this->estado = $nuevoEstado;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Actualiza el nombre del documento.
     */
    public function actualizarNombreDocumento(NombreDocumento $nombreDocumento): void
    {
        $this->nombreDocumento = $nombreDocumento;
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters

    public function id(): ?SolicitudId
    {
        return $this->id;
    }

    public function nombreDocumento(): NombreDocumento
    {
        return $this->nombreDocumento;
    }

    public function estado(): EstadoSolicitud
    {
        return $this->estado;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Métodos de consulta de negocio

    public function puedeSerAprobada(): bool
    {
        return in_array($this->estado, [
            EstadoSolicitud::PENDIENTE,
            EstadoSolicitud::MODIFICAR,
        ], true);
    }

    public function puedeSerRechazada(): bool
    {
        return in_array($this->estado, [
            EstadoSolicitud::PENDIENTE,
            EstadoSolicitud::MODIFICAR,
        ], true);
    }

    public function puedeSerModificada(): bool
    {
        return in_array($this->estado, [
            EstadoSolicitud::APROBADO,
            EstadoSolicitud::RECHAZADO,
        ], true);
    }

    public function puedeSerEliminada(): bool
    {
        return true;
    }

    public function estaPendiente(): bool
    {
        return $this->estado === EstadoSolicitud::PENDIENTE;
    }

    public function estaAprobada(): bool
    {
        return $this->estado === EstadoSolicitud::APROBADO;
    }

    public function estaRechazada(): bool
    {
        return $this->estado === EstadoSolicitud::RECHAZADO;
    }

    /**
     * Convierte la entidad a array para serialización.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id?->value(),
            'nombre_documento' => $this->nombreDocumento->value(),
            'estado' => $this->estado->value,
            'estado_label' => $this->estado->label(),
            'estado_color' => $this->estado->color(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
