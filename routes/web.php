<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\GradeHorariaController;
use App\Http\Controllers\FrequenciaController;
use App\Http\Controllers\IntroController;
use App\Services\CalendarioService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rota Pública
Route::get('/', function () {
    return view('welcome');
});

// --- GRUPO PROTEGIDO (Autenticação Necessária) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // =========================================================================
    // 1. DASHBOARD & ONBOARDING (Lógica Principal)
    // =========================================================================

    // Rota Dashboard com verificação de Onboarding
    Route::get('/dashboard', function () {
        $user = Auth::user();

        // Se o usuário é novo e não viu a intro, redireciona
        if (!$user->has_seen_intro) {
            return redirect()->route('intro');
        }

        // Carrega dados para a tela
        $disciplinas = $user->disciplinas()->with('frequencias')->get();
        return view('dashboard', compact('disciplinas'));
    })->name('dashboard');

    // Tela de Introdução (Boas-vindas)
    Route::get('/intro', function () {
        if (Auth::user()->has_seen_intro) {
            return redirect()->route('dashboard');
        }
        return view('intro.index');
    })->name('intro');

    // Ação do botão "Começar" (Marca intro como vista)
    Route::post('/intro/finish', function (Request $request) {
        $request->user()->update(['has_seen_intro' => true]);
        return redirect()->route('dashboard');
    })->name('intro.finish');

    // API para marcar o Tour do Dashboard como concluído
    Route::post('/tour/finish', function (Request $request) {
        $request->user()->update(['has_completed_tour' => true]);
        return response()->json(['success' => true]);
    })->name('tour.finish');


    // =========================================================================
    // 2. DISCIPLINAS (CRUD)
    // =========================================================================
    
    Route::get('/disciplinas', [DisciplinaController::class, 'index'])->name('disciplinas'); // Lista (opcional se usar dashboard)
    Route::get('/disciplinas/criar', [DisciplinaController::class, 'criar'])->name('disciplinas.criar'); // Form Criar
    Route::post('/disciplinas', [DisciplinaController::class, 'store'])->name('disciplinas.store'); // Salvar
    
    Route::get('/disciplinas/{id}/editar', [DisciplinaController::class, 'edit'])->name('disciplinas.edit'); // Form Editar
    Route::put('/disciplinas/{id}', [DisciplinaController::class, 'update'])->name('disciplinas.update'); // Atualizar
    Route::delete('/disciplinas/{id}', [DisciplinaController::class, 'destroy'])->name('disciplinas.destroy'); // Excluir


    // =========================================================================
    // 3. GRADE HORÁRIA
    // =========================================================================

    // Grade Geral (Visão da semana toda)
    Route::get('/grade', [GradeHorariaController::class, 'geral'])->name('grade.geral');

    // Configuração Específica de uma Disciplina (Lista e Adicionar)
    // Nota: Usamos {id} para o ID da disciplina aqui
    Route::get('/disciplinas/{id}/horarios', [GradeHorariaController::class, 'index'])->name('grade.index');
    Route::post('/disciplinas/{id}/horarios', [GradeHorariaController::class, 'store'])->name('grade.store');

    // Editar Horário Específico
    // Nota: Usamos {id} para o ID do horário (GradeHoraria) aqui
    Route::get('/grade/{id}/editar', [GradeHorariaController::class, 'edit'])->name('grade.edit');
    Route::put('/grade/{id}', [GradeHorariaController::class, 'update'])->name('grade.update');
    Route::delete('/grade/{id}', [GradeHorariaController::class, 'destroy'])->name('grade.destroy');


    // =========================================================================
    // 4. FREQUÊNCIA (API & Ações)
    // =========================================================================

    // Busca aulas do dia (usado no Modal de Chamada)
    Route::get('/api/buscar-aulas', [FrequenciaController::class, 'buscarPorData']);
    
    // Salva a chamada do dia (usado no Modal de Chamada)
    Route::post('/api/registrar-chamada', [FrequenciaController::class, 'registrarLote']);

    // Registrar falta rápida (botão + do card, se existir)
    Route::post('/api/frequencia/{id}/falta', [FrequenciaController::class, 'registrarFalta'])->name('api.frequencia.falta');


    // =========================================================================
    // 5. PERFIL DO USUÁRIO
    // =========================================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota para EXIBIR o Wizard
    Route::get('/intro', [IntroController::class, 'index'])->name('intro')
        ->middleware('auth');

    // Rota para SALVAR os dados e finalizar
    Route::post('/intro', [IntroController::class, 'store'])->name('intro.store')
        ->middleware('auth');

});

// Carrega rotas de autenticação (Login, Register, etc)
require __DIR__.'/auth.php';