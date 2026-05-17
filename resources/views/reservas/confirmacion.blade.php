@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0">¿Confirmar Reserva?</h4>
            </div>
            <div class="card-body p-5 text-center">

                @php
                    $nocturno    = $hora && $hora >= '22:00';
                    $matinal     = $hora && $hora < '13:00';
                    $diaSemana   = $fecha ? \Carbon\Carbon::parse($fecha)->dayOfWeek : null;
                    $esMiercoles = $diaSemana === 3;
                    $esFinSemana = in_array($diaSemana, [5, 6]);

                    if ($esMiercoles) {
                        $precioUnit = $evento->precio * 0.5;
                        $badgeLabel = 'Día del espectador −50%';
                        $badgeIcon  = 'bi-star';
                        $badgeClass = 'bg-success';
                        $esSuma     = false;
                    } elseif ($nocturno || $matinal) {
                        $precioUnit = max(0, $evento->precio - 3);
                        $badgeLabel = $nocturno ? 'Nocturno −3€' : 'Matinal −3€';
                        $badgeIcon  = $nocturno ? 'bi-moon-stars' : 'bi-sunrise';
                        $badgeClass = 'bg-warning text-dark';
                        $esSuma     = false;
                    } elseif ($esFinSemana) {
                        $precioUnit = $evento->precio + 4;
                        $badgeLabel = 'Viernes/Sábado +4€';
                        $badgeIcon  = 'bi-fire';
                        $badgeClass = 'bg-danger';
                        $esSuma     = true;
                    } else {
                        $precioUnit = $evento->precio;
                        $badgeLabel = null;
                        $badgeIcon  = null;
                        $badgeClass = null;
                        $esSuma     = false;
                    }

                    $precioTotal = $precioUnit * count($asientos);
                @endphp

                <div class="border-0 text-start mb-4">
                    <p class="mb-1"><strong>Película:</strong> {{ $evento->pelicula_titulo }}</p>
                    <p class="mb-1"><strong>Sala:</strong> {{ $evento->sala_nombre }}</p>
                    @if($fecha)
                        <p class="mb-1">
                            <strong>Fecha:</strong>
                            {{ \Carbon\Carbon::parse($fecha)->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                            @if($hora) · {{ $hora }} h @endif
                        </p>
                    @endif
                    <p class="mb-1 mt-3"><strong>Asientos:</strong><br>
                        @foreach($asientos as $asiento)
                            <span>Fila {{ $asiento['f'] }}, Silla {{ $asiento['s'] }}</span><br>
                        @endforeach
                    </p>
                    <p class="mb-1 mt-3">
                        <strong>Precio por entrada:</strong>
                        @if($badgeLabel)
                            @if(!$esSuma)
                                <span class="text-decoration-line-through text-muted me-1">{{ number_format($evento->precio, 2) }} €</span>
                                <span class="text-success fw-bold">{{ number_format($precioUnit, 2) }} €</span>
                            @else
                                <span class="text-danger fw-bold">{{ number_format($precioUnit, 2) }} €</span>
                            @endif
                            <span class="badge {{ $badgeClass }} ms-1"><i class="bi {{ $badgeIcon }} me-1"></i>{{ $badgeLabel }}</span>
                        @else
                            {{ number_format($precioUnit, 2) }} €
                        @endif
                    </p>
                    <p class="mb-0"><strong>Total:</strong> <span class="fs-5 fw-bold">{{ number_format($precioTotal, 2) }} €</span></p>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('reservas.mapa', $evento->id_eventos) }}{{ $fecha ? '?fecha='.$fecha.'&hora='.$hora : '' }}"
                       class="btn btn-outline-danger px-5 py-2">Cancelar</a>

                    <form action="{{ route('pago.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_evento" value="{{ $evento->id_eventos }}">
                        <input type="hidden" name="asientos_json" value="{{ json_encode($asientos) }}">
                        <input type="hidden" name="fecha_sesion" value="{{ $fecha }}">
                        <input type="hidden" name="hora_sesion"  value="{{ $hora }}">
                        <button type="submit" class="btn btn-success px-5 py-2 shadow">
                            <i class="bi bi-credit-card me-2"></i>Pagar con Stripe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
