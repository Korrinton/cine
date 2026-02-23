<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        // 1. Usuarios (No tiene dependencias)
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); 
            $table->string('correo')->unique(); 
            $table->string('password'); 
            $table->string('nombre'); 
            $table->string('apellidos'); 
            $table->enum('tipo', ['admin', 'cliente']); 
            $table->timestamps();
        });

        // 2. Salas (Debe existir antes que eventos)
        Schema::create('salas', function (Blueprint $table) {
            $table->id('id_sala');
            $table->string('nombre');
            $table->integer('aforo');
            $table->integer('filas');
            $table->integer('sillas');
            $table->timestamps();
        });

        // 3. Eventos (Depende de salas)
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

        // 4. Reservas (Depende de eventos y usuarios)
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva'); 
            $table->unsignedBigInteger('id_evento'); 
            $table->unsignedBigInteger('id_usuario'); 
            $table->date('fecha_reserva'); 
            $table->timestamps();

            $table->foreign('id_evento')->references('id_eventos')->on('eventos')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });

        // 5. Cache (Depende de salas)
        Schema::create('cache', function (Blueprint $table) {
            $table->id('id_cache'); 
            $table->unsignedBigInteger('id_sala'); 
            $table->json('sillas'); 
            $table->timestamps();
            
            $table->foreign('id_sala')->references('id_sala')->on('salas')->onDelete('cascade');
        });
        // Tabla de sesiones (obligatoria si SESSION_DRIVER=database)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Tabla para fallos de trabajos (recomendada por Laravel)
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Se eliminan en orden inverso para evitar fallos de claves foráneas
        Schema::dropIfExists('cache');
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('salas');
        Schema::dropIfExists('usuarios');

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('failed_jobs');
    }
};

