<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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