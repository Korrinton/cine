<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('token')->nullable()->after('hora_sesion');
            $table->boolean('validado')->default(false)->after('token');
            $table->timestamp('fecha_validacion')->nullable()->after('validado');
            $table->index('token', 'reservas_token_index');
        });

        $grupos = DB::table('reservas')
            ->select('id_usuario', 'id_evento', 'fecha_sesion', 'hora_sesion')
            ->groupBy('id_usuario', 'id_evento', 'fecha_sesion', 'hora_sesion')
            ->get();

        foreach ($grupos as $grupo) {
            DB::table('reservas')
                ->where('id_usuario',   $grupo->id_usuario)
                ->where('id_evento',    $grupo->id_evento)
                ->where('fecha_sesion', $grupo->fecha_sesion)
                ->where('hora_sesion',  $grupo->hora_sesion)
                ->whereNull('token')
                ->update(['token' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex('reservas_token_index');
            $table->dropColumn(['token', 'validado', 'fecha_validacion']);
        });
    }
};
