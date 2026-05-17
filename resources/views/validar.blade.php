@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width: 480px;">
    <div class="text-center mb-4">
        <h4 class="fw-bold">Validación de Entrada</h4>
    </div>

    @if($estado === 'valido')
        <div class="card border-0 shadow text-center p-4">
            <div class="mb-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold text-success mb-1">ENTRADA VÁLIDA</h3>
            <p class="text-muted mb-4">Acceso permitido</p>

            @php $primera = $reservas->first(); @endphp
            <div class="text-start bg-light rounded p-3 mb-3">
                <p class="mb-1"><strong>Película:</strong> {{ $primera->pelicula_titulo }}</p>
                <p class="mb-1"><strong>Sala:</strong> {{ $primera->sala_nombre }}</p>
                <p class="mb-1"><strong>Fecha:</strong>
                    {{ \Carbon\Carbon::parse($primera->fecha_sesion)->format('d/m/Y') }}
                    @if($primera->hora_sesion) · {{ \Carbon\Carbon::parse($primera->hora_sesion)->format('H:i') }} @endif
                </p>
                <p class="mb-0"><strong>Asientos:</strong>
                    {{ $reservas->map(fn($r) => 'F'.$r->fila.'-A'.$r->asiento)->join(', ') }}
                </p>
            </div>
            <p class="text-muted small">Validada el {{ now()->format('d/m/Y H:i') }}</p>
        </div>

    @elseif($estado === 'usado')
        @php $primera = $reservas->first(); @endphp
        <div class="card border-0 shadow text-center p-4">
            <div class="mb-3">
                <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold text-danger mb-1">ENTRADA YA USADA</h3>
            <p class="text-muted mb-4">Esta entrada ya fue validada anteriormente</p>

            <div class="text-start bg-light rounded p-3 mb-3">
                <p class="mb-1"><strong>Película:</strong> {{ $primera->pelicula_titulo }}</p>
                <p class="mb-1"><strong>Sala:</strong> {{ $primera->sala_nombre }}</p>
                <p class="mb-1"><strong>Fecha:</strong>
                    {{ \Carbon\Carbon::parse($primera->fecha_sesion)->format('d/m/Y') }}
                    @if($primera->hora_sesion) · {{ \Carbon\Carbon::parse($primera->hora_sesion)->format('H:i') }} @endif
                </p>
                <p class="mb-0"><strong>Asientos:</strong>
                    {{ $reservas->map(fn($r) => 'F'.$r->fila.'-A'.$r->asiento)->join(', ') }}
                </p>
            </div>
            @if($primera->fecha_validacion)
                <p class="text-muted small">
                    Validada el {{ \Carbon\Carbon::parse($primera->fecha_validacion)->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>

    @elseif($estado === 'pronto')
        @php $primera = $reservas->first(); @endphp
        <div class="card border-0 shadow text-center p-4">
            <div class="mb-3">
                <i class="bi bi-clock-fill text-warning" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold text-warning mb-1">DEMASIADO PRONTO</h3>
            <p class="text-muted mb-3">La validación abre a las <strong>{{ $abre_en }}</strong></p>
            <div class="text-start bg-light rounded p-3">
                <p class="mb-1"><strong>Película:</strong> {{ $primera->pelicula_titulo }}</p>
                <p class="mb-1"><strong>Sesión:</strong>
                    {{ \Carbon\Carbon::parse($primera->fecha_sesion)->format('d/m/Y') }}
                    @if($primera->hora_sesion) · {{ \Carbon\Carbon::parse($primera->hora_sesion)->format('H:i') }} @endif
                </p>
                <p class="mb-0"><strong>Asientos:</strong>
                    {{ $reservas->map(fn($r) => 'F'.$r->fila.'-A'.$r->asiento)->join(', ') }}
                </p>
            </div>
        </div>

    @elseif($estado === 'tarde')
        @php $primera = $reservas->first(); @endphp
        <div class="card border-0 shadow text-center p-4">
            <div class="mb-3">
                <i class="bi bi-clock-history text-danger" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold text-danger mb-1">FUERA DE HORARIO</h3>
            <p class="text-muted mb-3">El plazo de entrada para esta sesión ha finalizado.</p>
            <div class="text-start bg-light rounded p-3">
                <p class="mb-1"><strong>Película:</strong> {{ $primera->pelicula_titulo }}</p>
                <p class="mb-0"><strong>Sesión:</strong>
                    {{ \Carbon\Carbon::parse($primera->fecha_sesion)->format('d/m/Y') }}
                    @if($primera->hora_sesion) · {{ \Carbon\Carbon::parse($primera->hora_sesion)->format('H:i') }} @endif
                </p>
            </div>
        </div>

    @else
        <div class="card border-0 shadow text-center p-4">
            <div class="mb-3">
                <i class="bi bi-slash-circle-fill text-secondary" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold text-secondary mb-1">CÓDIGO INVÁLIDO</h3>
            <p class="text-muted">Este código QR no corresponde a ninguna entrada.</p>
        </div>
    @endif
</div>
@endsection
