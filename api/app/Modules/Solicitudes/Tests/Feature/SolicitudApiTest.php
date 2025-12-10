<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Tests\Feature;

use App\Modules\Solicitudes\Domain\Entities\Solicitud;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_listar_solicitudes(): void
    {
        Solicitud::factory()->count(5)->create();

        $response = $this->getJson('/api/solicitudes');

        $response
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_puede_crear_solicitud(): void
    {
        $response = $this->postJson('/api/solicitudes', [
            'nombre_documento' => 'Contrato de Prueba',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.nombre_documento', 'Contrato de Prueba')
            ->assertJsonPath('data.estado.value', 'pendiente');
    }

    public function test_puede_actualizar_estado(): void
    {
        $solicitud = Solicitud::factory()->pendiente()->create();

        $response = $this->patchJson("/api/solicitudes/{$solicitud->id}", [
            'estado' => 'aprobado',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.estado.value', 'aprobado');
    }

    public function test_validacion_nombre_requerido(): void
    {
        $response = $this->postJson('/api/solicitudes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_documento']);
    }

    public function test_solicitud_no_encontrada_retorna_404(): void
    {
        $response = $this->patchJson('/api/solicitudes/999', [
            'estado' => 'aprobado',
        ]);

        $response->assertStatus(404);
    }

    public function test_puede_listar_solicitudes_paginadas(): void
    {
        Solicitud::factory()->count(25)->create();

        $response = $this->getJson('/api/solicitudes?per_page=10');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ])
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25);
    }

    public function test_validacion_per_page_maximo(): void
    {
        $response = $this->getJson('/api/solicitudes?per_page=101');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_validacion_per_page_minimo(): void
    {
        $response = $this->getJson('/api/solicitudes?per_page=0');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_validacion_per_page_no_es_numero(): void
    {
        $response = $this->getJson('/api/solicitudes?per_page=abc');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_validacion_page_minimo(): void
    {
        $response = $this->getJson('/api/solicitudes?page=0');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_puede_eliminar_solicitud(): void
    {
        $solicitud = Solicitud::factory()->create();

        $response = $this->deleteJson("/api/solicitudes/{$solicitud->id}");

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Solicitud eliminada correctamente']);

        $this->assertDatabaseMissing('solicitudes', ['id' => $solicitud->id]);
    }

    public function test_eliminar_solicitud_inexistente_retorna_404(): void
    {
        $response = $this->deleteJson('/api/solicitudes/999');

        $response->assertStatus(404);
    }

    public function test_validacion_estado_invalido(): void
    {
        $solicitud = Solicitud::factory()->create();

        $response = $this->patchJson("/api/solicitudes/{$solicitud->id}", [
            'estado' => 'estado_invalido',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['estado']);
    }

    public function test_validacion_nombre_documento_minimo(): void
    {
        $response = $this->postJson('/api/solicitudes', [
            'nombre_documento' => 'ab',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_documento']);
    }

    public function test_validacion_nombre_documento_maximo(): void
    {
        $response = $this->postJson('/api/solicitudes', [
            'nombre_documento' => str_repeat('a', 256),
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_documento']);
    }

    public function test_paginacion_pagina_vacia_retorna_primera_pagina(): void
    {
        Solicitud::factory()->count(20)->create();

        $response = $this->getJson('/api/solicitudes?per_page=10');

        $response
            ->assertStatus(200)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_paginacion_ultima_pagina(): void
    {
        Solicitud::factory()->count(25)->create();

        $response = $this->getJson('/api/solicitudes?per_page=10&page=3');

        $response
            ->assertStatus(200)
            ->assertJsonPath('meta.current_page', 3)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonCount(5, 'data');
    }
}
