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
        Schema::table('eventos', function (Blueprint $table) {
        // Añadimos la columna que conectará con tu tabla
        $table->unsignedBigInteger('id_pelicula')->after('id_eventos')->nullable();
        
        // Creamos la relación oficial
        $table->foreign('id_pelicula')->references('id_pelicula')->on('peliculas')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            //
        });
    }
};
