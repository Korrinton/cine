@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Salas</h2>
        <a href="{{ route('salas.create') }}" class="btn btn-primary">
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
                        <th>Filas</th>
                        <th>Butacas por fila</th>
                        <th>Aforo total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salas as $sala)
                        <tr>
                            <td>{{ $sala->id_sala }}</td>
                            <td>{{ $sala->nombre }}</td>
                            <td>{{ $sala->filas }}</td>
                            <td>{{ $sala->sillas }}</td>
                            <td>{{ $sala->aforo }}</td>
                            <td class="text-end">
                                <form action="{{ route('salas.destroy', $sala->id_sala) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta sala? Se eliminarán también sus eventos.')">
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
