<?php

namespace Database\Seeders;

//use App\Models\User;
use App\Models\Usuario;
use App\Models\Pelicula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {      
        $this->call([
            UsuariosSeeder::class,
        ]);

        Pelicula::factory(10)->create();
    }
}
