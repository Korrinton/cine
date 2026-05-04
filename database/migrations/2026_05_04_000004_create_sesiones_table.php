<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id('id_sesion');
            $table->unsignedBigInteger('id_evento');
            $table->time('hora_inicio');
            $table->timestamps();

            $table->foreign('id_evento')
                  ->references('id_eventos')
                  ->on('eventos')
                  ->onDelete('cascade');
        });

        Schema::table('reservas', function (Blueprint $table) {
            $table->time('hora_sesion')->nullable()->after('fecha_sesion');
        });

        // Ampliar el unique para incluir la hora (en un solo ALTER para evitar error de FK)
        DB::statement('ALTER TABLE reservas ADD UNIQUE KEY asiento_sesion_unique (id_evento, fila, asiento, fecha_sesion, hora_sesion), DROP INDEX asiento_fecha_unique');

        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('hora_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->time('hora_inicio')->nullable();
        });

        DB::statement('ALTER TABLE reservas DROP INDEX asiento_sesion_unique');
        DB::statement('ALTER TABLE reservas ADD UNIQUE KEY asiento_fecha_unique (id_evento, fila, asiento, fecha_sesion)');

        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn('hora_sesion');
        });

        Schema::dropIfExists('sesiones');
    }
};
