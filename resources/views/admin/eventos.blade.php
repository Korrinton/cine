@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Panel admin
            </a>
            <h2 class="fw-bold mb-0 mt-1">Eventos</h2>
        </div>
        <a href="{{ route('admin.eventos.crear') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nuevo evento
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($eventos->isEmpty())
        <p class="text-muted">No hay eventos registrados.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Película</th>
                        <th>Sala</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Inicio</th>
                        <th class="text-center">Fin</th>
                        <th class="text-center">Sesiones</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventos as $evento)
                        <tr>
                            <td>{{ $evento->id_eventos }}</td>
                            <td>
                                @if($evento->pelicula)
                                    <div class="fw-semibold">{{ $evento->pelicula->titulo }}</div>
                                    <div class="text-muted small">
                                        @if($evento->pelicula->genero) {{ $evento->pelicula->genero }} · @endif
                                        {{ $evento->pelicula->duracion }} min
                                    </div>
                                @else
                                    <span class="text-muted">Sin película</span>
                                @endif
                            </td>
                            <td>{{ $evento->sala->nombre ?? '—' }}</td>
                            <td class="text-center">{{ number_format($evento->precio, 2) }} €</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($evento->fecha_estreno)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($evento->fecha_final)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @forelse($evento->sesiones as $sesion)
                                    <span class="badge bg-secondary me-1">
                                        {{ \Carbon\Carbon::parse($sesion->hora_inicio)->format('H:i') }}
                                    </span>
                                @empty
                                    <span class="text-muted small">—</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.eventos.editar', $evento->id_eventos) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>Editar
                                    </a>
                                    <form action="{{ route('admin.eventos.eliminar', $evento->id_eventos) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este evento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash me-1"></i>Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
