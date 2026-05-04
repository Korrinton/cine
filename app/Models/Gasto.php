<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = ['nombre', 'tipo', 'importe', 'anio', 'id_pelicula'];

    public function pelicula()
    {
        return $this->belongsTo(Pelicula::class, 'id_pelicula', 'id_pelicula');
    }
}
