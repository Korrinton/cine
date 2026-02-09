<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [UsuariosController::class, 'mostrarLogin'])->name('login');
    Route::get('/registro', [UsuariosController::class, 'mostrarRegistro'])->name('registro');
    Route::post('/registro', [UsuariosController::class, 'almacenar'])->name('register.store');
    Route::post('/login', [UsuariosController::class, 'acceder'])->name('login.post');
});
    Route::post('/logout', [UsuariosController::class, 'salir'])->name('logout');
    Route::get('/perfil', [UsuariosController::class, 'editar'])->name('perfil.editar');
    Route::put('/perfil', [UsuariosController::class, 'actualizar'])->name('perfil.actualizar');
