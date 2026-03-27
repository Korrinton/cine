<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Evento;
use App\Models\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventoController extends Controller
{
    /**
     * Muestra la vista de selección de asientos para un evento.
     */
    public function seleccionar($id_evento)
    {
        $evento = Evento::with('sala')->findOrFail($id_evento);

        // Obtenemos el estado de las sillas desde cache
        $cache = Cache::where('id_sala', $evento->id_sala)->first();
        $sillasOcupadas = $cache ? $cache->sillas : [];

        return view('reservas.seleccionar', compact('evento', 'sillasOcupadas'));
    }

    /**
     * Procesa la reserva de los asientos seleccionados.
     */
    public function reservar(Request $request, $id_evento)
    {
        $request->validate([
            'asientos' => 'required|array|min:1|max:8',
            'asientos.*' => 'string',
        ]);

        $evento = Evento::with('sala')->findOrFail($id_evento);
        $asientosSeleccionados = $request->asientos;

        // Verificar que ningún asiento ya esté ocupado
        $cache = Cache::where('id_sala', $evento->id_sala)->first();
        $sillasOcupadas = $cache ? $cache->sillas : [];

        $conflictos = array_intersect($asientosSeleccionados, $sillasOcupadas);
        if (!empty($conflictos)) {
            return back()->with('error', 'Algunos asientos ya fueron reservados: ' . implode(', ', $conflictos));
        }

        // Crear reservas
        foreach ($asientosSeleccionados as $asiento) {
            Reserva::create([
                'id_evento'     => $evento->id_eventos,
                'id_usuario'    => Auth::id(),
                'fecha_reserva' => Carbon::today(),
                'asiento'       => $asiento,
            ]);
        }

        // Actualizar cache de sillas ocupadas
        $nuevasOcupadas = array_merge($sillasOcupadas, $asientosSeleccionados);
        if ($cache) {
            $cache->update(['sillas' => $nuevasOcupadas]);
        } else {
            Cache::create([
                'id_sala' => $evento->id_sala,
                'sillas'  => $nuevasOcupadas,
            ]);
        }

        return redirect()->route('reservas.confirmacion')
                         ->with('success', 'Reserva confirmada: ' . implode(', ', $asientosSeleccionados));
    }

    /**
     * Pantalla de confirmación.
     */
    public function confirmacion()
    {
        return view('reservas.confirmacion');
    }
}