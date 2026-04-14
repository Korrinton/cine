<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;

class SalaController extends Controller
{
    public function index()
    {
        $salas = Sala::orderBy('nombre')->get();
        return view('salas.index', compact('salas'));
    }

    public function create()
    {
        return view('salas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'filas'  => 'required|integer|min:1',
            'sillas' => 'required|integer|min:1',
        ]);

        $aforo = $request->filas * $request->sillas;

        Sala::create([
            'nombre' => $request->nombre,
            'filas'  => $request->filas,
            'sillas' => $request->sillas,
            'aforo'  => $aforo,
        ]);

        return redirect()->route('salas.index')->with('success', 'Sala creada correctamente.');
    }

    public function destroy($id)
    {
        Sala::findOrFail($id)->delete();
        return redirect()->route('salas.index')->with('success', 'Sala eliminada correctamente.');
    }
}
