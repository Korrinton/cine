<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id('id_pelicula');
            $table->string('nombre');
            $table->text('sinopsis');
            $table->integer('duracion');
            $table->enum('genero', ['Acción', 'Comedia', 'Drama', 'Ciencia Ficción']);
            $table->date('fecha_estreno');
            $table->date('fecha_final');
            $table->integer('precio');
            $table->string('poster_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peliculas');
    }
};
