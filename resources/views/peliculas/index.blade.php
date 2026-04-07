@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Películas</h2>
        <a href="{{ route('peliculas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nueva película
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($peliculas->isEmpty())
        <p class="text-muted">No hay películas registradas.</p>
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
                                @if($pelicula->genero) {{ $pelicula->genero }} · @endif
                                {{ $pelicula->duracion }} min
                            </p>
                            @if($pelicula->descripcion)
                                <p class="card-text">{{ Str::limit($pelicula->descripcion, 100) }}</p>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <form action="{{ route('peliculas.destroy', $pelicula->id_pelicula) }}" method="POST"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar esta película?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
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
@endsection
