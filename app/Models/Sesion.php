<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sesion extends Model
{
    protected $table      = 'sesiones';
    protected $primaryKey = 'id_sesion';

    protected $fillable = ['id_evento', 'hora_inicio'];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'id_eventos');
    }
}
