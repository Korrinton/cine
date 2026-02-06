@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow border-0">
            {{-- Encabezado idéntico al Login (Negro con texto blanco) --}}
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0">Mi Perfil</h4>
            </div>

            <div class="card-body p-4">
                {{-- Mensaje de éxito --}}
                @if(session('success'))
                    <div class="alert alert-success py-2 small shadow-sm text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('perfil.actualizar') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $usuario->nombre }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" value="{{ $usuario->apellidos }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-muted">Correo electrónico (Solo lectura)</label>
                        <input type="email" class="form-control bg-light" value="{{ $usuario->correo }}" readonly>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-primary">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
            
            <div class="card-footer text-center py-3">
                <small><a href="{{ url('/dashboard') }}" class="text-muted">Volver al inicio</a></small>
            </div>
        </div>
    </div>
</div>
@endsection