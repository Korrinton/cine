@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Panel admin
            </a>
            <h2 class="fw-bold mb-0 mt-1">Salas</h2>
        </div>
        <a href="{{ route('admin.salas.crear') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nueva sala
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($salas->isEmpty())
        <p class="text-muted">No hay salas registradas.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th class="text-center">Filas</th>
                        <th class="text-center">Butacas/fila</th>
                        <th class="text-center">Aforo total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salas as $sala)
                        <tr>
                            <td>{{ $sala->id_sala }}</td>
                            <td class="fw-semibold">{{ $sala->nombre }}</td>
                            <td class="text-center">{{ $sala->filas }}</td>
                            <td class="text-center">{{ $sala->sillas }}</td>
                            <td class="text-center">{{ $sala->aforo }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.salas.eliminar', $sala->id_sala) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar {{ $sala->nombre }}? Se eliminarán también sus eventos.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash me-1"></i>Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
