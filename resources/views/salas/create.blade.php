@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 500px;">
    <h2 class="mb-4">Crear Nueva Sala</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('salas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la sala</label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                   value="{{ old('nombre') }}" placeholder="Ej: Sala 1" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="filas" class="form-label">Filas</label>
                <input type="number" name="filas" id="filas" class="form-control"
                       value="{{ old('filas') }}" min="1" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="sillas" class="form-label">Butacas por fila</label>
                <input type="number" name="sillas" id="sillas" class="form-control"
                       value="{{ old('sillas') }}" min="1" required>
            </div>
        </div>

        <div class="alert alert-secondary py-2 small mb-3" id="aforo-preview">
            Aforo total: <strong id="aforo-valor">—</strong> butacas
        </div>

        <button type="submit" class="btn btn-primary">Crear Sala</button>
        <a href="{{ route('salas.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
    const filas  = document.getElementById('filas');
    const sillas = document.getElementById('sillas');
    const aforo  = document.getElementById('aforo-valor');

    function actualizarAforo() {
        const f = parseInt(filas.value) || 0;
        const s = parseInt(sillas.value) || 0;
        aforo.textContent = (f && s) ? f * s : '—';
    }

    filas.addEventListener('input', actualizarAforo);
    sillas.addEventListener('input', actualizarAforo);
</script>
@endsection
