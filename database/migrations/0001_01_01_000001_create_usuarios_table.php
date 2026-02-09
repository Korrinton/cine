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
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('usuarios');        
    }
};
