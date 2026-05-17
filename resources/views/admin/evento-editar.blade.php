@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 700px;">

    <a href="{{ route('admin.eventos') }}" class="text-muted small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Volver a eventos
    </a>
    <h2 class="fw-bold mt-1 mb-4">Editar Evento</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.eventos.actualizar', $evento->id_eventos) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- PELÍCULA (solo lectura) --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Película</div>
            <div class="card-body">
                <p class="fw-semibold mb-0">{{ $evento->pelicula->titulo ?? 'Sin título' }}</p>
                <p class="text-muted small mb-0">
                    {{ $evento->pelicula->duracion ?? '?' }} min
                    @if($evento->pelicula->genero) · {{ $evento->pelicula->genero }} @endif
                </p>
            </div>
        </div>

        {{-- SALA Y FECHAS --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Sala y período de proyección</div>
            <div class="card-body">

                <div class="mb-3">
                    <label for="id_sala" class="form-label">Sala</label>
                    <select name="id_sala" id="id_sala" class="form-select" required>
                        <option value="">— Selecciona —</option>
                        @foreach($salas as $sala)
                            <option value="{{ $sala->id_sala }}"
                                {{ old('id_sala', $evento->id_sala) == $sala->id_sala ? 'selected' : '' }}>
                                {{ $sala->nombre }} — aforo {{ $sala->aforo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_estreno" class="form-label">Fecha de inicio</label>
                        <input type="date" name="fecha_estreno" id="fecha_estreno" class="form-control"
                            value="{{ old('fecha_estreno', $evento->fecha_estreno) }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_final" class="form-label">Fecha de fin</label>
                        <input type="date" name="fecha_final" id="fecha_final" class="form-control"
                            value="{{ old('fecha_final', $evento->fecha_final) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label">Precio por entrada (€)</label>
                    <input type="number" name="precio" id="precio" class="form-control" min="0" step="0.01"
                        value="{{ old('precio', $evento->precio) }}" required>
                </div>

            </div>
        </div>

        {{-- HORARIOS --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-clock me-1"></i>Horarios de sesión
                <small class="text-muted fw-normal ms-2">(cine abierto 12:00 – 00:00)</small>
            </div>
            <div class="card-body">
                <p class="form-label fw-semibold mb-2">
                    Selecciona uno o varios horarios <span class="text-danger">*</span>
                </p>
                <div id="contenedor_horarios" class="d-flex flex-wrap gap-2"></div>
                <div class="form-text mt-2">Cada horario marcado crea una sesión diaria.</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Guardar cambios</button>
    </form>
</div>

<script>
    const duracion        = {{ $evento->pelicula->duracion ?? 0 }};
    const horariosActuales = @json(old('horarios', $horariosActuales));

    const INICIO_CINE = 12 * 60;
    const FIN_CINE    = 24 * 60;
    const INTERVALO   = 30;

    function minAHora(min) {
        const h = String(Math.floor(min / 60)).padStart(2, '0');
        const m = String(min % 60).padStart(2, '0');
        return h + ':' + m;
    }

    function generarSlots() {
        const contenedor   = document.getElementById('contenedor_horarios');
        contenedor.innerHTML = '';

        const ultimoInicio = FIN_CINE - duracion;

        for (let t = INICIO_CINE; t <= ultimoInicio; t += INTERVALO) {
            const val     = minAHora(t);
            const fin     = minAHora(t + duracion);
            const id      = 'hora_' + t;
            const checked = horariosActuales.includes(val) ? 'checked' : '';

            const esMatinal  = t < 13 * 60;
            const esNocturno = t >= 22 * 60;
            let bgStyle   = '';
            let textStyle = '';
            if (esMatinal) {
                bgStyle = 'background-color:#cfe2ff;border-color:#9ec5fe;';
            } else if (esNocturno) {
                bgStyle   = 'background-color:#1e3a5f;border-color:#0d253f;';
                textStyle = 'color:#fff;';
            }

            contenedor.insertAdjacentHTML('beforeend', `
                <div class="form-check form-check-inline border rounded px-3 py-2" id="wrap_${t}"
                     style="${bgStyle}">
                    <input class="form-check-input" type="checkbox"
                           name="horarios[]" id="${id}" value="${val}"
                           data-min="${t}" data-duracion="${duracion}"
                           ${checked}
                           onchange="actualizarConflictos()">
                    <label class="form-check-label fw-semibold" for="${id}" style="${textStyle}">
                        ${val}<small class="fw-normal" style="${textStyle}opacity:.8"> – ${fin}</small>
                    </label>
                </div>
            `);
        }

        actualizarConflictos();
    }

    function actualizarConflictos() {
        const checkboxes    = document.querySelectorAll('#contenedor_horarios input[type=checkbox]');
        const seleccionados = [...checkboxes].filter(c => c.checked).map(c => parseInt(c.dataset.min));

        checkboxes.forEach(cb => {
            const t        = parseInt(cb.dataset.min);
            const dur      = parseInt(cb.dataset.duracion);
            const solapado = seleccionados.some(s => s !== t && Math.abs(s - t) < dur);
            const wrap     = document.getElementById('wrap_' + t);

            if (solapado && !cb.checked) {
                cb.disabled = true;
                wrap.classList.add('opacity-50');
                wrap.title = 'Se solapa con un horario ya seleccionado';
            } else {
                cb.disabled = false;
                wrap.classList.remove('opacity-50');
                wrap.title = '';
            }
        });
    }

    function sincronizarFechaFinal() {
        const inicio = document.getElementById('fecha_estreno').value;
        const fin    = document.getElementById('fecha_final');
        if (inicio) {
            fin.min = inicio;
            if (fin.value && fin.value < inicio) {
                fin.value = inicio;
            }
        }
    }

    document.getElementById('fecha_estreno').addEventListener('change', sincronizarFechaFinal);

    window.addEventListener('DOMContentLoaded', () => {
        sincronizarFechaFinal();
        generarSlots();
    });
</script>
@endsection
