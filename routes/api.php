<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorneoController;

Route::post('/torneos/crear', [TorneoController::class, 'crearTorneo']);
Route::get('/torneos', [TorneoController::class, 'consultarTorneosFinalizados']);
Route::get('/torneosPorFecha', [TorneoController::class, 'consultarTorneosPorFecha']);