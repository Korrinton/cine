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
        //Se busca el evento y la sala asociada
        $evento = DB::table('eventos')
            ->join('salas', 'eventos.id_sala', '=', 'salas.id_sala')
            ->where('id_eventos', $id_evento)
            ->first();

        if (!$evento) {
            return redirect('/dashboard')->withErrors(['error' => 'Este evento no existe.']);
        }

        //Se recuperan los asientos ocupados para este evento. En fila-asiento
        $ocupados = Reserva::where('id_evento', $id_evento)
            ->get(['fila', 'asiento'])
            ->map(fn($r) => $r->fila . '-' . $r->asiento)
            ->toArray();

        return view('reservas.mapa', compact('evento', 'ocupados'));
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

        //Se guardan los asientos seleccionados
        foreach ($asientos as $asiento) {
            Reserva::create([
                'id_evento' => $id_evento,
                'id_usuario' => $id_usuario,
                'fila' => $asiento['f'],
                'asiento' => $asiento['s'],
                'fecha_reserva' => now()
            ]);
        }

        //Se redirige al mapa otra vez
        return redirect()->route('reservas.mapa', $id_evento)->with('success', 'Reserva completada.');
    }
}