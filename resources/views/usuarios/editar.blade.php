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

                <form action="{{ route('perfil.actualizar') }}" method="POST" class="slow-submit">
                    @csrf
                    @method('PUT')

                    <div class="form-floating mb-3">
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" id="floatName" placeholder="Tu nombre" value="{{ $usuario->nombre }}" required>
                        <label for="floatName">Nombre</label>                        
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="apellidos" class="form-control" id="floatApellidos" placeholder="Apellidos" value="{{ $usuario->apellidos }}">
                        <label for="floatApellidos">Apellidos</label>
                    </div>

                    <hr class="my-4">

                    <div class="form-floating mb-3">
                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror no-select" 
                            id="floatCorreo" placeholder="nombre@ejemplo.com" value="{{ $usuario->correo }}"  tabindex="-1">
                        <label for="floatCorreo">Correo electrónico</label>
                    </div>


                    <div class="form-floating mb-3 position-relative">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                            id="floatPassword" placeholder="Dejar en blanco para no cambiar">
                        <label for="floatPassword">Nueva contraseña</label>
                        
                        <button type="button" id="togglePassword" 
                                class="btn-toggle-password position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent me-3">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
<script src="{{ asset('js/usuarios-script.js') }}"></script>
@endsection