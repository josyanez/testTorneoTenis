<?php

namespace Database\Factories;

use App\Models\Torneo;
use Illuminate\Database\Eloquent\Factories\Factory;

class TorneoFactory extends Factory
{
    protected $model = Torneo::class;

    public function definition()
    {
        return [
            'nombre' => 'Torneo ' . now(),
            'ganador_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}