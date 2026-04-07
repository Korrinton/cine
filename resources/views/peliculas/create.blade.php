@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 600px;">
    <h2 class="mb-4">Crear Nueva Película</h2>

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

    <form action="{{ route('peliculas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="duracion" class="form-label">Duración (minutos)</label>
                <input type="number" name="duracion" id="duracion" class="form-control" min="1" value="{{ old('duracion') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="genero" class="form-label">Género</label>
                <input type="text" name="genero" id="genero" class="form-control" value="{{ old('genero') }}" placeholder="Acción, Drama, Comedia…">
            </div>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del cartel</label>
            <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
            <div class="form-text">Opcional. Máximo 2 MB.</div>
        </div>

        <button type="submit" class="btn btn-primary">Crear Película</button>
        <a href="{{ url('/') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
@endsection
