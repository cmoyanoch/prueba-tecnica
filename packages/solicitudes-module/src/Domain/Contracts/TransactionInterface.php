<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Contracts;

/**
 * Contrato para manejo de transacciones.
 * Permite operaciones atómicas sin acoplarse a la implementación.
 */
interface TransactionInterface
{
    /**
     * Ejecuta el callback dentro de una transacción.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function execute(callable $callback): mixed;

    /**
     * Inicia una transacción manualmente.
     */
    public function begin(): void;

    /**
     * Confirma la transacción actual.
     */
    public function commit(): void;

    /**
     * Revierte la transacción actual.
     */
    public function rollback(): void;
}
