@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0">Crear Cuenta</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ url('/registro') }}" method="POST">
                    @csrf 
                    <div class="form-floating mb-3">
                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" 
                            id="floatCorreo" placeholder="nombre@ejemplo.com" value="{{ old('correo') }}">
                        <label for="floatCorreo">Dirección de correo</label>
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                            id="floatPassword" placeholder="Contraseña">
                        <label for="floatPassword">Contraseña</label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="nombre" class="form-control" id="floatNombre" placeholder="Nombre" value="{{ old('nombre') }}">
                        <label for="floatNombre">Nombre</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="apellidos" class="form-control" id="floatApellidos" placeholder="Apellidos" value="{{ old('apellidos') }}">
                        <label for="floatApellidos">Apellidos</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <small>¿Ya tienes cuenta? <a href="{{ url('/login') }}">Inicia sesión</a></small>
            </div>
        </div>
    </div>
</div>
@endsection