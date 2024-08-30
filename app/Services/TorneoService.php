<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\Jugador;
use App\Models\Enfrentamiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TorneoService
{
    public function simularTorneo(array $jugadores)
    {
        // Crear un nuevo torneo
        $torneo = Torneo::create(['nombre' => 'Torneo ' . now()]);

        // Registrar los jugadores en la base de datos si no están registrados
        foreach ($jugadores as $jugadorData) {
            Jugador::firstOrCreate([
                'nombre' => $jugadorData['nombre'],
            ], [
                'nivel_habilidad' => $jugadorData['nivel_habilidad'],
                'tiempo_reaccion' => rand(1, 100), // Ejemplo de tiempo de reacción
            ]);
        }

        // Obtener jugadores para la simulación
        $jugadores = Jugador::all()->toArray();

        // Simular el torneo
        while (count($jugadores) > 1) {
            $jugadores = $this->simularRonda($torneo, $jugadores);
        }

        // Guardar el ganador
        $ganador = $jugadores[0];
        
        $torneo->ganador_id = $ganador['id'];
        $torneo->save();

        return $ganador;
    }

    private function simularRonda(Torneo $torneo, array $jugadores)
    {
        $ganadores = [];
        for ($i = 0; $i < count($jugadores); $i += 2) {
            $jugador1 = $jugadores[$i];
            $jugador2 = $jugadores[$i + 1];

            $ganador = $this->enfrentar($jugador1, $jugador2);

            Enfrentamiento::create([
                'torneo_id' => $torneo->id,
                'jugador1_id' => $jugador1['id'],
                'jugador2_id' => $jugador2['id'],
                'ganador_id' => $ganador['id'],
            ]);

            $ganadores[] = $ganador;
        }

        return $ganadores;
    }

    private function enfrentar(array $jugador1, array $jugador2)
    {
        // Lógica para determinar el ganador basado en habilidad, suerte, y tiempo de reacción
        $probabilidad1 = $jugador1['nivel_habilidad'] + rand(0, 100) - $jugador1['tiempo_reaccion'];
        $probabilidad2 = $jugador2['nivel_habilidad'] + rand(0, 100) - $jugador2['tiempo_reaccion'];

        return $probabilidad1 >= $probabilidad2 ? $jugador1 : $jugador2;
    }
}