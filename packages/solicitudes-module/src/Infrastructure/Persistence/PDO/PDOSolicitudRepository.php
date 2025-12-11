<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Persistence\PDO;

use DateTimeImmutable;
use PDO;
use SolicitudesModule\Domain\Contracts\PaginatedResult;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Implementación del repositorio usando PDO puro.
 * Sin dependencias de framework - PHP puro.
 */
final class PDOSolicitudRepository implements SolicitudRepositoryInterface
{
    private const TABLE = 'solicitudes';

    public function __construct(
        private readonly PDO $pdo
    ) {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function save(Solicitud $solicitud): void
    {
        if ($solicitud->id() !== null) {
            $this->update($solicitud);
        } else {
            $this->insert($solicitud);
        }
    }

    private function insert(Solicitud $solicitud): void
    {
        $sql = sprintf(
            'INSERT INTO %s (nombre_documento, estado, created_at, updated_at) VALUES (:nombre_documento, :estado, :created_at, :updated_at)',
            self::TABLE
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nombre_documento' => $solicitud->nombreDocumento()->value(),
            'estado' => $solicitud->estado()->value,
            'created_at' => $solicitud->createdAt()->format('Y-m-d H:i:s'),
            'updated_at' => $solicitud->updatedAt()->format('Y-m-d H:i:s'),
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $solicitud->assignId(SolicitudId::fromInt($id));
    }

    private function update(Solicitud $solicitud): void
    {
        $sql = sprintf(
            'UPDATE %s SET nombre_documento = :nombre_documento, estado = :estado, updated_at = :updated_at WHERE id = :id',
            self::TABLE
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $solicitud->id()->value(),
            'nombre_documento' => $solicitud->nombreDocumento()->value(),
            'estado' => $solicitud->estado()->value,
            'updated_at' => $solicitud->updatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function findById(SolicitudId $id): ?Solicitud
    {
        $sql = sprintf('SELECT * FROM %s WHERE id = :id', self::TABLE);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id->value()]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->toDomainEntity($row);
    }

    public function findByIdOrFail(SolicitudId $id): Solicitud
    {
        $solicitud = $this->findById($id);

        if ($solicitud === null) {
            throw SolicitudNotFoundException::withId($id);
        }

        return $solicitud;
    }

    public function findAll(): array
    {
        $sql = sprintf('SELECT * FROM %s ORDER BY id DESC', self::TABLE);
        $stmt = $this->pdo->query($sql);

        $solicitudes = [];
        while ($row = $stmt->fetch()) {
            $solicitudes[] = $this->toDomainEntity($row);
        }

        return $solicitudes;
    }

    public function findAllPaginated(int $page = 1, int $perPage = 15): PaginatedResult
    {
        $total = $this->count();
        $offset = ($page - 1) * $perPage;

        $sql = sprintf(
            'SELECT * FROM %s ORDER BY id DESC LIMIT :limit OFFSET :offset',
            self::TABLE
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = [];
        while ($row = $stmt->fetch()) {
            $items[] = $this->toDomainEntity($row);
        }

        return PaginatedResult::create(
            items: $items,
            total: $total,
            perPage: $perPage,
            currentPage: $page
        );
    }

    public function findByEstado(EstadoSolicitud $estado): array
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE estado = :estado ORDER BY id DESC',
            self::TABLE
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['estado' => $estado->value]);

        $solicitudes = [];
        while ($row = $stmt->fetch()) {
            $solicitudes[] = $this->toDomainEntity($row);
        }

        return $solicitudes;
    }

    public function delete(Solicitud $solicitud): void
    {
        if ($solicitud->id() === null) {
            return;
        }

        $this->deleteById($solicitud->id());
    }

    public function deleteById(SolicitudId $id): bool
    {
        $sql = sprintf('DELETE FROM %s WHERE id = :id', self::TABLE);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id->value()]);

        return $stmt->rowCount() > 0;
    }

    public function count(): int
    {
        $sql = sprintf('SELECT COUNT(*) FROM %s', self::TABLE);
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function countByEstado(EstadoSolicitud $estado): int
    {
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE estado = :estado', self::TABLE);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['estado' => $estado->value]);

        return (int) $stmt->fetchColumn();
    }

    public function exists(SolicitudId $id): bool
    {
        $sql = sprintf('SELECT 1 FROM %s WHERE id = :id', self::TABLE);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id->value()]);

        return $stmt->fetchColumn() !== false;
    }

    public function nextIdentity(): ?SolicitudId
    {
        return null;
    }

    /**
     * Convierte un row de BD a entidad de dominio.
     *
     * @param array<string, mixed> $row
     */
    private function toDomainEntity(array $row): Solicitud
    {
        return Solicitud::reconstitute(
            id: SolicitudId::fromInt((int) $row['id']),
            nombreDocumento: NombreDocumento::fromString($row['nombre_documento']),
            estado: EstadoSolicitud::from($row['estado']),
            createdAt: new DateTimeImmutable($row['created_at']),
            updatedAt: new DateTimeImmutable($row['updated_at'])
        );
    }
}
