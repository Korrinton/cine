@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0">Iniciar Sesión</h4>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        {{ session('success') }}
                    </div>
                @endif
                @error('error')
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        <div>
                            {{ $message }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" id="floatCorreo" placeholder="nombre@ejemplo.com" value="{{ old('correo') }}">
                        <label for="floatCorreo">Dirección de correo</label>
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="floatPassword" placeholder="Contraseña">
                        <label for="floatPassword">Contraseña</label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <small>¿No tienes cuenta? <a href="{{ url('/registro') }}">Regístrate aquí</a></small>
            </div>
        </div>
    </div>
</div>
@endsection