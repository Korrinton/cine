@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 700px;">
    <h2 class="mb-4">Crear Nuevo Evento</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data">
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

                {{-- Seleccionar existente --}}
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

                {{-- Crear nueva --}}
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
                            value="{{ old('fecha_estreno') }}" required>
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

        <button type="submit" class="btn btn-primary">Crear Evento</button>
        <a href="{{ url('/') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
    const radios = document.querySelectorAll('input[name="modo_pelicula"]');
    const bloqueExistente = document.getElementById('bloque_existente');
    const bloqueNueva = document.getElementById('bloque_nueva');

    function actualizarBloques() {
        const modo = document.querySelector('input[name="modo_pelicula"]:checked').value;
        bloqueExistente.style.display = modo === 'existente' ? '' : 'none';
        bloqueNueva.style.display     = modo === 'nueva'     ? '' : 'none';
    }

    radios.forEach(r => r.addEventListener('change', actualizarBloques));
    actualizarBloques();
</script>
@endsection
