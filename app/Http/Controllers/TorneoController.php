<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Torneo; 
use App\Services\TorneoService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
* @OA\Info(
*             title="Torneo de tenis", 
*             version="1.0",
*             description="Test Desarrollador backend"
* )
*
* @OA\Server(url="http://localhost:8000/")
*/

class TorneoController extends Controller
{

    /**
     * Crear un nuevo torneo
     * 
     * @OA\Post(
     *     path="/api/torneos/crear",
     *     tags={"Torneo"},
     *     summary="Crear un nuevo torneo",
     *     description="Crea un torneo de eliminación directa con los jugadores proporcionados",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "jugadores": {
     *                     {"nombre": "Maria", "nivel_habilidad": 90, "tiempo_reaccion": 20},
     *                     {"nombre": "Laura", "nivel_habilidad": 90, "tiempo_reaccion": 25},
     *                     {"nombre": "Adriana", "nivel_habilidad": 90, "tiempo_reaccion": 20},
     *                     {"nombre": "Ana", "nivel_habilidad": 60, "tiempo_reaccion": 22}
     *                 }
     *             },
     *             @OA\Property(
     *                 property="jugadores",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="nombre", type="string", example="maria"),
     *                     @OA\Property(property="nivel_habilidad", type="integer", example=90),
     *                     @OA\Property(property="tiempo_reaccion", type="integer", example=20),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Torneo creado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Torneo creado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en los datos enviados",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Datos inválidos")
     *         )
     *     )
     * )
     */
    public function crearTorneo(Request $request)
    {
        $jugadoresData = $request->input('jugadores');

        // Crear el torneo
        $torneo = Torneo::create(['nombre' => 'Torneo ' . now()]);

        // Insertar jugadores
        $jugadores = collect($jugadoresData)->map(function($jugadorData) {
            return Jugador::create($jugadorData);
        });

        // Simular el torneo
        $ganador = $this->simularTorneo($jugadores, $torneo);

        // Guardar el ganador en el torneo
        $torneo->update(['ganador_id' => $ganador->id]);

        return response()->json(['ganador' => $ganador->nombre]);
    }

    private function simularTorneo($jugadores, $torneo)
    {
        while ($jugadores->count() > 1) {
            // Mezclar jugadores antes de cada ronda
            $jugadores = $jugadores->shuffle();
            
            // Dividir los jugadores en enfrentamientos
            $enfrentamientos = $jugadores->chunk(2);
            $ganadores = collect();
    
            if ($enfrentamientos->isEmpty()) {
                dd('No hay enfrentamientos disponibles.');
            }
    
            // Procesar cada enfrentamiento
            foreach ($enfrentamientos as $enfrentamiento) {
                $cont = 1;
                $jugador1 = null;
                $jugador2 = null;
                
                foreach ($enfrentamiento as $datosJugador) {
                    if ($cont == 1){
                        $jugador1 = $datosJugador;
                    } else {
                        $jugador2 = $datosJugador;
                    }
                    $cont++;
                }
    
                if ($jugador1 && $jugador2) {
                    $ganador = $this->determinarGanador($jugador1, $jugador2);
                    // Registrar el enfrentamiento en el torneo
                    $torneo->enfrentamientos()->create([
                        'jugador1_id' => $jugador1->id,
                        'jugador2_id' => $jugador2->id,
                        'ganador_id' => $ganador->id,
                    ]);
                    // Añadir el ganador a la colección de ganadores
                    $ganadores->push($ganador);
                }
            }
    
            // Verificar que hay ganadores para la próxima ronda
            if ($ganadores->isEmpty()) {
                throw new \Exception('No se pudo determinar un ganador.');
            }
    
            // Asignar los ganadores para la siguiente ronda
            $jugadores = $ganadores;
        }
        return $jugadores->first();
    }
    
    private function determinarGanador($jugador1, $jugador2)
    {
        // Habilidad, suerte, y tiempo de reacción afectan el resultado
        $factorSuerte1 = rand(0, 10) / 100;
        $factorSuerte2 = rand(0, 10) / 100;
    
        $valor1 = ($jugador1->nivel_habilidad - $jugador1->tiempo_reaccion) * (1 + $factorSuerte1);
        $valor2 = ($jugador2->nivel_habilidad - $jugador2->tiempo_reaccion) * (1 + $factorSuerte2);
    
        return $valor1 > $valor2 ? $jugador1 : $jugador2;
    }

    /**
     * Obtener la lista de torneos
     * 
     * @OA\Get(
     *     path="/api/torneos",
     *     tags={"Torneo"},
     *     summary="Obtener todos los torneos",
     *     description="Devuelve una lista de todos los torneos creados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de torneos obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Torneo de Verano"),
     *                 @OA\Property(property="ganador", type="string", example="Pedro"),
     *                 @OA\Property(property="fecha", type="string", example="2024-08-29"),
     *                 @OA\Property(property="created_at", type="string", example="2024-08-28T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-08-29T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron torneos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="No hay torneos disponibles")
     *         )
     *     )
     * )
     */

    public function consultarTorneosFinalizados()
    {
        $torneos = Torneo::with('ganador')->get();
        return response()->json($torneos);
    }

    /**
     * Consultar torneos finalizados por fecha
     * 
     * @OA\Get(
     *     path="/api/torneosPorFecha",
     *     tags={"Torneo"},
     *     summary="Consultar torneos finalizados por fecha",
     *     description="Devuelve una lista de torneos finalizados en la fecha especificada.",
     *     @OA\Parameter(
     *         name="fecha",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date", example="2024-08-28"),
     *         description="La fecha en la que se crearon los torneos que se desean consultar."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="number", example=1),
     *                  @OA\Property(property="nombre", type="string", example="Torneo 2024-08-28 10:00:00"),
     *                  @OA\Property(property="ganador_id", type="number", example=493),
     *                  @OA\Property(property="created_at", type="string", example="2024-08-28T10:00:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-08-28T10:30:00.000000Z"),
     *                  @OA\Property(
     *                      property="ganador",
     *                      type="object",
     *                      @OA\Property(property="id", type="number", example=493),
     *                      @OA\Property(property="nombre", type="string", example="maria"),
     *                      @OA\Property(property="nivel_habilidad", type="integer", example=90),
     *                      @OA\Property(property="tiempo_reaccion", type="integer", example=20),
     *                      @OA\Property(property="created_at", type="string", example="2024-08-28T10:00:00.000000Z"),
     *                      @OA\Property(property="updated_at", type="string", example="2024-08-28T10:30:00.000000Z")
     *                  )
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="NOT FOUND",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No se encontraron torneos finalizados en la fecha especificada.")
     *         )
     *     )
     * )
     */
    public function consultarTorneosPorFecha(Request $request)
    {
        $fecha = $request->input('fecha');
        $torneos = Torneo::with('ganador')
        ->whereDate('created_at', $fecha)
        ->get();
    
        if ($torneos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron torneos finalizados en la fecha especificada.'], 404);
        }
    
        return response()->json($torneos);
    }
}
