@extends('layouts.app')

@section('content')
<div class="py-4">

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Cartelera</h2>
            <p class="text-muted mb-0 small">Películas en proyección</p>
        </div>
    </div>

    {{-- Sin eventos --}}
    @if($eventos->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-camera-reels" style="font-size: 3rem; color: #aaa;"></i>
            <p class="text-muted mt-3">No hay eventos programados actualmente.</p>
        </div>

    {{-- Grid de eventos --}}
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($eventos as $evento)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">

                        {{-- Póster --}}
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

                            {{-- Título --}}
                            <h5 class="card-title fw-bold mb-1">
                                {{ $evento->pelicula->titulo ?? 'Sin título' }}
                            </h5>

                            {{-- Género y duración --}}
                            @if($evento->pelicula)
                                <p class="text-muted small mb-2">
                                    @if($evento->pelicula->genero)
                                        <span class="badge bg-secondary me-1">{{ $evento->pelicula->genero }}</span>
                                    @endif
                                    <i class="bi bi-clock me-1"></i>{{ $evento->pelicula->duracion }} min
                                </p>
                            @endif

                            {{-- Sinopsis --}}
                            @if($evento->pelicula && $evento->pelicula->descripcion)
                                <p class="card-text small text-muted mb-3" style="flex-grow: 1;">
                                    {{ Str::limit($evento->pelicula->descripcion, 110) }}
                                </p>
                            @else
                                <div style="flex-grow: 1;"></div>
                            @endif

                            {{-- Info del evento --}}
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
                                @if(isset($evento->precio))
                                    <li>
                                        <i class="bi bi-ticket-perforated me-1 text-primary"></i>
                                        <strong>{{ number_format($evento->precio, 2) }} €</strong>
                                    </li>
                                @endif
                            </ul>

                            {{-- Botón reservar --}}
                            @auth
                                <a href="{{ route('reservas.mapa', $evento->id_eventos) }}"
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-ticket me-1"></i>Comprar entradas
                                </a>
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
@endsection
