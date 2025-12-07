<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DisciplinaApiController;
use App\Http\Controllers\Api\HorarioAulaController;
use App\Http\Controllers\Api\FrequenciaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// As rotas dentro deste grupo requerem que o usuário esteja autenticado
Route::middleware('auth:sanctum')->group(function () {
    
    // Rotas CRUD API para Disciplinas
    Route::resource('disciplinas', DisciplinaController::class);

    // Rotas CRUD API para Disciplinas (já definidas)
    Route::resource('disciplinas', DisciplinaController::class);

    // Rotas CRUD API ANINHADAS para HorarioAula
    // O recurso 'disciplinas' vem primeiro, garantindo a URL /api/disciplinas/{id}/horarios
    Route::resource('disciplinas.horarios', HorarioAulaController::class)->shallow(); 
    // O método 'shallow()' mantém as rotas 'show', 'update' e 'destroy' com um ID simples.

    // Rotas CRUD API para Frequências
    // Ex: GET /api/disciplinas/1/frequencias (Lista faltas de Matématica)
    // Ex: POST /api/disciplinas/1/frequencias (Registra nova falta)
    Route::resource('disciplinas.frequencias', FrequenciaController::class)->shallow();
});

