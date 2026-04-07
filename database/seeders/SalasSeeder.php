<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sala;

class SalasSeeder extends Seeder
{
    public function run(): void
    {
        $salas = [
            [
                'nombre' => 'Sala 1',
                'filas'  => 10,
                'sillas' => 10,
                'aforo'  => 100,
            ],
            [
                'nombre' => 'Sala 2',
                'filas'  => 8,
                'sillas' => 12,
                'aforo'  => 96,
            ],
            [
                'nombre' => 'Sala 3',
                'filas'  => 6,
                'sillas' => 8,
                'aforo'  => 48,
            ],
        ];

        foreach ($salas as $data) {
            Sala::create($data);
        }
    }
}
