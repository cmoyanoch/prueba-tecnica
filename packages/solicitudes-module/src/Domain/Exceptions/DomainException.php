<?php

declare(strict_types=1);

namespace SolicitudesModule\Domain\Exceptions;

use Exception;

/**
 * Excepción base del dominio.
 * Todas las excepciones del módulo deben extender de esta.
 */
abstract class DomainException extends Exception
{
    protected function __construct(
        string $message,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Código de error único para identificar el tipo de excepción.
     */
    abstract public function errorCode(): string;
}
