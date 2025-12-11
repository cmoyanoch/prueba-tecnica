<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Persistence\Doctrine;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SolicitudesModule\Domain\Contracts\PaginatedResult;
use SolicitudesModule\Domain\Contracts\SolicitudRepositoryInterface;
use SolicitudesModule\Domain\Entities\Solicitud;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;
use SolicitudesModule\Domain\Exceptions\SolicitudNotFoundException;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

/**
 * Implementación del repositorio usando Doctrine ORM (Symfony).
 */
final class DoctrineSolicitudRepository implements SolicitudRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->repository = $entityManager->getRepository(DoctrineSolicitudEntity::class);
    }

    public function save(Solicitud $solicitud): void
    {
        if ($solicitud->id() !== null) {
            // Update existente
            $entity = $this->repository->find($solicitud->id()->value());
            if ($entity !== null) {
                $entity->nombreDocumento = $solicitud->nombreDocumento()->value();
                $entity->estado = $solicitud->estado()->value;
                $entity->updatedAt = $solicitud->updatedAt();
            }
        } else {
            // Crear nuevo
            $entity = new DoctrineSolicitudEntity();
            $entity->nombreDocumento = $solicitud->nombreDocumento()->value();
            $entity->estado = $solicitud->estado()->value;
            $entity->createdAt = $solicitud->createdAt();
            $entity->updatedAt = $solicitud->updatedAt();
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        if ($solicitud->id() === null && isset($entity)) {
            $solicitud->assignId(SolicitudId::fromInt($entity->id));
        }
    }

    public function findById(SolicitudId $id): ?Solicitud
    {
        $entity = $this->repository->find($id->value());

        if ($entity === null) {
            return null;
        }

        return $this->toDomainEntity($entity);
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
        $entities = $this->repository->findBy([], ['id' => 'DESC']);

        return array_map(
            fn($entity) => $this->toDomainEntity($entity),
            $entities
        );
    }

    public function findAllPaginated(int $page = 1, int $perPage = 15): PaginatedResult
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        // Count total
        $countQb = clone $qb;
        $total = (int) $countQb
            ->select('COUNT(s.id)')
            ->from(DoctrineSolicitudEntity::class, 's')
            ->getQuery()
            ->getSingleScalarResult();

        // Get items
        $offset = ($page - 1) * $perPage;
        $entities = $qb
            ->select('s')
            ->from(DoctrineSolicitudEntity::class, 's')
            ->orderBy('s.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        $items = array_map(
            fn($entity) => $this->toDomainEntity($entity),
            $entities
        );

        return PaginatedResult::create(
            items: $items,
            total: $total,
            perPage: $perPage,
            currentPage: $page
        );
    }

    public function findByEstado(EstadoSolicitud $estado): array
    {
        $entities = $this->repository->findBy(
            ['estado' => $estado->value],
            ['id' => 'DESC']
        );

        return array_map(
            fn($entity) => $this->toDomainEntity($entity),
            $entities
        );
    }

    public function delete(Solicitud $solicitud): void
    {
        if ($solicitud->id() === null) {
            return;
        }

        $entity = $this->repository->find($solicitud->id()->value());
        
        if ($entity !== null) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }
    }

    public function deleteById(SolicitudId $id): bool
    {
        $entity = $this->repository->find($id->value());

        if ($entity === null) {
            return false;
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return true;
    }

    public function count(): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(DoctrineSolicitudEntity::class, 's')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByEstado(EstadoSolicitud $estado): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(DoctrineSolicitudEntity::class, 's')
            ->where('s.estado = :estado')
            ->setParameter('estado', $estado->value)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function exists(SolicitudId $id): bool
    {
        return $this->repository->find($id->value()) !== null;
    }

    public function nextIdentity(): ?SolicitudId
    {
        return null;
    }

    /**
     * Convierte una entidad Doctrine a entidad de dominio.
     */
    private function toDomainEntity(DoctrineSolicitudEntity $entity): Solicitud
    {
        return Solicitud::reconstitute(
            id: SolicitudId::fromInt($entity->id),
            nombreDocumento: NombreDocumento::fromString($entity->nombreDocumento),
            estado: EstadoSolicitud::from($entity->estado),
            createdAt: $entity->createdAt,
            updatedAt: $entity->updatedAt
        );
    }
}
