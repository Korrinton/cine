<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_evento',
        'id_usuario',
        'fila',
        'asiento',
        'fecha_reserva',
        'fecha_sesion',
        'hora_sesion',
    ];
}