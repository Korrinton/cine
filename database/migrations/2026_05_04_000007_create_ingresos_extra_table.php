<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }
};
