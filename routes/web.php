<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\GradeHorariaController;
use App\Http\Controllers\GradeGeralController;
use App\Http\Controllers\FrequenciaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui são registradas as rotas da aplicação.
|
*/

// Rota Pública
Route::get('/', function () {
    return view('welcome');
});

// --- GRUPO PROTEGIDO (Autenticação Necessária) ---
Route::middleware(['auth', 'verified'])->group(function () {

// --- ROTAS DE GRADE HORÁRIA ---

// Tela de configuração de horários (Ex: /disciplinas/1/horarios)
    Route::get('/disciplinas/{id}/horarios', [GradeHorariaController::class, 'index'])->name('grade.index');
    
    // Salvar horário
    Route::post('/disciplinas/{id}/horarios', [GradeHorariaController::class, 'store'])->name('grade.store');
    
    // Apagar horário
    Route::delete('/grade/{id}', [GradeHorariaController::class, 'destroy'])->name('grade.destroy');

    // Rota da Grade Geral (Visão Completa)
    Route::get('/grade-geral', [GradeGeralController::class, 'index'])->name('grade.geral');

// --- ROTAS DE DISCIPLINAS ---
    Route::get('/disciplinas', [DisciplinaController::class, 'index'])->name('disciplinas');

    // Isso vai listar as disciplinas ao logar, em vez de mostrar uma tela vazia
    Route::get('/dashboard', [DisciplinaController::class, 'index'])->name('dashboard');

    // Rota para MOSTRAR o formulário
    Route::get('/disciplinas/criar', [DisciplinaController::class, 'create'])->name('disciplinas.criar');

    Route::post('/disciplinas', [DisciplinaController::class, 'store'])->name('disciplinas.store');

    // Rota API para registrar falta (Usada pelo botão + do Dashboard)
    Route::post('/api/frequencia/{id}/falta', [FrequenciaController::class, 'registrarFalta'])
        ->name('api.frequencia.falta');

    // Rotas de Perfil (Necessárias para o funcionamento do Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Carrega as rotas de autenticação
require __DIR__.'/auth.php';