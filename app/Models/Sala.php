<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    protected $table = 'salas';
    protected $primaryKey = 'id_sala';

    protected $fillable = [
        'nombre',
        'aforo',
        'filas',
        'sillas',
    ];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'id_sala', 'id_sala');
    }
}
