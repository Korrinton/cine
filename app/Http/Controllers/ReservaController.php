<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    public function index($id_evento)
    {
        //Se busca el evento y la película y sala asociadas
        $evento = DB::table('eventos')
        ->join('salas', 'eventos.id_sala', '=', 'salas.id_sala')
        ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
        ->where('id_eventos', $id_evento)
        ->select(
            'eventos.*', 
            'salas.nombre as sala_nombre', 
            'salas.sillas', 
            'salas.filas',
            'salas.aforo', 
            'peliculas.titulo as pelicula_titulo'
        )
        ->first();

        if (!$evento) {
            return redirect('/')->withErrors(['error' => 'Este evento no existe.']);
        }

        //Se recuperan los asientos ocupados para este evento. En fila-asiento
        $ocupados = Reserva::where('id_evento', $id_evento)
            ->pluck('asiento')
            ->toArray();

        return view('reservas.mapa', compact('evento', 'ocupados'));
    }

    public function confirmacion(Request $request) {
        $id_usuario = Auth::id();
        $id_evento = $request->id_evento;
        $asientos = json_decode($request->asientos_json, true);

        if (empty($asientos)) {
            return back()->withErrors(['error' => 'Selecciona al menos un asiento.']);
        }

        //Si el usuario ya tiene una reserva no debe dejar reservar
        $yaTiene = Reserva::where('id_usuario', $id_usuario)
        ->where('id_evento', $id_evento)
        ->exists();

        if ($yaTiene) {
            return redirect()->route('reservas.mapa', $id_evento)
            ->withErrors(['error' => 'Ya tienes una reserva activa para este evento.']);
        }

        //Se busca el evento para mostrar su nombre en la confirmación
        $evento = DB::table('eventos')
            ->join('salas', 'eventos.id_sala', '=', 'salas.id_sala')
            ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
            ->where('id_eventos', $id_evento)
            ->select('eventos.*', 'salas.nombre as sala_nombre', 'peliculas.titulo as pelicula_titulo')
            ->first();

        return view('reservas.confirmacion', compact('evento', 'asientos'));
    }

    public function store(Request $request)
    {
        $id_usuario = Auth::id();
        $id_evento = $request->id_evento;
        
        //Se decodifica el json que envía js del mapa.
        $asientos = json_decode($request->asientos_json, true);

        if (empty($asientos)) {
            return back()->withErrors(['error' => 'Debes seleccionar al menos un asiento.']);
        }

        //Si el usuario ya tiene una reserva no debe dejar reservar
        $yaTiene = Reserva::where('id_usuario', $id_usuario)
            ->where('id_evento', $id_evento)
            ->exists();

        if ($yaTiene) {
            return back()->withErrors(['error' => 'Ya tienes una reserva para este evento.']);
        }

        //Se verifica si un asiento elegido ya ha sido reservado mientras el usuario elegía
        foreach ($asientos as $asiento) {
            $codigoAsiento = $asiento['f'] . '-' . $asiento['s'];
            $existe = Reserva::where('id_evento', $id_evento)
                            ->where('asiento', $codigoAsiento)
                            ->exists();
            
            if ($existe) {
                return redirect()->route('reservas.mapa', $id_evento)
                                ->withErrors(['error' => "El asiento $codigoAsiento ya ha sido ocupado. Por favor, elige otro."]);
            }
        }

        //Se guardan los asientos seleccionados
        foreach ($asientos as $asiento) {
            Reserva::create([
                'id_evento' => $id_evento,
                'id_usuario' => $id_usuario,
                'asiento' => $asiento['f'] . '-' . $asiento['s'], 
                'fecha_reserva' => now(),
            ]);
        }

        //Se redirige al mapa otra vez
        return redirect()->route('reservas.mapa', $id_evento)->with('success', 'Reserva completada.');
    }
}