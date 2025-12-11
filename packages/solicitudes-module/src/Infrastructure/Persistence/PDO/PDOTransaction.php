<?php

declare(strict_types=1);

namespace SolicitudesModule\Infrastructure\Persistence\PDO;

use PDO;
use SolicitudesModule\Domain\Contracts\TransactionInterface;

/**
 * Implementación de transacciones usando PDO.
 */
final class PDOTransaction implements TransactionInterface
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    public function execute(callable $callback): mixed
    {
        $this->begin();

        try {
            $result = $callback();
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }
}
