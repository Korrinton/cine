@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0">¿Confirmar Reserva?</h4>
            </div>
            <div class="card-body p-5 text-center">
                
                <div class="border-0 text-start mb-4">
                    <p class="mb-1"><strong>Película:</strong> {{ $evento->pelicula_titulo }}</p>
                    <p class="mb-1"><strong>Sala:</strong> {{ $evento->sala_nombre }}</p>
                    
                    <p class="mb-1 mt-3"><strong>Asientos:</strong><br>
                        @foreach($asientos as $asiento)
                            <span class="">Fila {{ $asiento['f'] }}, Silla {{ $asiento['s'] }}</span><br>
                        @endforeach
                    </p>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('reservas.mapa', $evento->id_eventos) }}" class="btn btn-outline-danger px-5 py-2">Cancelar</a>

                    <form action="{{ route('reservas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_evento" value="{{ $evento->id_eventos }}">
                        <input type="hidden" name="asientos_json" value="{{ json_encode($asientos) }}">
                        <button type="submit" class="btn btn-success px-5 py-2 shadow">Confirmar</button>
                    </form>
                </div>
        </div>
    </div>
</div>
@endsection