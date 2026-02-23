<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventoController;


Route::get('/eventos/crear', [EventoController::class, 'create'])->name('eventos.create');

Route::get('/', function () {
    return view('welcome');
});
