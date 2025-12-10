<?php

declare(strict_types=1);

use App\Modules\Solicitudes\Domain\Enums\EstadoSolicitud;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_documento');
            $table->enum('estado', EstadoSolicitud::values())
                  ->default(EstadoSolicitud::PENDIENTE->value);
            $table->timestamps();
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
