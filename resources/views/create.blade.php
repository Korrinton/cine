<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Evento</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        select, input { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background: #333; color: white; border: none; cursor: pointer; }
        .alert { padding: 10px; background: #d4edda; color: #155724; margin-bottom: 15px; }
        .error { color: red; font-size: 0.9em; }
    </style>
</head>
<body>

    <h2>Crear Nuevo Evento de Proyección</h2>

    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('eventos.store') }}" method="POST">
        @csrf <div class="form-group">
            <label for="id_pelicula">Película:</label>
            <select name="id_pelicula" id="id_pelicula" required>
                <option value="">Selecciona una película...</option>
                @foreach($peliculas as $pelicula)
                    <option value="{{ $pelicula->id_pelicula }}">{{ $pelicula->nombre }}</option>
                @endforeach
            </select>
            @error('id_pelicula') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="id_sala">Sala:</label>
            <select name="id_sala" id="id_sala" required>
                <option value="">Selecciona una sala...</option>
                @foreach($salas as $sala)
                    <option value="{{ $sala->id_sala }}">{{ $sala->nombre }} (Aforo: {{ $sala->aforo }})</option>
                @endforeach
            </select>
            @error('id_sala') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="horarios">Día y Hora de Proyección:</label>
            <input type="datetime-local" name="horarios" id="horarios" required>
            @error('horarios') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Crear Evento</button>
    </form>

</body>
</html>