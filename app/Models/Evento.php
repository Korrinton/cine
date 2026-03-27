<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $table = 'eventos';
    
    protected $primaryKey = 'id_evento'; 

    //fillable para asignación masiva
    protected $fillable = [
        'nombre',
        'id_pelicula',
        'id_sala',
        'horarios',
        'asientos_disponibles',
        'precio',
        'fecha_estreno',
        'fecha_final',
        
    ];
    //casts para convertir los campos a tipos específicos
    protected $casts = [
        'asientos_disponibles' => 'array',
        'horarios' => 'datetime'
    ];
    //relaciones

    // Un evento pertenece a una sala
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class, 'id_sala', 'id_sala');
    }
    // Un evento pertenece a una película
    public function pelicula(): BelongsTo
    {
        return $this->belongsTo(Pelicula::class, 'id_pelicula', 'id_pelicula');
    }
    // Un evento tiene muchas reservas
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'id_evento', 'id_evento');
    }
}