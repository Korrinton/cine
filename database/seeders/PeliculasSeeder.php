<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelicula;

class PeliculasSeeder extends Seeder
{
    public function run(): void
    {
        Pelicula::create([
            'titulo'      => 'El Señor de los Anillos: La Comunidad del Anillo',
            'descripcion' => 'El hobbit Frodo Bolsón hereda el Anillo Único y debe emprender un viaje para destruirlo en el Monte del Destino antes de que caiga en manos del oscuro señor Sauron.',
            'duracion'    => 178,
            'genero'      => 'Fantasía',
            'imagen'      => 'peliculas/Bq7QP437p8FyX5cYCYM7ooLYBBBxtkUJnyDkN2Tr.webp',
        ]);
    }
}
