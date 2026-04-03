@extends('layouts.app') 

@section('content')
<div class="py-5"> {{-- Quitamos el container de aquí porque el layout ya trae uno --}}
    <h2 class="text-center mb-4">Películas en Cartelera</h2>
    
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($peliculas as $pelicula)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <img src="{{ $pelicula->poster_url }}" class="card-img-top rounded-top" alt="{{ $pelicula->nombre }}" style="height: 350px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $pelicula->nombre }}</h5>
                        <span class="badge rounded-pill bg-dark mb-2">{{ $pelicula->genero }}</span>
                        <p class="card-text text-muted small">
                            {{ Str::limit($pelicula->sinopsis, 70) }}
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 d-grid pb-3">
                        <a href="{{ route('pelicula.show', $pelicula->id_pelicula) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-ticket-perforated me-1"></i> Ver Horarios
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection