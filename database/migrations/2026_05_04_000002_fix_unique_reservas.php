<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropUnique('asiento_ocupado_unique');
            $table->unique(['id_evento', 'fila', 'asiento', 'fecha_sesion'], 'asiento_fecha_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropUnique('asiento_fecha_unique');
            $table->unique(['id_evento', 'fila', 'asiento'], 'asiento_ocupado_unique');
        });
    }
};
