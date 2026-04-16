<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\AdminController;

Route::get('/eventos/crear', [EventoController::class, 'create'])->name('eventos.create');
Route::post('/eventos',     [EventoController::class, 'store'])->name('eventos.store');

Route::get('/peliculas',         [PeliculaController::class, 'index'])->name('peliculas.index');
Route::get('/peliculas/crear',   [PeliculaController::class, 'create'])->name('peliculas.create');
Route::post('/peliculas',        [PeliculaController::class, 'store'])->name('peliculas.store');
Route::delete('/peliculas/{id}', [PeliculaController::class, 'destroy'])->name('peliculas.destroy');

Route::get('/', function () {
    $eventos = \App\Models\Evento::with(['pelicula', 'sala'])
        ->orderBy('fecha_estreno')
        ->get();
    return view('welcome', compact('eventos'));
});

//rutas para usuarios no autenticados
Route::controller(UserController::class)->middleware('guest')->group(function () {
    Route::get('/registro', 'create')->name('register');
    Route::post('/registro', 'store')->name('register.store');
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'authenticate')->name('login.post');
});

//rutas para usuarios autenticados
Route::controller(UserController::class)->middleware('auth')->group(function () {
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/perfil', 'edit')->name('profile.edit');
    Route::put('/perfil', 'update')->name('profile.update');
});

Route::get('/reservar/{id_evento}', [ReservaController::class, 'index'])->name('reservas.mapa')->middleware('auth');
Route::post('/reservar', [ReservaController::class, 'store'])->name('reservas.store')->middleware('auth');

// Películas
Route::get('/peliculas',         [PeliculaController::class, 'index'])->name('peliculas.index');
Route::get('/peliculas/crear',   [PeliculaController::class, 'create'])->name('peliculas.create');
Route::post('/peliculas',        [PeliculaController::class, 'store'])->name('peliculas.store');
Route::delete('/peliculas/{id}', [PeliculaController::class, 'destroy'])->name('peliculas.destroy');

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                  [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/salas',             [AdminController::class, 'salas'])->name('salas');
    Route::get('/salas/crear',       [AdminController::class, 'salaCrear'])->name('salas.crear');
    Route::post('/salas',            [AdminController::class, 'salaGuardar'])->name('salas.guardar');
    Route::delete('/salas/{id}',     [AdminController::class, 'salaEliminar'])->name('salas.eliminar');
    Route::get('/eventos',           [AdminController::class, 'eventos'])->name('eventos');
    Route::get('/eventos/crear',     [AdminController::class, 'eventoCrear'])->name('eventos.crear');
    Route::post('/eventos',          [AdminController::class, 'eventoGuardar'])->name('eventos.guardar');
    Route::delete('/eventos/{id}',   [AdminController::class, 'eventoEliminar'])->name('eventos.eliminar');
});

// Salas
Route::get('/salas',         [SalaController::class, 'index'])->name('salas.index');
Route::get('/sala/crear',    [SalaController::class, 'create'])->name('salas.create');
Route::post('/salas',        [SalaController::class, 'store'])->name('salas.store');
Route::delete('/salas/{id}', [SalaController::class, 'destroy'])->name('salas.destroy');

// Eventos
Route::get('/eventos/crear',  [EventoController::class, 'create'])->name('eventos.create');
Route::post('/eventos',       [EventoController::class, 'store'])->name('eventos.store');