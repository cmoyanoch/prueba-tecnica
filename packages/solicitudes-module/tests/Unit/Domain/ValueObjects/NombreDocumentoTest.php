<?php

declare(strict_types=1);

namespace SolicitudesModule\Tests\Unit\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use SolicitudesModule\Domain\Exceptions\InvalidNombreDocumentoException;
use SolicitudesModule\Domain\ValueObjects\NombreDocumento;

final class NombreDocumentoTest extends TestCase
{
    public function test_can_create_valid_nombre_documento(): void
    {
        $nombre = NombreDocumento::fromString('Test Document');

        $this->assertEquals('Test Document', $nombre->value());
        $this->assertEquals('Test Document', (string) $nombre);
    }

    public function test_trims_whitespace(): void
    {
        $nombre = NombreDocumento::fromString('  Test Document  ');

        $this->assertEquals('Test Document', $nombre->value());
    }

    public function test_throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidNombreDocumentoException::class);
        NombreDocumento::fromString('');
    }

    public function test_throws_exception_for_whitespace_only(): void
    {
        $this->expectException(InvalidNombreDocumentoException::class);
        NombreDocumento::fromString('   ');
    }

    public function test_throws_exception_for_too_short(): void
    {
        $this->expectException(InvalidNombreDocumentoException::class);
        NombreDocumento::fromString('ab');
    }

    public function test_throws_exception_for_too_long(): void
    {
        $longString = str_repeat('a', 256);
        
        $this->expectException(InvalidNombreDocumentoException::class);
        NombreDocumento::fromString($longString);
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $nombre1 = NombreDocumento::fromString('Test');
        $nombre2 = NombreDocumento::fromString('Test');

        $this->assertTrue($nombre1->equals($nombre2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $nombre1 = NombreDocumento::fromString('Test');
        $nombre2 = NombreDocumento::fromString('Other');

        $this->assertFalse($nombre1->equals($nombre2));
    }
}
