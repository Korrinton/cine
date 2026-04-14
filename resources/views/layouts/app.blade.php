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
                            @if(Auth::user()->isAdmin())
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-shield-lock me-2"></i>Panel Admin
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('perfil.editar') }}">
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
                    <a class="nav-link" href="{{ url('/login') }}">Login</a>
                    <a class="nav-link" href="{{ url('/registro') }}">Registro</a>
                @endguest
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content') 
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>