<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['fijo', 'variable']);
            $table->unsignedBigInteger('id_pelicula')->nullable();
            $table->decimal('importe', 10, 2);
            $table->unsignedSmallInteger('anio');
            $table->timestamps();

            $table->foreign('id_pelicula')->references('id_pelicula')->on('peliculas')->onDelete('set null');
        });

        Schema::create('ingresos_extra', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('importe', 10, 2);
            $table->unsignedSmallInteger('anio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos_extra');
        Schema::dropIfExists('gastos');
    }
};
