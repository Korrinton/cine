@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Panel de Administración</h2>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="fs-1 text-primary"><i class="bi bi-calendar-event"></i></div>
                <div class="fs-3 fw-bold">{{ $totalEventos }}</div>
                <div class="text-muted small">Eventos</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="fs-1 text-success"><i class="bi bi-door-open"></i></div>
                <div class="fs-3 fw-bold">{{ $totalSalas }}</div>
                <div class="text-muted small">Salas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="fs-1 text-warning"><i class="bi bi-film"></i></div>
                <div class="fs-3 fw-bold">{{ $totalPeliculas }}</div>
                <div class="text-muted small">Películas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="fs-1 text-danger"><i class="bi bi-ticket-perforated"></i></div>
                <div class="fs-3 fw-bold">{{ $totalReservas }}</div>
                <div class="text-muted small">Reservas</div>
            </div>
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <h5 class="fw-semibold mb-3">Gestión</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('admin.eventos') }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3 py-4">
                    <i class="bi bi-calendar-event fs-2 text-primary"></i>
                    <div>
                        <div class="fw-semibold">Eventos</div>
                        <div class="text-muted small">Ver, crear y eliminar eventos</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.salas') }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3 py-4">
                    <i class="bi bi-door-open fs-2 text-success"></i>
                    <div>
                        <div class="fw-semibold">Salas</div>
                        <div class="text-muted small">Ver, crear y eliminar salas</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.peliculas') }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3 py-4">
                    <i class="bi bi-film fs-2 text-warning"></i>
                    <div>
                        <div class="fw-semibold">Películas</div>
                        <div class="text-muted small">Ver y gestionar películas</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection
