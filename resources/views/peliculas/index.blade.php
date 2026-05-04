@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-film me-2"></i>Películas</h2>
        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#panelCrearPelicula">
            <i class="bi bi-plus-lg me-1"></i>Nueva película
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Grid de películas --}}
    @if($peliculas->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-camera-reels" style="font-size: 3rem;"></i>
            <p class="mt-3">No hay películas registradas.</p>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($peliculas as $pelicula)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        @if($pelicula->imagen)
                            <img src="{{ asset('storage/' . $pelicula->imagen) }}"
                                 class="card-img-top"
                                 style="height: 300px; object-fit: cover;"
                                 alt="{{ $pelicula->titulo }}">
                        @else
                            <div class="bg-secondary d-flex align-items-center justify-content-center"
                                 style="height: 300px;">
                                <i class="bi bi-film text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $pelicula->titulo }}</h5>
                            <p class="card-text text-muted small">
                                @if($pelicula->genero)
                                    <span class="badge bg-secondary me-1">{{ $pelicula->genero }}</span>
                                @endif
                                <span class="badge bg-dark">
                                    <i class="bi bi-clock me-1"></i>{{ $pelicula->duracion }} min
                                </span>
                            </p>
                            @if($pelicula->descripcion)
                                <p class="card-text small text-muted">{{ Str::limit($pelicula->descripcion, 100) }}</p>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <form action="{{ route('admin.peliculas.eliminar', $pelicula->id_pelicula) }}" method="POST"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar «{{ $pelicula->titulo }}»?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ===== BARRA LATERAL: Crear película ===== --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="panelCrearPelicula" aria-labelledby="panelCrearPeliculaLabel"
     style="width: 420px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="panelCrearPeliculaLabel">
            <i class="bi bi-plus-circle me-2 text-primary"></i>Nueva Película
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>

    <div class="offcanvas-body">

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.peliculas.guardar') }}" method="POST" enctype="multipart/form-data" id="formCrearPelicula">
            @csrf

            <div class="mb-3">
                <label for="titulo" class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror"
                       value="{{ old('titulo') }}" placeholder="Ej: Inception" required>
                @error('titulo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                          rows="4" placeholder="Sinopsis de la película…">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label for="duracion" class="form-label fw-semibold">Duración (min) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                        <input type="number" name="duracion" id="duracion"
                               class="form-control @error('duracion') is-invalid @enderror"
                               min="1" max="600" value="{{ old('duracion') }}" placeholder="120" required>
                        @error('duracion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-6">
                    <label for="genero" class="form-label fw-semibold">Género</label>
                    <select name="genero" id="genero" class="form-select @error('genero') is-invalid @enderror">
                        <option value="">— Seleccionar —</option>
                        @foreach(['Acción','Animación','Aventura','Ciencia Ficción','Comedia','Drama','Fantasía','Horror','Musical','Romance','Suspense','Thriller'] as $g)
                            <option value="{{ $g }}" {{ old('genero') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                    @error('genero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Carga de imagen con vista previa --}}
            <div class="mb-4">
                <label for="imagen" class="form-label fw-semibold">Cartel</label>
                <input type="file" name="imagen" id="imagen"
                       class="form-control @error('imagen') is-invalid @enderror"
                       accept="image/*" onchange="previsualizarImagen(event)">
                <div class="form-text">Opcional · JPG/PNG/WEBP · Máx. 2 MB</div>
                @error('imagen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="contenedorPrevia" class="mt-2 d-none text-center">
                    <img id="previaImagen" src="" alt="Vista previa"
                         class="img-fluid rounded shadow-sm" style="max-height: 200px; object-fit: cover;">
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Guardar película
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previsualizarImagen(event) {
        const archivo = event.target.files[0];
        if (!archivo) return;
        const contenedor = document.getElementById('contenedorPrevia');
        const previa = document.getElementById('previaImagen');
        previa.src = URL.createObjectURL(archivo);
        contenedor.classList.remove('d-none');
    }

    // Si hay errores de validación, reabrir el panel automáticamente
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Offcanvas(document.getElementById('panelCrearPelicula')).show();
        });
    @endif
</script>
@endsection
