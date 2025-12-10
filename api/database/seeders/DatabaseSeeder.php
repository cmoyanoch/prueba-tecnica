<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Solicitudes\Database\Seeders\SolicitudSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SolicitudSeeder::class,
        ]);
    }
}
