@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 500px;">

    <a href="{{ route('admin.salas') }}" class="text-muted small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Volver a salas
    </a>
    <h2 class="fw-bold mt-1 mb-4">Nueva Sala</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.salas.guardar') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la sala</label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                   value="{{ old('nombre') }}" placeholder="Ej: Sala 1" required>
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label for="filas" class="form-label">Filas</label>
                <input type="number" name="filas" id="filas" class="form-control"
                       value="{{ old('filas') }}" min="1" required>
            </div>
            <div class="col-6 mb-3">
                <label for="sillas" class="form-label">Butacas por fila</label>
                <input type="number" name="sillas" id="sillas" class="form-control"
                       value="{{ old('sillas') }}" min="1" required>
            </div>
        </div>

        <div class="alert alert-secondary py-2 small mb-4">
            Aforo total: <strong id="aforo-valor">—</strong> butacas
        </div>

        <button type="submit" class="btn btn-primary w-100">Crear Sala</button>
    </form>
</div>

<script>
    const filas  = document.getElementById('filas');
    const sillas = document.getElementById('sillas');
    const aforo  = document.getElementById('aforo-valor');

    function actualizar() {
        const f = parseInt(filas.value) || 0;
        const s = parseInt(sillas.value) || 0;
        aforo.textContent = (f && s) ? f * s : '—';
    }

    filas.addEventListener('input', actualizar);
    sillas.addEventListener('input', actualizar);
</script>
@endsection
