<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Illuminate\Http\Request;

class PeliculaController extends Controller
{
    public function index()
    {
        // Obtenemos solo las películas que están en fecha
        $peliculas = Pelicula::enCartelera()->get();

        // Las mandamos a la vista 'home'
        return view('home', compact('peliculas'));
    }

    public function show($id)
    {
        $pelicula = Pelicula::findOrFail($id);
        // Aquí es donde luego conectarás con los 'eventos' (horarios)
        return view('peliculas.show', compact('pelicula'));
    }
}
