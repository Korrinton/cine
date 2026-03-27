<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'id_sala', 'id_sala');
    }

    public function cache()
    {
        return $this->hasOne(Cache::class, 'id_sala', 'id_sala');
    }
}