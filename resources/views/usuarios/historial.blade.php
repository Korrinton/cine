@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    <h2 class="mb-4"><i class="bi bi-ticket-perforated me-2"></i>Historial de Compras</h2>

    @if($reservas->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bag-x" style="font-size: 3rem;"></i>
            <p class="mt-3">Todavía no has realizado ninguna reserva.</p>
            <a href="{{ url('/') }}" class="btn btn-primary mt-2">Ver cartelera</a>
        </div>
    @else
        @foreach($reservas as $id_evento => $asientos)
            @php
                $primero     = $asientos->first();
                $ids         = $asientos->pluck('id_reserva')->join(', ');
                $asientosTxt = $asientos->map(fn($r) => "F{$r->fila}-A{$r->asiento}")->join(' | ');
                $fechaSesion = $primero->fecha_sesion
                    ? \Carbon\Carbon::parse($primero->fecha_sesion)->format('d/m/Y')
                    : \Carbon\Carbon::parse($primero->fecha_estreno)->format('d/m/Y');
                $horaSesion = $primero->hora_sesion
                    ? \Carbon\Carbon::parse($primero->hora_sesion)->format('H:i')
                    : '';
                $nocturno    = $horaSesion && $horaSesion >= '22:00';
                $matinal     = $horaSesion && $horaSesion < '13:00';
                $diaSemana   = $primero->fecha_sesion ? \Carbon\Carbon::parse($primero->fecha_sesion)->dayOfWeek : null;
                $esMiercoles = $diaSemana === 3;
                $esFinSemana = in_array($diaSemana, [5, 6]);

                if ($esMiercoles) {
                    $precioUnit = $primero->precio * 0.5;
                    $badgeLabel = 'Día del espectador −50%';
                    $badgeClass = 'bg-success';
                } elseif ($nocturno || $matinal) {
                    $precioUnit = max(0, $primero->precio - 3);
                    $badgeLabel = $nocturno ? '−3€ Nocturno' : '−3€ Matinal';
                    $badgeClass = 'bg-warning text-dark';
                } elseif ($esFinSemana) {
                    $precioUnit = $primero->precio + 4;
                    $badgeLabel = 'Viernes/Sábado +4€';
                    $badgeClass = 'bg-danger';
                } else {
                    $precioUnit = $primero->precio;
                    $badgeLabel = null;
                    $badgeClass = null;
                }
                $descuento = $badgeLabel !== null;
                $qrData = implode("\n", [
                    'CINE - ENTRADA',
                    'Película: ' . $primero->pelicula_titulo,
                    'Sala: '     . $primero->sala_nombre,
                    'Fecha: '    . $fechaSesion . ($horaSesion ? ' ' . $horaSesion : ''),
                    'Asientos: ' . $asientosTxt,
                    'Ref: #'     . $ids,
                ]);
            @endphp

            <div class="card shadow-sm mb-4">
                {{-- Cabecera --}}
                <div class="card-header bg-dark text-white d-flex align-items-center gap-3 py-3">
                    @if($primero->pelicula_imagen)
                        <img src="{{ asset('storage/' . $primero->pelicula_imagen) }}"
                             alt="{{ $primero->pelicula_titulo }}"
                             style="width:50px;height:70px;object-fit:cover;border-radius:4px;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-secondary rounded"
                             style="width:50px;height:70px;">
                            <i class="bi bi-film text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-0">{{ $primero->pelicula_titulo }}</h5>
                        <small class="text-white-50">
                            @if($primero->genero)<span class="me-2">{{ $primero->genero }}</span> · @endif
                            <span class="ms-1">{{ $primero->duracion }} min</span>
                        </small>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row align-items-start g-4">

                        {{-- Info + tabla de asientos --}}
                        <div class="col-md-7">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>{{ $primero->sala_nombre }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <i class="bi bi-calendar-event"></i>
                                        <span>{{ $fechaSesion }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <i class="bi bi-clock"></i>
                                        <span>Reservado el {{ \Carbon\Carbon::parse($primero->fecha_reserva)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fila</th>
                                        <th>Asiento</th>
                                        <th class="text-end">Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asientos as $reserva)
                                        <tr>
                                            <td>{{ $reserva->fila }}</td>
                                            <td>{{ $reserva->asiento }}</td>
                                            <td class="text-end">
                                                {{ number_format($precioUnit, 2) }} €
                                                @if($descuento)
                                                    <span class="badge {{ $badgeClass }} ms-1">{{ $badgeLabel }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-semibold">
                                    <tr>
                                        <td colspan="2" class="text-end">Total</td>
                                        <td class="text-end">{{ number_format($precioUnit * $asientos->count(), 2) }} €</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Código QR --}}
                        <div class="col-md-5 text-center">
                            <div class="border rounded p-3 d-inline-block bg-white">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->errorCorrection('M')->generate($qrData) !!}
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="bi bi-qr-code me-1"></i>Muestra este código en la entrada
                            </p>
                            <p class="text-muted" style="font-size: 0.7rem;">Ref: #{{ $ids }}</p>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
