<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PagoController extends Controller
{
    public function checkout(Request $request)
    {
        $id_usuario = Auth::id();
        $id_evento  = $request->id_evento;
        $fecha      = $request->fecha_sesion;
        $hora       = $request->hora_sesion;
        $asientos   = json_decode($request->asientos_json, true);

        if (empty($asientos)) {
            return back()->withErrors(['error' => 'Selecciona al menos un asiento.']);
        }

        $eventoData = DB::table('eventos')
            ->join('peliculas', 'eventos.id_pelicula', '=', 'peliculas.id_pelicula')
            ->where('id_eventos', $id_evento)
            ->select('eventos.*', 'peliculas.titulo as pelicula_titulo')
            ->first();

        if (!$eventoData) {
            return redirect('/')->withErrors(['error' => 'Evento no encontrado.']);
        }

        $hoy        = Carbon::today();
        $fechaFinal = Carbon::parse($eventoData->fecha_final ?? $eventoData->fecha_estreno)->endOfDay();

        if (Carbon::parse($fecha)->lt($hoy)) {
            return back()->withErrors(['error' => 'No puedes reservar para una fecha pasada.']);
        }
        if (Carbon::now()->gt($fechaFinal)) {
            return back()->withErrors(['error' => 'Este evento ya ha finalizado.']);
        }

        $yaTiene = Reserva::where('id_usuario', $id_usuario)
            ->where('id_evento', $id_evento)
            ->where('fecha_sesion', $fecha)
            ->where('hora_sesion', $hora)
            ->exists();

        if ($yaTiene) {
            return back()->withErrors(['error' => 'Ya tienes una reserva para este evento en esa fecha y hora.']);
        }

        $precioUnit  = $this->calcularPrecio($eventoData->precio, $fecha, $hora);
        $precioTotal = $precioUnit * count($asientos);

        session(['pago_pendiente' => [
            'id_evento'    => $id_evento,
            'id_usuario'   => $id_usuario,
            'asientos'     => $asientos,
            'fecha_sesion' => $fecha,
            'hora_sesion'  => $hora,
        ]]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'unit_amount'  => (int) round($precioUnit * 100),
                    'product_data' => [
                        'name' => $eventoData->pelicula_titulo . ' — Entrada de cine',
                    ],
                ],
                'quantity' => count($asientos),
            ]],
            'mode'        => 'payment',
            'success_url' => route('pago.exito') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('pago.cancelar', [
                'id_evento' => $id_evento,
                'fecha'     => $fecha,
                'hora'      => $hora,
            ]),
        ]);

        return redirect($stripeSession->url);
    }

    public function exito(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeSession = StripeSession::retrieve($request->session_id);

        if ($stripeSession->payment_status !== 'paid') {
            return redirect('/')->withErrors(['error' => 'El pago no se ha completado.']);
        }

        $datos = session('pago_pendiente');

        if (!$datos) {
            return redirect('/')->with('success', 'Pago recibido. Tu reserva ya estaba registrada.');
        }

        $yaTiene = Reserva::where('id_usuario', $datos['id_usuario'])
            ->where('id_evento', $datos['id_evento'])
            ->where('fecha_sesion', $datos['fecha_sesion'])
            ->where('hora_sesion', $datos['hora_sesion'])
            ->exists();

        if (!$yaTiene) {
            $token = Str::uuid()->toString();
            foreach ($datos['asientos'] as $asiento) {
                Reserva::create([
                    'id_evento'     => $datos['id_evento'],
                    'id_usuario'    => $datos['id_usuario'],
                    'fila'          => $asiento['f'],
                    'asiento'       => $asiento['s'],
                    'fecha_reserva' => now(),
                    'fecha_sesion'  => $datos['fecha_sesion'],
                    'hora_sesion'   => $datos['hora_sesion'],
                    'token'         => $token,
                ]);
            }
        }

        session()->forget('pago_pendiente');

        return redirect('/')->with('success', '¡Pago completado! Puedes ver tu entrada en el historial de compras.');
    }

    public function cancelar(Request $request)
    {
        session()->forget('pago_pendiente');

        $id_evento = $request->id_evento;
        $fecha     = $request->fecha;
        $hora      = $request->hora;

        return redirect()
            ->to(route('reservas.mapa', $id_evento) . '?fecha=' . $fecha . '&hora=' . $hora)
            ->withErrors(['error' => 'Pago cancelado. Puedes intentarlo de nuevo.']);
    }

    private function calcularPrecio(float $precioBase, string $fecha, ?string $hora): float
    {
        $diaSemana   = Carbon::parse($fecha)->dayOfWeek;
        $esMiercoles = $diaSemana === 3;
        $esFinSemana = in_array($diaSemana, [5, 6]);
        $nocturno    = $hora && $hora >= '22:00';
        $matinal     = $hora && $hora < '13:00';

        if ($esMiercoles) return round($precioBase * 0.5, 2);
        if ($nocturno || $matinal) return max(0, round($precioBase - 3, 2));
        if ($esFinSemana) return round($precioBase + 4, 2);
        return $precioBase;
    }
}
