@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 700px;">

    <a href="{{ route('admin.eventos') }}" class="text-muted small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Volver a eventos
    </a>
    <h2 class="fw-bold mt-1 mb-4">Nuevo Evento</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.eventos.guardar') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- PELÍCULA --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Película</div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">¿Qué quieres hacer?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="modo_pelicula" id="modo_existente"
                                value="existente" {{ old('modo_pelicula', 'existente') === 'existente' ? 'checked' : '' }}>
                            <label class="form-check-label" for="modo_existente">Seleccionar película existente</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="modo_pelicula" id="modo_nueva"
                                value="nueva" {{ old('modo_pelicula') === 'nueva' ? 'checked' : '' }}>
                            <label class="form-check-label" for="modo_nueva">Crear nueva película</label>
                        </div>
                    </div>
                </div>

                <div id="bloque_existente">
                    <div class="mb-3">
                        <label for="id_pelicula" class="form-label">Película</label>
                        <select name="id_pelicula" id="id_pelicula" class="form-select">
                            <option value="">— Selecciona —</option>
                            @foreach($peliculas as $p)
                                <option value="{{ $p->id_pelicula }}" {{ old('id_pelicula') == $p->id_pelicula ? 'selected' : '' }}>
                                    {{ $p->titulo }} ({{ $p->duracion }} min)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="bloque_nueva" style="display:none;">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duracion" class="form-label">Duración (minutos)</label>
                            <input type="number" name="duracion" id="duracion" class="form-control" min="1" value="{{ old('duracion') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="genero" class="form-label">Género</label>
                            <input type="text" name="genero" id="genero" class="form-control" value="{{ old('genero') }}"
                                placeholder="Acción, Drama, Comedia…">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del cartel</label>
                        <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
                    </div>
                </div>

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
                            <option value="{{ $sala->id_sala }}" {{ old('id_sala') == $sala->id_sala ? 'selected' : '' }}>
                                {{ $sala->nombre }} — aforo {{ $sala->aforo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_estreno" class="form-label">Fecha de inicio</label>
                        <input type="date" name="fecha_estreno" id="fecha_estreno" class="form-control"
                            value="{{ old('fecha_estreno') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_final" class="form-label">Fecha de fin</label>
                        <input type="date" name="fecha_final" id="fecha_final" class="form-control"
                            value="{{ old('fecha_final') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label">Precio por entrada (€)</label>
                    <input type="number" name="precio" id="precio" class="form-control" min="0" step="0.01"
                        value="{{ old('precio', 0) }}" required>
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

                <div id="aviso_sin_pelicula" class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Selecciona o introduce la duración de la película para ver los horarios disponibles.
                </div>

                <div id="bloque_horario" style="display:none;">
                    <p class="form-label fw-semibold mb-2">
                        Selecciona uno o varios horarios <span class="text-danger">*</span>
                    </p>
                    <div id="contenedor_horarios" class="d-flex flex-wrap gap-2"></div>
                    <div class="form-text mt-2">Cada horario marcado crea una sesión diaria.</div>
                </div>

            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Crear Evento</button>
    </form>
</div>

<script>
    // Datos de duración de cada película
    const duraciones = {
        @foreach($peliculas as $p)
            {{ $p->id_pelicula }}: {{ $p->duracion }},
        @endforeach
    };

    const INICIO_CINE = 12 * 60; // 12:00 en minutos
    const FIN_CINE    = 24 * 60; // 00:00 (medianoche) en minutos
    const INTERVALO   = 30;      // cada 30 minutos

    function minAHora(min) {
        const h = String(Math.floor(min / 60)).padStart(2, '0');
        const m = String(min % 60).padStart(2, '0');
        return h + ':' + m;
    }

    function generarSlots(duracionMin) {
        const contenedor = document.getElementById('contenedor_horarios');
        const aviso      = document.getElementById('aviso_sin_pelicula');
        const bloque     = document.getElementById('bloque_horario');

        contenedor.innerHTML = '';

        if (!duracionMin || duracionMin <= 0) {
            aviso.innerHTML     = '<i class="bi bi-info-circle me-1"></i>Selecciona o introduce la duración de la película para ver los horarios disponibles.';
            aviso.style.display = '';
            bloque.style.display = 'none';
            return;
        }

        const ultimoInicio = FIN_CINE - duracionMin;

        if (ultimoInicio < INICIO_CINE) {
            aviso.innerHTML     = '<i class="bi bi-exclamation-triangle me-1"></i>La película es demasiado larga para el horario del cine (12:00–20:00).';
            aviso.style.display = '';
            bloque.style.display = 'none';
            return;
        }

        aviso.style.display  = 'none';
        bloque.style.display = '';

        const oldHorarios = @json(old('horarios', []));

        for (let t = INICIO_CINE; t <= ultimoInicio; t += INTERVALO) {
            const val  = minAHora(t);
            const fin  = minAHora(t + duracionMin);
            const id   = 'hora_' + t;
            const checked = oldHorarios.includes(val) ? 'checked' : '';

            const esMatinal   = t < 13 * 60;
            const esNocturno  = t >= 22 * 60;
            let bgStyle = '';
            let textStyle = '';
            if (esMatinal) {
                bgStyle   = 'background-color:#cfe2ff;border-color:#9ec5fe;';
            } else if (esNocturno) {
                bgStyle   = 'background-color:#1e3a5f;border-color:#0d253f;';
                textStyle = 'color:#fff;';
            }

            contenedor.insertAdjacentHTML('beforeend', `
                <div class="form-check form-check-inline border rounded px-3 py-2" id="wrap_${t}"
                     style="${bgStyle}">
                    <input class="form-check-input" type="checkbox"
                           name="horarios[]" id="${id}" value="${val}"
                           data-min="${t}" data-duracion="${duracionMin}"
                           ${checked}
                           onchange="actualizarConflictos()">
                    <label class="form-check-label fw-semibold" for="${id}" style="${textStyle}">
                        ${val}<small class="fw-normal" style="${textStyle}opacity:.8"> – ${fin}</small>
                    </label>
                </div>
            `);
        }

        // Restaurar conflictos si había old() marcados
        if (oldHorarios.length) actualizarConflictos();
    }

    function actualizarConflictos() {
        const checkboxes = document.querySelectorAll('#contenedor_horarios input[type=checkbox]');
        const seleccionados = [...checkboxes].filter(c => c.checked).map(c => parseInt(c.dataset.min));

        checkboxes.forEach(cb => {
            const t        = parseInt(cb.dataset.min);
            const dur      = parseInt(cb.dataset.duracion);
            const solapado = seleccionados.some(s => s !== t && Math.abs(s - t) < dur);

            const wrap = document.getElementById('wrap_' + t);
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

    function getDuracionActual() {
        const modo = document.querySelector('input[name="modo_pelicula"]:checked').value;
        if (modo === 'existente') {
            const id = document.getElementById('id_pelicula').value;
            return id ? (duraciones[id] || 0) : 0;
        } else {
            return parseInt(document.getElementById('duracion').value) || 0;
        }
    }

    //Cambio de película existente
    document.getElementById('id_pelicula').addEventListener('change', function () {
        generarSlots(duraciones[this.value] || 0);
    });

    //Cambio de duración en nueva película
    document.getElementById('duracion').addEventListener('input', function () {
        generarSlots(parseInt(this.value) || 0);
    });

    //Cambio de modo pelicula
    const radios = document.querySelectorAll('input[name="modo_pelicula"]');
    const bloqueExistente = document.getElementById('bloque_existente');
    const bloqueNueva     = document.getElementById('bloque_nueva');

    function actualizarBloques() {
        const modo = document.querySelector('input[name="modo_pelicula"]:checked').value;
        bloqueExistente.style.display = modo === 'existente' ? '' : 'none';
        bloqueNueva.style.display     = modo === 'nueva'     ? '' : 'none';
        generarSlots(getDuracionActual());
    }

    radios.forEach(r => r.addEventListener('change', actualizarBloques));
    actualizarBloques();

    // Restaurar horario si hay old() tras error de validación
    window.addEventListener('DOMContentLoaded', () => generarSlots(getDuracionActual()));
</script>
@endsection
