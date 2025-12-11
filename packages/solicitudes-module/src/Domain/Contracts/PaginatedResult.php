<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Contracts;

/**
 * Resultado paginado genérico.
 * PHP puro - Sin dependencias de framework.
 *
 * @template T
 */
final readonly class PaginatedResult
{
    /**
     * @param array<T> $items
     */
    public function __construct(
        private array $items,
        private int $total,
        private int $perPage,
        private int $currentPage,
        private int $lastPage
    ) {}

    /**
     * @return array<T>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Convierte a array para serialización.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                fn($item) => method_exists($item, 'toArray') ? $item->toArray() : $item,
                $this->items
            ),
            'meta' => [
                'total' => $this->total,
                'per_page' => $this->perPage,
                'current_page' => $this->currentPage,
                'last_page' => $this->lastPage,
                'has_more_pages' => $this->hasMorePages(),
            ],
        ];
    }

    /**
     * Factory method para crear desde datos raw.
     *
     * @param array<T> $items
     */
    public static function create(
        array $items,
        int $total,
        int $perPage,
        int $currentPage
    ): self {
        $lastPage = max(1, (int) ceil($total / $perPage));

        return new self(
            items: $items,
            total: $total,
            perPage: $perPage,
            currentPage: $currentPage,
            lastPage: $lastPage
        );
    }

    /**
     * Crea un resultado vacío.
     */
    public static function empty(int $perPage = 15): self
    {
        return new self(
            items: [],
            total: 0,
            perPage: $perPage,
            currentPage: 1,
            lastPage: 1
        );
    }
}
