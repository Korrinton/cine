<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ValidarController;
use App\Http\Controllers\RecaudacionController;

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

// Validación de entradas (solo admin)
Route::get('/validar/{token}', [ValidarController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('validar.entrada');

// Pagos
Route::middleware('auth')->group(function () {
    Route::post('/pagar/checkout', [PagoController::class, 'checkout'])->name('pago.checkout');
    Route::get('/pagar/exito',     [PagoController::class, 'exito'])->name('pago.exito');
    Route::get('/pagar/cancelar',  [PagoController::class, 'cancelar'])->name('pago.cancelar');
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
    Route::get('/eventos/{id}/editar', [AdminController::class, 'eventoEditar'])->name('eventos.editar');
    Route::put('/eventos/{id}',        [AdminController::class, 'eventoActualizar'])->name('eventos.actualizar');
    Route::delete('/eventos/{id}',     [AdminController::class, 'eventoEliminar'])->name('eventos.eliminar');

    // Películas
    Route::get('/peliculas',           [AdminController::class, 'peliculas'])->name('peliculas');
    Route::post('/peliculas',          [AdminController::class, 'peliculaGuardar'])->name('peliculas.guardar');
    Route::delete('/peliculas/{id}',   [AdminController::class, 'peliculaEliminar'])->name('peliculas.eliminar');

    // Recaudación y gastos
    Route::get('/recaudacion',            [RecaudacionController::class, 'index'])->name('recaudacion');
    Route::post('/gastos',                [RecaudacionController::class, 'gastoGuardar'])->name('gastos.guardar');
    Route::delete('/gastos/{id}',         [RecaudacionController::class, 'gastoEliminar'])->name('gastos.eliminar');
    Route::post('/ingresos-extra',        [RecaudacionController::class, 'ingresoGuardar'])->name('ingresos.guardar');
    Route::delete('/ingresos-extra/{id}', [RecaudacionController::class, 'ingresoEliminar'])->name('ingresos.eliminar');
});
