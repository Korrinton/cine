<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Support\Facades\DB;

class ValidarController extends Controller
{
    public function show($token)
    {
        $reservas = DB::table('reservas')
            ->join('eventos', 'reservas.id_evento', '=', 'eventos.id_eventos')
            ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
            ->join('salas', 'eventos.id_sala', '=', 'salas.id_sala')
            ->where('reservas.token', $token)
            ->select(
                'reservas.id_reserva',
                'reservas.fila',
                'reservas.asiento',
                'reservas.validado',
                'reservas.fecha_validacion',
                'reservas.fecha_sesion',
                'reservas.hora_sesion',
                'peliculas.titulo as pelicula_titulo',
                'salas.nombre as sala_nombre'
            )
            ->get();

        if ($reservas->isEmpty()) {
            return view('validar', ['estado' => 'invalido', 'reservas' => collect()]);
        }

        $primera = $reservas->first();

        if ($primera->validado) {
            return view('validar', ['estado' => 'usado', 'reservas' => $reservas]);
        }

        Reserva::where('token', $token)->update([
            'validado'         => true,
            'fecha_validacion' => now(),
        ]);

        return view('validar', ['estado' => 'valido', 'reservas' => $reservas]);
    }
}
