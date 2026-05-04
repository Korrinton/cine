@extends('layouts.app')

@section('content')
<div class="py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Cartelera</h2>
            <p class="text-muted mb-0 small">Películas en proyección</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($eventos->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-camera-reels" style="font-size: 3rem; color: #aaa;"></i>
            <p class="text-muted mt-3">No hay eventos programados actualmente.</p>
        </div>

    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($eventos as $evento)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">

                        @if($evento->pelicula && $evento->pelicula->imagen)
                            <img src="{{ asset('storage/' . $evento->pelicula->imagen) }}"
                                 class="card-img-top"
                                 style="height: 320px; object-fit: cover;"
                                 alt="{{ $evento->pelicula->titulo }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-secondary text-white"
                                 style="height: 320px;">
                                <i class="bi bi-film" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title fw-bold mb-1">
                                {{ $evento->pelicula->titulo ?? 'Sin título' }}
                            </h5>

                            @if($evento->pelicula)
                                <p class="text-muted small mb-2">
                                    @if($evento->pelicula->genero)
                                        <span class="badge bg-secondary me-1">{{ $evento->pelicula->genero }}</span>
                                    @endif
                                    <i class="bi bi-clock me-1"></i>{{ $evento->pelicula->duracion }} min
                                </p>
                            @endif

                            @if($evento->pelicula && $evento->pelicula->descripcion)
                                <p class="card-text small text-muted mb-3" style="flex-grow: 1;">
                                    {{ Str::limit($evento->pelicula->descripcion, 110) }}
                                </p>
                            @else
                                <div style="flex-grow: 1;"></div>
                            @endif

                            <ul class="list-unstyled small mb-3">
                                @if($evento->sala)
                                    <li class="mb-1">
                                        <i class="bi bi-door-open me-1 text-primary"></i>
                                        {{ $evento->sala->nombre }}
                                        <span class="text-muted">(aforo {{ $evento->sala->aforo }})</span>
                                    </li>
                                @endif
                                @if($evento->fecha_estreno)
                                    <li class="mb-1">
                                        <i class="bi bi-calendar-event me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($evento->fecha_estreno)->format('d/m/Y') }}
                                        @if($evento->fecha_final)
                                            — {{ \Carbon\Carbon::parse($evento->fecha_final)->format('d/m/Y') }}
                                        @endif
                                    </li>
                                @endif
                                @if($evento->sesiones->count())
                                    <li class="mb-1">
                                        <i class="bi bi-clock me-1 text-primary"></i>
                                        {{ $evento->sesiones->map(fn($s) => \Carbon\Carbon::parse($s->hora_inicio)->format('H:i'))->join(' · ') }}
                                    </li>
                                @endif
                                @if(isset($evento->precio))
                                    @php
                                        $tieneNocturna = $evento->sesiones->contains(fn($s) => $s->hora_inicio >= '22:00:00');
                                        $tieneMatinal  = $evento->sesiones->contains(fn($s) => $s->hora_inicio < '13:00:00');
                                        $tieneDescuento = $tieneNocturna || $tieneMatinal;
                                    @endphp
                                    <li>
                                        <i class="bi bi-ticket-perforated me-1 text-primary"></i>
                                        <strong>{{ number_format($evento->precio, 2) }} €</strong>
                                        @if($tieneDescuento)
                                            <span class="badge bg-warning text-dark ms-1">
                                                <i class="bi bi-tag me-1"></i>Desde {{ number_format($evento->precio * 0.5, 2) }} €
                                            </span>
                                        @endif
                                    </li>
                                    @if($tieneMatinal || $tieneNocturna)
                                        <li class="mt-1">
                                            @if($tieneMatinal)
                                                <span class="badge bg-info text-dark me-1">
                                                    <i class="bi bi-sunrise me-1"></i>Matinal −50%
                                                </span>
                                            @endif
                                            @if($tieneNocturna)
                                                <span class="badge bg-secondary me-1">
                                                    <i class="bi bi-moon-stars me-1"></i>Nocturno −50%
                                                </span>
                                            @endif
                                        </li>
                                    @endif
                                @endif
                            </ul>

                            @auth
                                <button type="button"
                                        class="btn btn-primary w-100"
                                        onclick="abrirModalFecha(
                                            {{ $evento->id_eventos }},
                                            '{{ addslashes($evento->pelicula->titulo ?? 'Sin título') }}',
                                            '{{ $evento->fecha_estreno }}',
                                            '{{ $evento->fecha_final ?? $evento->fecha_estreno }}',
                                            {{ $evento->sesiones->map(fn($s) => \Carbon\Carbon::parse($s->hora_inicio)->format('H:i'))->values()->toJson() }}
                                        )">
                                    <i class="bi bi-ticket me-1"></i>Comprar entradas
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                   class="btn btn-outline-primary w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Inicia sesión para reservar
                                </a>
                            @endauth

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ===== MODAL SELECCIÓN DE FECHA ===== --}}
<div class="modal fade" id="modalFecha" tabindex="-1" aria-labelledby="modalFechaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalFechaLabel">
                    <i class="bi bi-calendar-check me-2"></i>Selecciona la fecha
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <p class="fw-semibold mb-1" id="modalPelicula"></p>
                <p class="text-muted small mb-3" id="modalRangoDates"></p>

                <div class="mb-3">
                    <label for="inputFecha" class="form-label fw-semibold">
                        ¿Qué día quieres ir? <span class="text-danger">*</span>
                    </label>
                    <input type="date" id="inputFecha" class="form-control form-control-lg"
                           onchange="mostrarHoras()">
                </div>

                <div id="bloqueHoras" class="mb-2" style="display:none;">
                    <label class="form-label fw-semibold">
                        Elige la hora de la sesión <span class="text-danger">*</span>
                    </label>
                    <div id="contenedorHoras" class="d-flex flex-wrap gap-2"></div>
                </div>

                <div id="errorModal" class="text-danger small mt-2 d-none"></div>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4 d-grid">
                <button type="button" class="btn btn-primary btn-lg" onclick="irAlMapa()">
                    <i class="bi bi-map me-2"></i>Elegir asientos
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    let eventoIdActual  = null;
    let horasDisponibles = [];

    function abrirModalFecha(idEvento, titulo, fechaMin, fechaMax, horas) {
        eventoIdActual   = idEvento;
        horasDisponibles = horas;

        document.getElementById('modalPelicula').textContent = titulo;

        const fmtMin = formatearFecha(fechaMin);
        const fmtMax = formatearFecha(fechaMax);
        document.getElementById('modalRangoDates').textContent =
            'Sesiones disponibles: ' + fmtMin + (fmtMin !== fmtMax ? ' – ' + fmtMax : '');

        const input = document.getElementById('inputFecha');
        input.min   = fechaMin.substring(0, 10);
        input.max   = fechaMax.substring(0, 10);
        input.value = fechaMin.substring(0, 10);

        document.getElementById('errorModal').classList.add('d-none');
        mostrarHoras();

        new bootstrap.Modal(document.getElementById('modalFecha')).show();
    }

    function mostrarHoras() {
        const contenedor = document.getElementById('contenedorHoras');
        const bloque     = document.getElementById('bloqueHoras');

        contenedor.innerHTML = '';

        if (!horasDisponibles.length) {
            bloque.style.display = 'none';
            return;
        }

        bloque.style.display = '';
        horasDisponibles.forEach((hora, i) => {
            const id = 'hora_modal_' + i;
            contenedor.insertAdjacentHTML('beforeend', `
                <div>
                    <input type="radio" class="btn-check" name="hora_modal" id="${id}" value="${hora}" ${i === 0 ? 'checked' : ''}>
                    <label class="btn btn-outline-primary" for="${id}">${hora}</label>
                </div>
            `);
        });
    }

    function irAlMapa() {
        const fecha = document.getElementById('inputFecha').value;
        const err   = document.getElementById('errorModal');

        if (!fecha) {
            err.textContent = 'Por favor selecciona una fecha.';
            err.classList.remove('d-none');
            return;
        }

        let hora = null;
        if (horasDisponibles.length) {
            const checked = document.querySelector('input[name="hora_modal"]:checked');
            if (!checked) {
                err.textContent = 'Por favor selecciona un horario.';
                err.classList.remove('d-none');
                return;
            }
            hora = checked.value;
        }

        err.classList.add('d-none');
        let url = '/reservar/' + eventoIdActual + '?fecha=' + fecha;
        if (hora) url += '&hora=' + hora;
        window.location.href = url;
    }

    function formatearFecha(fechaStr) {
        const d = new Date(fechaStr);
        return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric', timeZone: 'UTC' });
    }
</script>
@endsection
