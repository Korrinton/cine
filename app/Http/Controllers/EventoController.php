<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Pelicula;
use App\Models\Sala;

class EventoController extends Controller
{
    public function create()
    {
        $peliculas = Pelicula::orderBy('titulo')->get();
        $salas     = Sala::orderBy('nombre')->get();
        return view('eventos.create', compact('peliculas', 'salas'));
    }

    public function store(Request $request)
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
            'fecha_estreno' => 'required|date',
            'fecha_final'   => 'required|date|after_or_equal:fecha_estreno',
        ]);

        // Comprobar solapamiento de sala
        $solapado = Evento::where('id_sala', $request->id_sala)
            ->where('fecha_estreno', '<=', $request->fecha_final)
            ->where('fecha_final',   '>=', $request->fecha_estreno)
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
}
