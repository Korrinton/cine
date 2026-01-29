<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('salas', function (Blueprint $table) {
            $table->id('id_sala');
            $table->string('nombre');
            $table->integer('aforo');
            $table->integer('filas');
            $table->integer('sillas');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('salas');
    }
};
