<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ReservaController;

Route::get('/eventos/crear', [EventoController::class, 'create'])->name('eventos.create');

Route::get('/', function () {
    return view('welcome');
});





// Sin middleware auth por ahora
Route::get('/evento/{id}/asientos', [ReservaController::class, 'seleccionar'])->name('reservas.seleccionar');
Route::post('/evento/{id}/reservar', [ReservaController::class, 'reservar'])->name('reservas.reservar');
Route::get('/reservas/confirmacion', [ReservaController::class, 'confirmacion'])->name('reservas.confirmacion');
