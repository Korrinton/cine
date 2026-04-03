<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    use HasFactory;

    protected $table = 'peliculas';
    protected $primaryKey = 'id_pelicula';

    protected $fillable = [
        'nombre', 'sinopsis', 'duracion', 'genero', 
        'fecha_estreno', 'fecha_final', 'precio', 'poster_url'
    ];

    // Esto convierte las strings de la DB en objetos Carbon (fechas) automáticamente
    protected $casts = [
        'fecha_estreno' => 'date',
        'fecha_final' => 'date',
    ];

    // Query Scope: Para usarlo luego como Pelicula::enCartelera()->get()
    public function scopeEnCartelera($query)
    {
        return $query->where('fecha_estreno', '<=', now())
                     ->where('fecha_final', '>=', now());
    }
}