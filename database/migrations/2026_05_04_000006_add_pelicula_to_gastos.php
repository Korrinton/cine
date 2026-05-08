<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pelicula')->nullable()->after('tipo');
            $table->foreign('id_pelicula')->references('id_pelicula')->on('peliculas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropForeign(['id_pelicula']);
            $table->dropColumn('id_pelicula');
        });
    }
};
