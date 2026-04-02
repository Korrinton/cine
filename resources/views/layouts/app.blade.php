<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/usuarios-style.css') }}">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Cine</a>
            
            <div class="navbar-nav ms-auto">
                @auth
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-circle me-2">
                                <span class="initials">{{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}</span>
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
                            <li>
                                <a class="dropdown-item">
                                    <i class="bi bi-person-circle me-2"></i>{{ Str::limit(Auth::user()->nombre, 10, '...') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear me-2"></i>Modificar Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" >
                                    <i class="bi bi-film me-2"></i>Historial de Compras
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Salir
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>                                    
                @endauth

                @guest
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                    <!--
                    <a class="nav-link" href="{{ url('/login') }}">Login</a>
                    -->
                    <a class="nav-link" href="{{ url('/registro') }}">Registro</a>
                @endguest
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content') 
    </div>

    <!--
    <div class="modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-dark text-white text-center py-3">
                    <h4 class="modal-title w-100 mb-0" id="loginModalLabel">Iniciar Sesión</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    {{-- Manejo de Alertas --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @error('error')
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>{{ $message }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror

                    {{-- Tu Formulario --}}
                    <form action="{{ route('login.post') }}" method="POST" class="slow-submit">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" id="floatCorreo" placeholder="nombre@ejemplo.com" value="{{ old('correo') }}">
                            <label for="floatCorreo">Correo electrónico</label>
                            @error('correo')
                                <div class="invalid-feedback">{{ $message }} </div>
                            @enderror                        
                        </div>

                        <div class="form-floating mb-3 position-relative has-validation">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="floatPassword" placeholder="Contraseña">
                            <label for="floatPassword">Contraseña</label>
                            
                            <button type="button" id="togglePassword" class="btn-toggle-password border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-3" style="z-index: 10;" tabindex="-1">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Recuérdame</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Entrar</button>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-center py-3">
                    <small>¿No tienes cuenta? <a href="{{ url('/registro') }}">Regístrate aquí</a></small>
                </div>
            </div>
        </div>
    </div>
    -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--
    <script src="{{ asset('js/usuarios-script.js') }}"></script>
    -->
</body>
</html>