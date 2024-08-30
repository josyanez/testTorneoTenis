<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Torneo;

class TorneoControllerTest extends TestCase
{
    /** @test */
    public function testConsultarTorneosFinalizados()
    {
        // Crear un torneo utilizando la factory
        $torneo = Torneo::factory()->create();

        // Realizar la solicitud a la ruta correspondiente
        $response = $this->getJson('/api/torneos');

        // Verificar que la respuesta sea exitosa y contenga los datos esperados
        $response->assertStatus(200)
                ->assertJsonStructure([
                    '*' => [
                        'id',
                        'nombre',
                        'ganador_id',
                        'created_at',
                        'updated_at',
                        'ganador'
                    ]
                ]);
    }

    /** @test */
    public function consultar_torneos_por_fecha()
    {
        // Preparar datos de prueba
        $fecha = '2024-08-29T00:00:00.000000Z';
        $torneo = Torneo::factory()->create([
            'created_at' => $fecha,
            'ganador_id' => 1
        ]);

        // Realizar la solicitud a la ruta con el parámetro de fecha
        $response = $this->getJson('/api/torneosPorFecha?fecha=' . $fecha);

        // Verificar que la respuesta sea exitosa y contenga los datos esperados
        $response->assertStatus(200)
                ->assertJsonFragment([
                    'id' => $torneo->id,
                    'created_at' => $fecha
                ]);
    }

}
