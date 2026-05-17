<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Sala;
use App\Models\Pelicula;
use App\Models\Reserva;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalEventos  = Evento::count();
        $totalSalas    = Sala::count();
        $totalPeliculas = Pelicula::count();
        $totalReservas = Reserva::count();

        return view('admin.dashboard', compact(
            'totalEventos', 'totalSalas', 'totalPeliculas', 'totalReservas'
        ));
    }

    // ── SALAS ────────────────────────────────────────────────────────────────

    public function salas()
    {
        $salas = Sala::orderBy('nombre')->get();
        return view('admin.salas', compact('salas'));
    }

    public function salaCrear()
    {
        return view('admin.sala-crear');
    }

    public function salaGuardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'filas'  => 'required|integer|min:1',
            'sillas' => 'required|integer|min:1',
        ]);

        Sala::create([
            'nombre' => $request->nombre,
            'filas'  => $request->filas,
            'sillas' => $request->sillas,
            'aforo'  => $request->filas * $request->sillas,
        ]);

        return redirect()->route('admin.salas')->with('success', 'Sala creada correctamente.');
    }

    public function salaEliminar($id)
    {
        Sala::findOrFail($id)->delete();
        return redirect()->route('admin.salas')->with('success', 'Sala eliminada.');
    }

    // ── EVENTOS ──────────────────────────────────────────────────────────────

    public function eventos()
    {
        $eventos = Evento::with(['pelicula', 'sala', 'sesiones'])->orderBy('fecha_estreno')->get();
        return view('admin.eventos', compact('eventos'));
    }

    public function eventoCrear()
    {
        $peliculas = Pelicula::orderBy('titulo')->get();
        $salas     = Sala::orderBy('nombre')->get();
        return view('admin.evento-crear', compact('peliculas', 'salas'));
    }

    public function eventoGuardar(Request $request)
    {
        $request->validate([
            'modo_pelicula' => 'required|in:existente,nueva',
            'id_pelicula'   => 'required_if:modo_pelicula,existente|nullable|exists:peliculas,id_pelicula',
            'titulo'        => 'required_if:modo_pelicula,nueva|nullable|string|max:255',
            'descripcion'   => 'nullable|string',
            'duracion'      => 'required_if:modo_pelicula,nueva|nullable|integer|min:1',
            'genero'        => 'nullable|string|max:100',
            'imagen'        => 'nullable|image|max:2048',
            'id_sala'       => 'required|exists:salas,id_sala',
            'precio'        => 'required|numeric|min:0',
            'fecha_estreno' => 'required|date|after_or_equal:today',
            'fecha_final'   => 'required|date|after_or_equal:fecha_estreno',
            'horarios'      => 'required|array|min:1',
            'horarios.*'    => 'required|date_format:H:i',
        ]);

        $solapado = Evento::where('id_sala', $request->id_sala)
            ->where('fecha_estreno', '<=', $request->fecha_final)
            ->where('fecha_final',   '>=', $request->fecha_estreno)
            ->exists();

        if ($solapado) {
            return back()->withInput()->withErrors([
                'id_sala' => 'La sala ya tiene un evento en ese rango de fechas.',
            ]);
        }

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

        $evento = Evento::create([
            'nombre'        => Pelicula::find($idPelicula)->titulo,
            'id_pelicula'   => $idPelicula,
            'id_sala'       => $request->id_sala,
            'precio'        => $request->precio,
            'fecha_estreno' => $request->fecha_estreno,
            'fecha_final'   => $request->fecha_final,
        ]);

        foreach ($request->horarios as $hora) {
            Sesion::create(['id_evento' => $evento->id_eventos, 'hora_inicio' => $hora]);
        }

        return redirect()->route('admin.eventos')->with('success', 'Evento creado correctamente.');
    }

    public function eventoEditar($id)
    {
        $evento          = Evento::with(['pelicula', 'sesiones'])->findOrFail($id);
        $salas           = Sala::orderBy('nombre')->get();
        $horariosActuales = $evento->sesiones->map(fn($s) => substr($s->hora_inicio, 0, 5))->toArray();
        return view('admin.evento-editar', compact('evento', 'salas', 'horariosActuales'));
    }

    public function eventoActualizar(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        $request->validate([
            'id_sala'       => 'required|exists:salas,id_sala',
            'precio'        => 'required|numeric|min:0',
            'fecha_estreno' => 'required|date|after_or_equal:today',
            'fecha_final'   => 'required|date|after_or_equal:fecha_estreno',
            'horarios'      => 'required|array|min:1',
            'horarios.*'    => 'required|date_format:H:i',
        ]);

        $solapado = Evento::where('id_sala', $request->id_sala)
            ->where('id_eventos', '!=', $id)
            ->where('fecha_estreno', '<=', $request->fecha_final)
            ->where('fecha_final',   '>=', $request->fecha_estreno)
            ->exists();

        if ($solapado) {
            return back()->withInput()->withErrors([
                'id_sala' => 'La sala ya tiene otro evento en ese rango de fechas.',
            ]);
        }

        $evento->update([
            'id_sala'       => $request->id_sala,
            'precio'        => $request->precio,
            'fecha_estreno' => $request->fecha_estreno,
            'fecha_final'   => $request->fecha_final,
        ]);

        Sesion::where('id_evento', $id)->delete();
        foreach ($request->horarios as $hora) {
            Sesion::create(['id_evento' => $id, 'hora_inicio' => $hora]);
        }

        return redirect()->route('admin.eventos')->with('success', 'Evento actualizado correctamente.');
    }

    public function eventoEliminar($id)
    {
        Evento::findOrFail($id)->delete();
        return redirect()->route('admin.eventos')->with('success', 'Evento eliminado.');
    }

    // ── PELÍCULAS ─────────────────────────────────────────────────────────────

    public function peliculas()
    {
        $peliculas = Pelicula::orderBy('titulo')->get();
        return view('peliculas.index', compact('peliculas'));
    }

    public function peliculaGuardar(Request $request)
    {
        $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion'    => 'required|integer|min:1',
            'genero'      => 'nullable|string|max:100',
            'imagen'      => 'nullable|image|max:2048',
        ]);

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('peliculas', 'public');
        }

        Pelicula::create([
            'titulo'      => $request->titulo,
            'descripcion' => $request->descripcion,
            'duracion'    => $request->duracion,
            'genero'      => $request->genero,
            'imagen'      => $rutaImagen,
        ]);

        return redirect()->route('admin.peliculas')->with('success', 'Película creada correctamente.');
    }

    public function peliculaEliminar($id)
    {
        $pelicula = Pelicula::findOrFail($id);
        if ($pelicula->imagen) {
            Storage::disk('public')->delete($pelicula->imagen);
        }
        $pelicula->delete();
        return redirect()->route('admin.peliculas')->with('success', 'Película eliminada correctamente.');
    }
}
