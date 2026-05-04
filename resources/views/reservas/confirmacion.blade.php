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
                    $descuento   = $nocturno || $matinal;
                    $precioUnit  = $descuento ? $evento->precio * 0.5 : $evento->precio;
                    $precioTotal = $precioUnit * count($asientos);
                    $badgeLabel  = $nocturno ? 'Nocturno −50%' : 'Matinal −50%';
                    $badgeIcon   = $nocturno ? 'bi-moon-stars' : 'bi-sunrise';
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
                        @if($descuento)
                            <span class="text-decoration-line-through text-muted me-1">{{ number_format($evento->precio, 2) }} €</span>
                            <span class="text-success fw-bold">{{ number_format($precioUnit, 2) }} €</span>
                            <span class="badge bg-warning text-dark ms-1"><i class="bi {{ $badgeIcon }} me-1"></i>{{ $badgeLabel }}</span>
                        @else
                            {{ number_format($precioUnit, 2) }} €
                        @endif
                    </p>
                    <p class="mb-0"><strong>Total:</strong> <span class="fs-5 fw-bold">{{ number_format($precioTotal, 2) }} €</span></p>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('reservas.mapa', $evento->id_eventos) }}{{ $fecha ? '?fecha='.$fecha : '' }}"
                       class="btn btn-outline-danger px-5 py-2">Cancelar</a>

                    <form action="{{ route('reservas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_evento" value="{{ $evento->id_eventos }}">
                        <input type="hidden" name="asientos_json" value="{{ json_encode($asientos) }}">
                        <input type="hidden" name="fecha_sesion" value="{{ $fecha }}">
                        <input type="hidden" name="hora_sesion"  value="{{ $hora }}">
                        <button type="submit" class="btn btn-success px-5 py-2 shadow">Confirmar</button>
                    </form>
                </div>
        </div>
    </div>
</div>
@endsection