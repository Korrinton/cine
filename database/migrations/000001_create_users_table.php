<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        // 1. USUARIOS
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre');
            $table->string('apellidos')->nullable();
            $table->string('correo')->unique();
            $table->string('password');
            $table->string('tipo')->default('cliente');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. SALAS
        Schema::create('salas', function (Blueprint $table) {
            $table->id('id_sala');
            $table->string('nombre');
            $table->integer('aforo');
            $table->integer('filas');
            $table->integer('sillas');
            $table->timestamps();
        });

        // 3. EVENTOS
        Schema::create('eventos', function (Blueprint $table) {
            $table->id('id_eventos');
            $table->string('nombre');
            $table->unsignedBigInteger('id_sala');
            $table->integer('precio');
            $table->date('fecha_estreno');
            $table->date('fecha_final');
            $table->timestamps();

            $table->foreign('id_sala')->references('id_sala')->on('salas')->onDelete('cascade');
        });

        // 4. RESERVAS
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva');
            $table->unsignedBigInteger('id_evento');
            $table->unsignedBigInteger('id_usuario');
            $table->integer('fila');
            $table->integer('asiento');
            $table->timestamp('fecha_reserva');
            $table->date('fecha_sesion')->nullable();
            $table->time('hora_sesion')->nullable();
            $table->timestamps();

            $table->foreign('id_evento')->references('id_eventos')->on('eventos')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            $table->unique(['id_evento', 'fila', 'asiento', 'fecha_sesion', 'hora_sesion'], 'asiento_sesion_unique');
        });

        // 5. CACHE
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // 6. SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('salas');
        Schema::dropIfExists('usuarios');
    }
};
