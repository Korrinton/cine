<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Evento;
use App\Models\Cache;
use App\Models\Pelicula;
use App\Models\Sala;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventoController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo evento.
     */
    public function create()
    {
        $peliculas = Pelicula::orderBy('titulo')->get();
        $salas = Sala::orderBy('nombre')->get();
        return view('create', compact('peliculas', 'salas'));
    }

    /**
     * Guarda el nuevo evento (y opcionalmente crea la película).
     */
    public function store(Request $request)
    {
        $request->validate([
            'modo_pelicula'     => 'required|in:existente,nueva',
            'id_pelicula'       => 'required_if:modo_pelicula,existente|nullable|exists:peliculas,id_pelicula',
            'titulo'            => 'required_if:modo_pelicula,nueva|nullable|string|max:255',
            'descripcion'       => 'nullable|string',
            'duracion'          => 'required_if:modo_pelicula,nueva|nullable|integer|min:1',
            'genero'            => 'nullable|string|max:100',
            'imagen'            => 'nullable|image|max:2048',
            'id_sala'           => 'required|exists:salas,id_sala',
            'precio'            => 'required|integer|min:0',
            'fecha_estreno'     => 'required|date',
            'fecha_final'       => 'required|date|after_or_equal:fecha_estreno',
        ]);

        // Verificar que la sala no tenga eventos solapados en ese rango
        $solapado = Evento::where('id_sala', $request->id_sala)
            ->where('fecha_estreno', '<=', $request->fecha_final)
            ->where('fecha_final', '>=', $request->fecha_estreno)
            ->exists();

        if ($solapado) {
            return back()->withInput()->withErrors([
                'id_sala' => 'La sala ya tiene un evento en ese rango de fechas.',
            ]);
        }

        // Crear película nueva si se eligió esa opción
        if ($request->modo_pelicula === 'nueva') {
            $rutaImagen = null;
            if ($request->hasFile('imagen')) {
                $rutaImagen = $request->file('imagen')->store('peliculas', 'public');
            }

            $pelicula = Pelicula::create([
                'titulo'      => $request->titulo,
                'descripcion' => $request->descripcion,
                'duracion'    => $request->duracion,
                'genero'      => $request->genero,
                'imagen'      => $rutaImagen,
            ]);
            $idPelicula = $pelicula->id_pelicula;
        } else {
            $idPelicula = $request->id_pelicula;
        }

        $sala = Sala::findOrFail($request->id_sala);

        Evento::create([
            'nombre'        => Pelicula::find($idPelicula)->titulo,
            'id_pelicula'   => $idPelicula,
            'id_sala'       => $request->id_sala,
            'precio'        => $request->precio,
            'fecha_estreno' => $request->fecha_estreno,
            'fecha_final'   => $request->fecha_final,
        ]);

        return redirect()->route('eventos.create')->with('success', 'Evento creado correctamente.');
    }

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