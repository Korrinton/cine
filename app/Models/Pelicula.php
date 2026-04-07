<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelicula extends Model
{
    protected $table = 'peliculas';
    protected $primaryKey = 'id_pelicula';

    protected $fillable = [
        'titulo',
        'descripcion',
        'duracion',
        'genero',
        'imagen',
    ];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'id_pelicula', 'id_pelicula');
    }
}
