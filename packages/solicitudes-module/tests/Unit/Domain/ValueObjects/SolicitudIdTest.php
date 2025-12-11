<?php

declare(strict_types=1);

namespace SolicitudesModule\Tests\Unit\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use SolicitudesModule\Domain\Exceptions\InvalidSolicitudIdException;
use SolicitudesModule\Domain\ValueObjects\SolicitudId;

final class SolicitudIdTest extends TestCase
{
    public function test_can_create_from_int(): void
    {
        $id = SolicitudId::fromInt(1);

        $this->assertEquals(1, $id->value());
        $this->assertEquals('1', (string) $id);
    }

    public function test_can_create_from_string(): void
    {
        $id = SolicitudId::fromString('42');

        $this->assertEquals(42, $id->value());
    }

    public function test_throws_exception_for_zero(): void
    {
        $this->expectException(InvalidSolicitudIdException::class);
        SolicitudId::fromInt(0);
    }

    public function test_throws_exception_for_negative(): void
    {
        $this->expectException(InvalidSolicitudIdException::class);
        SolicitudId::fromInt(-1);
    }

    public function test_throws_exception_for_non_numeric_string(): void
    {
        $this->expectException(InvalidSolicitudIdException::class);
        SolicitudId::fromString('abc');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $id1 = SolicitudId::fromInt(1);
        $id2 = SolicitudId::fromInt(1);

        $this->assertTrue($id1->equals($id2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $id1 = SolicitudId::fromInt(1);
        $id2 = SolicitudId::fromInt(2);

        $this->assertFalse($id1->equals($id2));
    }
}
