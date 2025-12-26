<?php

use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\GradeHorariaController;
use App\Http\Controllers\FrequenciaController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\EventoController;
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
    // 1. DASHBOARD & ONBOARDING
    // =========================================================================

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    Route::get('/intro/datas', [IntroController::class, 'stepDatas'])->name('intro.step_datas');
    Route::post('/intro/datas', [IntroController::class, 'storeDatas'])->name('intro.store_datas');

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

    // Histórico
    Route::get('/historico', [FrequenciaController::class, 'historico'])->name('frequencia.historico');


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

    // EVENTOS / DIAS LIVRES (CRUD COMPLETO)
    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/{evento}/editar', [EventoController::class, 'edit'])->name('eventos.edit');
    Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
    Route::put('/eventos/{evento}', [EventoController::class, 'update'])->name('eventos.update');
    Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');

    // =========================================================================
    // 6. RELATÓRIOS (PDF)
    // =========================================================================
    Route::get('/relatorio/baixar', [RelatorioController::class, 'gerarRelatorio'])
        ->name('relatorio.baixar');

    // =========================================================================
    // 7. PWA
    // =========================================================================
    Route::get('/offline', function () {
        return view('offline'); // Ou crie uma view 'offline.blade.php' simples
    });

});

// Carrega rotas de autenticação (Login, Register, etc)
require __DIR__.'/auth.php';