<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\AdminController;

// Inicio
Route::get('/', function () {
    $eventos = \App\Models\Evento::with(['pelicula', 'sala', 'sesiones'])
        ->orderBy('fecha_estreno')
        ->get();
    return view('welcome', compact('eventos'));
});

// Usuarios no autenticados
Route::controller(UsuariosController::class)->middleware('guest')->group(function () {
    Route::get('/registro', 'mostrarRegistro')->name('register');
    Route::post('/registro', 'almacenar')->name('register.store');
    Route::get('/login', 'mostrarLogin')->name('login');
    Route::post('/login', 'acceder')->name('login.post');
});

// Usuarios autenticados
Route::controller(UsuariosController::class)->middleware('auth')->group(function () {
    Route::post('/logout', 'salir')->name('logout');
    Route::get('/perfil', 'editar')->name('profile.edit');
    Route::put('/perfil', 'actualizar')->name('profile.update');
    Route::get('/historial', 'historial')->name('historial');
});

// Reservas
Route::middleware('auth')->group(function () {
    Route::get('/reservar/{id_evento}',      [ReservaController::class, 'index'])->name('reservas.mapa');
    Route::post('/reservar/confirmacion',    [ReservaController::class, 'confirmacion'])->name('reservas.confirmacion');
    Route::post('/reservar',                 [ReservaController::class, 'store'])->name('reservas.store');
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                    [AdminController::class, 'dashboard'])->name('dashboard');

    // Salas
    Route::get('/salas',               [AdminController::class, 'salas'])->name('salas');
    Route::get('/salas/crear',         [AdminController::class, 'salaCrear'])->name('salas.crear');
    Route::post('/salas',              [AdminController::class, 'salaGuardar'])->name('salas.guardar');
    Route::delete('/salas/{id}',       [AdminController::class, 'salaEliminar'])->name('salas.eliminar');

    // Eventos
    Route::get('/eventos',             [AdminController::class, 'eventos'])->name('eventos');
    Route::get('/eventos/crear',       [AdminController::class, 'eventoCrear'])->name('eventos.crear');
    Route::post('/eventos',            [AdminController::class, 'eventoGuardar'])->name('eventos.guardar');
    Route::delete('/eventos/{id}',     [AdminController::class, 'eventoEliminar'])->name('eventos.eliminar');

    // Películas
    Route::get('/peliculas',           [AdminController::class, 'peliculas'])->name('peliculas');
    Route::post('/peliculas',          [AdminController::class, 'peliculaGuardar'])->name('peliculas.guardar');
    Route::delete('/peliculas/{id}',   [AdminController::class, 'peliculaEliminar'])->name('peliculas.eliminar');

    // Recaudación y gastos
    Route::get('/recaudacion',         [AdminController::class, 'recaudacion'])->name('recaudacion');
    Route::post('/gastos',                [AdminController::class, 'gastoGuardar'])->name('gastos.guardar');
    Route::delete('/gastos/{id}',         [AdminController::class, 'gastoEliminar'])->name('gastos.eliminar');
    Route::post('/ingresos-extra',        [AdminController::class, 'ingresoGuardar'])->name('ingresos.guardar');
    Route::delete('/ingresos-extra/{id}', [AdminController::class, 'ingresoEliminar'])->name('ingresos.eliminar');
});
