<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    protected $fillable = ['nombre', 'ganador_id'];
    use HasFactory;
    public function enfrentamientos()
    {
        return $this->hasMany(Enfrentamiento::class);
    }

    public function ganador()
    {
        return $this->belongsTo(Jugador::class, 'ganador_id');
    }
}