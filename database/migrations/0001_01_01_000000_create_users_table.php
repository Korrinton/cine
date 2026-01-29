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
    //c\reate users table
        Schema::createIfNotExists('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); 
            $table->string('correo')->unique(); 
            $table->string('password'); 
            $table->string('nombre'); 
            $table->string('apellidos'); 
            $table->enum('tipo', ['admin', 'cliente']); 
            $table->timestamps();
        });
    //create reservations table
        Schema::createIfNotExists('reservas', function (Blueprint $table) {
            $table->id('id_reserva'); 
            $table->unsignedBigInteger('id_evento'); 
            $table->unsignedBigInteger('id_usuario'); 
            $table->date('fecha_reserva'); 
            $table->timestamps();

            $table->foreign('id_evento')->references('id_eventos')->on('eventos')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
        //create instalation and events tables
        Schema::createIfNotExists('salas', function (Blueprint $table) {
            $table->id('id_sala');
            $table->string('nombre');
            $table->integer('aforo');
            $table->integer('filas');
            $table->integer('sillas');
            $table->timestamps();
        });
        //create events table
        Schema::createIfNotExists('eventos', function (Blueprint $table) {
            $table->id('id_eventos'); 
            $table->string('nombre'); 
            $table->unsignedBigInteger('id_sala'); 
            $table->integer('precio'); 
            $table->date('fecha_estreno'); 
            $table->date('fecha_final'); 
            $table->timestamps();

            $table->foreign('id_sala')->references('id_sala')->on('salas')->onDelete('cascade');
        });
        //create cache table
        Schema::createIfNotExists('cache', function (Blueprint $table) {
            $table->id('id_cache'); 
            $table->unsignedBigInteger('id_sala'); 
            $table->json('sillas'); 
            $table->timestamps();
            $table->foreign('id_sala')->references('id_sala')->on('salas')->onDelete('cascade');

        });
        
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('salas');
        Schema::dropIfExists('cache');
        
    }
};
