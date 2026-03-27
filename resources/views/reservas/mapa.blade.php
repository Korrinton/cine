@extends('layouts.app')

@section('content')
<!--

para crear una reserva deberán existir en la base de datos por lo menos
una sala y un evento vinculado a ella

por ejemplo
INSERT INTO salas (id_sala, nombre, aforo, filas, sillas, created_at, updated_at) 
VALUES (1, 'Sala 1', 100, 10, 10, NOW(), NOW());

INSERT INTO eventos (id_eventos, nombre, id_sala, precio, fecha_estreno, fecha_final, created_at, updated_at) 
VALUES (1, 'Kill Bill', 1, 12, '2026-03-27', '2026-04-20', NOW(), NOW());

para acceder a probar la funcionalidad se debe haber iniciado sesión, de momento,
debe ser mediante la url y la id del evento
por ejemplo
http://localhost:8000/reservar/1

esto busca la sala vinculada al evento y genera un mapa visual, una vez seleccionados los
asientos y creada la reserva, crea los registros y los vincula a la id de usuario

-->


<style>
    .cine-grid { 
        display: grid; gap: 8px; margin: 20px auto; justify-content: center; 
    }

    .silla { 
        width: 35px; height: 35px; border: 1px solid #ccc; cursor: pointer; 
        display: flex; align-items: center; justify-content: center; border-radius: 5px; 
        font-size: 0.8rem; transition: 0.2s;
    }
    
    .silla.ocupada {
        background-color: #ff4d4d; cursor: not-allowed; color: white; border: none; 
    }

    .silla.seleccionada {
        background-color: #4CAF50; color: white; border: none;; 
    }
    
    .pantalla { 
        background: #333; color: white; text-align: center; 
        margin: 0 auto 30px; padding: 10px; width: 80%; border-radius: 0 0 50px 50px;
        text-transform: uppercase; font-weight: bold;
    }
</style>

<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card shadow border-0">
            {{-- Encabezado idéntico al Login (Negro con texto blanco) --}}
            <div class="card-header bg-dark text-white text-center py-3 d-flex justify-content-between align-items-center px-4">
                <h4 class="mb-0">Reserva: {{ $evento->nombre }}</h4>
            </div>

            <div class="card-body p-4 text-center">
                {{-- Alertas de Éxito o Error --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="pantalla shadow-sm">Pantalla</div>

                {{-- Generación del Mapa --}}
                <div class="cine-grid" style="grid-template-columns: repeat({{ $evento->sillas }}, 40px);">
                    @for ($f = 1; $f <= $evento->filas; $f++)
                        @for ($s = 1; $s <= $evento->sillas; $s++)
                            @php 
                                $id = "$f-$s"; 
                                $estaOcupada = in_array($id, $ocupados); 
                            @endphp
                            <div class="silla {{ $estaOcupada ? 'ocupada' : '' }}" 
                                 @if(!$estaOcupada) onclick="gestionarAsiento(this, {{ $f }}, {{ $s }})" @endif
                                 id="silla-{{ $id }}">
                                {{ $s }}
                            </div>
                        @endfor
                    @endfor
                </div>

                <div class="mt-4 p-3 bg-light rounded border">
                    <h6>Asientos seleccionados: <span id="contador-asientos">0</span> / 6</h6>
                    <p class="small text-muted" id="lista-asientos">Ninguno seleccionado</p>
                </div>

                <form action="{{ route('reservas.store') }}" method="POST" id="form-reserva" class="mt-4">
                    @csrf
                    <input type="hidden" name="id_evento" value="{{ $evento->id_eventos }}">
                    <input type="hidden" name="asientos_json" id="asientos_json">
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" id="btn-confirmar" class="btn btn-primary px-5" disabled>
                            Confirmar Reserva
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/reservas-script.js') }}"></script>
@endsection