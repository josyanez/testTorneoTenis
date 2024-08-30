<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    protected $table = 'jugadores';
    protected $fillable = ['nombre', 'nivel_habilidad', 'tiempo_reaccion'];

    public function torneosGanados()
    {
        return $this->hasMany(Torneo::class, 'ganador_id');
    }
}