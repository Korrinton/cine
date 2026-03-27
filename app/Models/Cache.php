<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    protected $table = 'cache';
    protected $primaryKey = 'id_cache';

    protected $fillable = ['id_sala', 'sillas'];

    protected $casts = [
        'sillas' => 'array',
    ];

    public function sala()
    {
        return $this->belongsTo(Sala::class, 'id_sala', 'id_sala');
    }
}