<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        //Crear un administrador
        User::create([
            'nombre' => 'Admin',
            'apellidos' => 'Sistema',
            'correo' => 'admin@cine.com',
            'password' => Hash::make('password'),
            'tipo' => 'admin',
        ]);

        //Crear un cliente de prueba
        User::create([
            'nombre' => 'KiKe',
            'apellidos' => 'PG',
            'correo' => 'user@cine.com',
            'password' => Hash::make('password'),
            'tipo' => 'cliente',
        ]);
    }
}