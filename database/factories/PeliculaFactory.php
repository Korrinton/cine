<?php

namespace Database\Factories;

use App\Models\Pelicula;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pelicula>
 */
class PeliculaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'nombre' => $this->faker->sentence(3),
        'sinopsis' => $this->faker->paragraph(),
        'duracion' => $this->faker->numberBetween(90, 180),
        'genero' => $this->faker->randomElement(['Acción', 'Comedia', 'Drama', 'Ciencia Ficción']),
        'fecha_estreno' => now()->subDays(15),
        'fecha_final' => now()->addDays(15),
        'precio' => $this->faker->numberBetween(5, 12),
        'poster_url' => 'https://picsum.photos/seed/' . rand(1, 1000) . '/300/450',
    ];
    }
}
