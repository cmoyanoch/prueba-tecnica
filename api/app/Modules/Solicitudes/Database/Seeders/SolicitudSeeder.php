<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Database\Seeders;

use App\Modules\Solicitudes\Domain\Entities\Solicitud;
use Illuminate\Database\Seeder;

class SolicitudSeeder extends Seeder
{
    public function run(): void
    {
        Solicitud::factory()->count(4)->pendiente()->create();
        Solicitud::factory()->count(3)->aprobado()->create();
        Solicitud::factory()->count(3)->rechazado()->create();
    }
}
