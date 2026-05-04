<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoExtra extends Model
{
    protected $table    = 'ingresos_extra';
    protected $fillable = ['nombre', 'importe', 'anio'];
}
