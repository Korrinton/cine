<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\IngresoExtra;
use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecaudacionController extends Controller
{
    public function index(Request $request)
    {
        $anioActual = now()->year;
        $anio       = (int) $request->get('anio', $anioActual);

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
            ->join('eventos',   'reservas.id_evento',  '=', 'eventos.id_eventos')
            ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
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
            $total = $filas->sum(fn($r) => self::calcularPrecioEntrada(
                $r->precio_base,
                $r->fecha_sesion,
                $r->hora_sesion
            ));

            return [
                'titulo'   => $filas->first()->titulo,
                'imagen'   => $filas->first()->imagen,
                'genero'   => $filas->first()->genero,
                'entradas' => $filas->count(),
                'total'    => $total,
            ];
        })->sortByDesc('total')->values();

        $totalGlobal   = $porPelicula->sum('total');
        $entradasTotal = $porPelicula->sum('entradas');

        $peliculas       = Pelicula::orderBy('titulo')->get();
        $gastosFijos     = Gasto::where('anio', $anio)->where('tipo', 'fijo')->orderBy('nombre')->get();
        $gastosVariables = Gasto::where('anio', $anio)->where('tipo', 'variable')
                                ->with('pelicula')->orderBy('nombre')->get();

        $ingresosExtra     = IngresoExtra::where('anio', $anio)->orderBy('nombre')->get();
        $totalIngresoExtra = $ingresosExtra->sum('importe');
        $totalBruto        = $totalGlobal + $totalIngresoExtra;

        $ivaRate       = 0.21;
        $baseImponible = $totalBruto / (1 + $ivaRate);
        $cuotaIva      = $totalBruto - $baseImponible;

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
            'beneficio', 'baseImponible', 'cuotaIva', 'totalSesiones', 'sesionesPorPelicula',
            'peliculas', 'ingresosExtra', 'totalIngresoExtra', 'totalBruto'
        ));
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

    public function gastoEliminar($id)
    {
        $gasto = Gasto::findOrFail($id);
        $anio  = $gasto->anio;
        $gasto->delete();

        return redirect()->route('admin.recaudacion', ['anio' => $anio])
            ->with('success_gasto', 'Gasto eliminado.');
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

    public function ingresoEliminar($id)
    {
        $ingreso = IngresoExtra::findOrFail($id);
        $anio    = $ingreso->anio;
        $ingreso->delete();

        return redirect()->route('admin.recaudacion', ['anio' => $anio])
            ->with('success_gasto', 'Ingreso eliminado.');
    }

    public static function calcularPrecioEntrada($precioBase, $fechaSesion, $horaSesion): float
    {
        $hora        = $horaSesion ? substr($horaSesion, 0, 5) : null;
        $nocturno    = $hora && $hora >= '22:00';
        $matinal     = $hora && $hora < '13:00';
        $diaSemana   = $fechaSesion ? \Carbon\Carbon::parse($fechaSesion)->dayOfWeek : null;
        $esMiercoles = $diaSemana === 3;
        $esFinSemana = in_array($diaSemana, [5, 6]);

        if ($esMiercoles)          return $precioBase * 0.5;
        if ($nocturno || $matinal) return max(0, $precioBase - 3);
        if ($esFinSemana)          return $precioBase + 4;
        return (float) $precioBase;
    }
}
