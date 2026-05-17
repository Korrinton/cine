<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function index($id_evento)
    {
        $evento = DB::table('eventos')
            ->join('salas', 'eventos.id_sala', '=', 'salas.id_sala')
            ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
            ->where('id_eventos', $id_evento)
            ->select('eventos.*', 'salas.nombre as sala_nombre', 'salas.sillas', 'salas.filas', 'salas.aforo', 'peliculas.titulo as pelicula_titulo')
            ->first();

        if (!$evento) {
            return redirect('/')->withErrors(['error' => 'Este evento no existe.']);
        }

        $fecha = request()->query('fecha', $evento->fecha_estreno);
        $hora  = request()->query('hora');

        $hoy       = Carbon::today();
        $fechaFinal = Carbon::parse($evento->fecha_final ?? $evento->fecha_estreno)->endOfDay();

        if (Carbon::parse($fecha)->lt($hoy)) {
            return redirect('/')->withErrors(['error' => 'No puedes reservar para una fecha pasada.']);
        }
        if (Carbon::now()->gt($fechaFinal)) {
            return redirect('/')->withErrors(['error' => 'Este evento ya ha finalizado.']);
        }
        if (Carbon::parse($fecha)->lt(Carbon::parse($evento->fecha_estreno)) ||
            Carbon::parse($fecha)->gt(Carbon::parse($evento->fecha_final ?? $evento->fecha_estreno))) {
            return redirect('/')->withErrors(['error' => 'La fecha seleccionada está fuera del rango del evento.']);
        }

        $ocupados = Reserva::where('id_evento', $id_evento)
            ->where('fecha_sesion', $fecha)
            ->where('hora_sesion', $hora)
            ->get(['fila', 'asiento'])
            ->map(fn($r) => $r->fila . '-' . $r->asiento)
            ->toArray();

        return view('reservas.mapa', compact('evento', 'ocupados', 'fecha', 'hora'));
    }

    public function confirmacion(Request $request)
    {
        $id_usuario = Auth::id();
        $id_evento  = $request->id_evento;
        $fecha      = $request->fecha_sesion;
        $hora       = $request->hora_sesion;
        $asientos   = json_decode($request->asientos_json, true);

        if (empty($asientos)) {
            return back()->withErrors(['error' => 'Selecciona al menos un asiento.']);
        }

        $eventoData = DB::table('eventos')->where('id_eventos', $id_evento)->first();
        if ($eventoData) {
            $hoy        = Carbon::today();
            $fechaFinal = Carbon::parse($eventoData->fecha_final ?? $eventoData->fecha_estreno)->endOfDay();
            if (Carbon::parse($fecha)->lt($hoy)) {
                return back()->withErrors(['error' => 'No puedes reservar para una fecha pasada.']);
            }
            if (Carbon::now()->gt($fechaFinal)) {
                return back()->withErrors(['error' => 'Este evento ya ha finalizado.']);
            }
        }

        $yaTiene = Reserva::where('id_usuario', $id_usuario)
            ->where('id_evento', $id_evento)
            ->where('fecha_sesion', $fecha)
            ->where('hora_sesion', $hora)
            ->exists();

        if ($yaTiene) {
            return redirect()
                ->to(route('reservas.mapa', $id_evento) . '?fecha=' . $fecha . '&hora=' . $hora)
                ->withErrors(['error' => 'Ya tienes una reserva para este evento en esa fecha y hora.']);
        }

        $evento = DB::table('eventos')
            ->join('salas', 'eventos.id_sala', '=', 'salas.id_sala')
            ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
            ->where('id_eventos', $id_evento)
            ->select('eventos.*', 'salas.nombre as sala_nombre', 'peliculas.titulo as pelicula_titulo')
            ->first();

        return view('reservas.confirmacion', compact('evento', 'asientos', 'fecha', 'hora'));
    }

    public function store(Request $request)
    {
        $id_usuario = Auth::id();
        $id_evento  = $request->id_evento;
        $fecha      = $request->fecha_sesion;
        $hora       = $request->hora_sesion;
        $asientos   = json_decode($request->asientos_json, true);

        if (empty($asientos)) {
            return back()->withErrors(['error' => 'Debes seleccionar al menos un asiento.']);
        }

        $eventoData = DB::table('eventos')->where('id_eventos', $id_evento)->first();
        if ($eventoData) {
            $hoy        = Carbon::today();
            $fechaFinal = Carbon::parse($eventoData->fecha_final ?? $eventoData->fecha_estreno)->endOfDay();
            if (Carbon::parse($fecha)->lt($hoy)) {
                return back()->withErrors(['error' => 'No puedes reservar para una fecha pasada.']);
            }
            if (Carbon::now()->gt($fechaFinal)) {
                return back()->withErrors(['error' => 'Este evento ya ha finalizado.']);
            }
        }

        $yaTiene = Reserva::where('id_usuario', $id_usuario)
            ->where('id_evento', $id_evento)
            ->where('fecha_sesion', $fecha)
            ->where('hora_sesion', $hora)
            ->exists();

        if ($yaTiene) {
            return back()->withErrors(['error' => 'Ya tienes una reserva para este evento en esa fecha y hora.']);
        }

        foreach ($asientos as $asiento) {
            $existe = Reserva::where('id_evento', $id_evento)
                ->where('fecha_sesion', $fecha)
                ->where('hora_sesion', $hora)
                ->where('fila', $asiento['f'])
                ->where('asiento', $asiento['s'])
                ->exists();

            if ($existe) {
                return redirect()
                    ->to(route('reservas.mapa', $id_evento) . '?fecha=' . $fecha . '&hora=' . $hora)
                    ->withErrors(['error' => "El asiento fila {$asiento['f']}, asiento {$asiento['s']} ya está ocupado. Elige otro."]);
            }
        }

        foreach ($asientos as $asiento) {
            Reserva::create([
                'id_evento'     => $id_evento,
                'id_usuario'    => $id_usuario,
                'fila'          => $asiento['f'],
                'asiento'       => $asiento['s'],
                'fecha_reserva' => now(),
                'fecha_sesion'  => $fecha,
                'hora_sesion'   => $hora,
            ]);
        }

        return redirect('/')->with('success', '¡Reserva completada! Puedes ver tu entrada en el historial de compras.');
    }
}
