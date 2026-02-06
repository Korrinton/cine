<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/usuarios-style.css') }}">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Cine</a>
            
            <div class="navbar-nav ms-auto">
                @auth
                    <a href="{{ route('perfil.editar') }}" class="nav-link text-info">
                        <i class="fas fa-user"></i> {{ Auth::user()->nombre }}
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-flex align-items-center">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger ms-2">Salir</button>
                    </form>
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