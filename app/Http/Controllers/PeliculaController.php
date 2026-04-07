<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelicula;

class PeliculaController extends Controller
{
    public function index()
    {
        $peliculas = Pelicula::orderBy('titulo')->get();
        return view('peliculas.index', compact('peliculas'));
    }

    public function create()
    {
        return view('peliculas.create');
    }

    public function store(Request $request)
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

        return redirect()->route('peliculas.create')->with('success', 'Película creada correctamente.');
    }

    public function destroy($id)
    {
        $pelicula = Pelicula::findOrFail($id);
        $pelicula->delete();

        return redirect()->route('peliculas.index')->with('success', 'Película eliminada correctamente.');
    }
}
