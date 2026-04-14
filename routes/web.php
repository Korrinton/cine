<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\EventoController;

Route::get('/', function () {
    $eventos = \App\Models\Evento::with(['pelicula', 'sala'])
        ->orderBy('fecha_estreno')
        ->get();
    return view('welcome', compact('eventos'));
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [UsuariosController::class, 'mostrarLogin'])->name('login');
    Route::get('/registro', [UsuariosController::class, 'mostrarRegistro'])->name('registro');
    Route::post('/registro', [UsuariosController::class, 'almacenar'])->name('register.store');
    Route::post('/login', [UsuariosController::class, 'acceder'])->name('login.post');
});

Route::post('/logout', [UsuariosController::class, 'salir'])->name('logout')->middleware('auth');
Route::get('/perfil', [UsuariosController::class, 'editar'])->name('perfil.editar')->middleware('auth');
Route::put('/perfil', [UsuariosController::class, 'actualizar'])->name('perfil.actualizar')->middleware('auth');

Route::get('/reservar/{id_evento}', [ReservaController::class, 'index'])->name('reservas.mapa')->middleware('auth');
Route::post('/reservar', [ReservaController::class, 'store'])->name('reservas.store')->middleware('auth');

// Películas
Route::get('/peliculas',         [PeliculaController::class, 'index'])->name('peliculas.index');
Route::get('/peliculas/crear',   [PeliculaController::class, 'create'])->name('peliculas.create');
Route::post('/peliculas',        [PeliculaController::class, 'store'])->name('peliculas.store');
Route::delete('/peliculas/{id}', [PeliculaController::class, 'destroy'])->name('peliculas.destroy');

// Eventos
Route::get('/eventos/crear',  [EventoController::class, 'create'])->name('eventos.create');
Route::post('/eventos',       [EventoController::class, 'store'])->name('eventos.store');