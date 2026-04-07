<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\ReservaController;

Route::get('/eventos/crear', [EventoController::class, 'create'])->name('eventos.create');
Route::post('/eventos',     [EventoController::class, 'store'])->name('eventos.store');

Route::get('/peliculas',         [PeliculaController::class, 'index'])->name('peliculas.index');
Route::get('/peliculas/crear',   [PeliculaController::class, 'create'])->name('peliculas.create');
Route::post('/peliculas',        [PeliculaController::class, 'store'])->name('peliculas.store');
Route::delete('/peliculas/{id}', [PeliculaController::class, 'destroy'])->name('peliculas.destroy');

Route::get('/', function () {
    return view('welcome');
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


Route::middleware('auth')->group(function () {
    Route::get('/reservar/{id}', [ReservaController::class, 'index'])->name('reservas.mapa');
    Route::post('/reservar/guardar', [ReservaController::class, 'store'])->name('reservas.store');
    Route::get('/reservas/confirmacion', [EventoController::class, 'confirmacion'])->name('reservas.confirmacion');
});

/*
No funciona no hacer caso
Route::middleware('auth')->group(function () {
    Route::get('/evento/{id}/asientos',  [EventoController::class, 'seleccionar'])->name('reservas.seleccionar');
    Route::post('/evento/{id}/reservar', [EventoController::class, 'reservar'])->name('reservas.reservar');
    Route::get('/reservas/confirmacion', [EventoController::class, 'confirmacion'])->name('reservas.confirmacion');
});
*/