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
    //crear tabla de usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');          
            $table->string('correo')->unique();
            $table->string('password');        
            $table->string('nombre')->nullable();          
            $table->string('apellidos')->nullable();       
            $table->enum('tipo', ['admin', 'cliente']); 
            $table->rememberToken();//para el recuérdame del login
            $table->timestamps();// Campos de fecha automáticos
        });        

    //create instalation and events tables
        Schema::create('salas', function (Blueprint $table) {
            $table->id('id_sala');
            $table->string('nombre');
            $table->integer('aforo');
            $table->integer('filas');
            $table->integer('sillas');
            $table->timestamps();
        });
        //create events table
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
    //create reservations table
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva'); 
            $table->unsignedBigInteger('id_evento'); 
            $table->unsignedBigInteger('id_usuario'); 
            $table->date('fecha_reserva'); 
            $table->timestamps();

            $table->foreign('id_evento')->references('id_eventos')->on('eventos')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
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
    }
};
