<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id('id_pelicula');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->integer('duracion')->comment('duración en minutos');
            $table->string('genero')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pelicula')->nullable()->after('id_sala');
            $table->foreign('id_pelicula')
                  ->references('id_pelicula')
                  ->on('peliculas')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropForeign(['id_pelicula']);
            $table->dropColumn('id_pelicula');
        });

        Schema::dropIfExists('peliculas');
    }
};
