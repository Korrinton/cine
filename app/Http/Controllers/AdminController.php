<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Sala;
use App\Models\Pelicula;
use App\Models\Reserva;
use App\Models\Sesion;
use App\Models\Gasto;
use App\Models\IngresoExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'fecha_estreno' => 'required|date',
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

    public function recaudacion(Request $request)
    {
        $anioActual = now()->year;
        $anio       = (int) $request->get('anio', $anioActual);

        // Años disponibles (desde el primer registro)
        $aniosDisponibles = DB::table('reservas')
            ->selectRaw('YEAR(fecha_sesion) as anio')
            ->whereNotNull('fecha_sesion')
            ->groupBy('anio')
            ->orderByDesc('anio')
            ->pluck('anio');

        if ($aniosDisponibles->isEmpty()) {
            $aniosDisponibles = collect([$anioActual]);
        }

        $reservas = DB::table('reservas')
            ->join('eventos',   'reservas.id_evento',    '=', 'eventos.id_eventos')
            ->join('peliculas', 'eventos.id_pelicula',   '=', 'peliculas.id_pelicula')
            ->whereYear('reservas.fecha_sesion', $anio)
            ->select(
                'peliculas.id_pelicula',
                'peliculas.titulo',
                'peliculas.imagen',
                'peliculas.genero',
                'eventos.precio as precio_base',
                'reservas.id_reserva',
                'reservas.fecha_sesion',
                'reservas.hora_sesion'
            )
            ->get();

        $porPelicula = $reservas->groupBy('id_pelicula')->map(function ($filas) {
            $total = $filas->sum(function ($r) {
                return self::calcularPrecioEntrada(
                    $r->precio_base,
                    $r->fecha_sesion,
                    $r->hora_sesion
                );
            });

            return [
                'titulo'   => $filas->first()->titulo,
                'imagen'   => $filas->first()->imagen,
                'genero'   => $filas->first()->genero,
                'entradas' => $filas->count(),
                'total'    => $total,
            ];
        })->sortByDesc('total')->values();

        $totalGlobal  = $porPelicula->sum('total');
        $entradasTotal = $porPelicula->sum('entradas');

        $peliculas       = Pelicula::orderBy('titulo')->get();
        $gastosFijos     = Gasto::where('anio', $anio)->where('tipo', 'fijo')->orderBy('nombre')->get();
        $gastosVariables = Gasto::where('anio', $anio)->where('tipo', 'variable')
                                ->with('pelicula')->orderBy('nombre')->get();

        $ingresosExtra    = IngresoExtra::where('anio', $anio)->orderBy('nombre')->get();
        $totalIngresoExtra = $ingresosExtra->sum('importe');
        $totalBruto        = $totalGlobal + $totalIngresoExtra;

        $ivaRate       = 0.21;
        $baseImponible = $totalBruto / (1 + $ivaRate);
        $cuotaIva      = $totalBruto - $baseImponible;

        // Sesiones distintas por película en el año
        $sesionesPorPelicula = DB::table('reservas')
            ->join('eventos', 'reservas.id_evento', '=', 'eventos.id_eventos')
            ->whereYear('reservas.fecha_sesion', $anio)
            ->whereNotNull('reservas.fecha_sesion')
            ->select('eventos.id_pelicula', 'reservas.id_evento', 'reservas.fecha_sesion', 'reservas.hora_sesion')
            ->distinct()
            ->get()
            ->groupBy('id_pelicula')
            ->map->count();

        $totalSesiones  = $sesionesPorPelicula->sum();
        $totalFijos     = $gastosFijos->sum('importe');
        $totalVariables = $gastosVariables->sum(
            fn($g) => $g->importe * ($sesionesPorPelicula->get($g->id_pelicula, 0))
        );
        $totalGastos = $totalFijos + $totalVariables;
        $beneficio   = $baseImponible - $totalGastos;

        return view('admin.recaudacion', compact(
            'porPelicula', 'totalGlobal', 'entradasTotal', 'anio', 'aniosDisponibles',
            'gastosFijos', 'gastosVariables', 'totalFijos', 'totalVariables', 'totalGastos',
            'beneficio', 'baseImponible', 'cuotaIva', 'totalSesiones', 'sesionesPorPelicula', 'peliculas',
            'ingresosExtra', 'totalIngresoExtra', 'totalBruto'
        ));
    }

    public function ingresoGuardar(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string|max:150',
            'importe' => 'required|numeric|min:0',
            'anio'    => 'required|integer',
        ]);

        IngresoExtra::create($request->only('nombre', 'importe', 'anio'));

        return redirect()->route('admin.recaudacion', ['anio' => $request->anio])
            ->with('success_gasto', 'Ingreso añadido correctamente.');
    }

    public function ingresoEliminar(Request $request, $id)
    {
        $ingreso = IngresoExtra::findOrFail($id);
        $anio    = $ingreso->anio;
        $ingreso->delete();

        return redirect()->route('admin.recaudacion', ['anio' => $anio])
            ->with('success_gasto', 'Ingreso eliminado.');
    }

    public function gastoGuardar(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:150',
            'tipo'        => 'required|in:fijo,variable',
            'importe'     => 'required|numeric|min:0',
            'anio'        => 'required|integer',
            'id_pelicula' => 'nullable|exists:peliculas,id_pelicula',
        ]);

        Gasto::create($request->only('nombre', 'tipo', 'importe', 'anio', 'id_pelicula'));

        return redirect()->route('admin.recaudacion', ['anio' => $request->anio])
            ->with('success_gasto', 'Gasto añadido correctamente.');
    }

    public function gastoEliminar(Request $request, $id)
    {
        $gasto = Gasto::findOrFail($id);
        $anio  = $gasto->anio;
        $gasto->delete();

        return redirect()->route('admin.recaudacion', ['anio' => $anio])
            ->with('success_gasto', 'Gasto eliminado.');
    }

    private static function calcularPrecioEntrada($precioBase, $fechaSesion, $horaSesion): float
    {
        $hora        = $horaSesion ? substr($horaSesion, 0, 5) : null;
        $nocturno    = $hora && $hora >= '22:00';
        $matinal     = $hora && $hora < '13:00';
        $diaSemana   = $fechaSesion ? \Carbon\Carbon::parse($fechaSesion)->dayOfWeek : null;
        $esMiercoles = $diaSemana === 3;
        $esFinSemana = in_array($diaSemana, [5, 6]);

        if ($esMiercoles)            return $precioBase * 0.5;
        if ($nocturno || $matinal)   return max(0, $precioBase - 3);
        if ($esFinSemana)            return $precioBase + 4;
        return (float) $precioBase;
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
