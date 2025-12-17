<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisciplinaController;
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

    // ✅ CORREÇÃO: O Dashboard agora aponta para o seu Controller
    // Isso vai listar as disciplinas ao logar, em vez de mostrar uma tela vazia
    Route::get('/dashboard', [DisciplinaController::class, 'index'])->name('dashboard');

    // Rotas de Perfil (Necessárias para o funcionamento do Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Carrega as rotas de autenticação
require __DIR__.'/auth.php';