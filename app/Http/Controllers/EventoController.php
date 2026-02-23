<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;       
use App\Models\Pelicula;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function create()
    {
        // Pasamos las salas y películas a la vista para rellenar los desplegables (<select>)
        $salas = Sala::all();
        $peliculas = Pelicula::all(); // Asegúrate de tener datos en esta tabla

        return view('eventos.create', compact('salas', 'peliculas'));
    }

    public function store(Request $request)
    {
        // 1. Validar los datos de entrada
        $request->validate([
            'id_pelicula' => 'required|exists:peliculas,id_pelicula',
            'id_sala' => 'required|exists:salas,id_sala',
            'horarios' => 'required|date'
        ]);

        // 2. Obtener la sala para copiar su JSON de asientos
        $sala = Sala::findOrFail($request->id_sala);

        // 3. Crear el evento
        Evento::create([
            'id_pelicula' => $request->id_pelicula,
            'id_sala' => $request->id_sala,
            'horarios' => $request->horarios,
            'asientos_disponibles' => $sala->asientos
        ]);

        // 4. Redirigir con mensaje de éxito
        return redirect()->route('eventos.create')->with('success', 'Evento creado y asientos generados correctamente.');
    } 
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
