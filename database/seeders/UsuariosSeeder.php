<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario; 
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        //Crear un administrador
        Usuario::create([
            'nombre' => 'Admin',
            'apellidos' => 'Sistema',
            'correo' => 'admin@admin.com',
            'password' => Hash::make('Admin.123'), // Siempre usa Hash::make
            'tipo' => 'admin',
        ]);

        //Crear un cliente de prueba
        Usuario::create([
            'nombre' => 'KiKe',
            'apellidos' => 'PG',
            'correo' => 'kike@kike.com',
            'password' => Hash::make('Kike.123'),
            'tipo' => 'cliente',
        ]);
    }
}