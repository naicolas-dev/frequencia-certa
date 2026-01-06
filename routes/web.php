<?php

use App\Http\Controllers\AiAdvisorController;
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

// Tela de offline
Route::get('/offline', function () {
    return view('offline');
});

// --- GRUPO PROTEGIDO (Geral do App - SEM LIMITES DE ACESSO) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD & ONBOARDING
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ... (Suas rotas de Intro e Tour mantidas aqui) ...
    Route::get('/intro', [IntroController::class, 'index'])->name('intro'); // Usei a versão do controller que parece ser a final
    Route::post('/intro', [IntroController::class, 'store'])->name('intro.store');
    
    // API Tour Finish
    Route::post('/tour/finish', function (Request $request) {
        $request->user()->update(['has_completed_tour' => true]);
        return response()->json(['success' => true]);
    })->name('tour.finish');

    // 2. DISCIPLINAS
    Route::get('/disciplinas', [DisciplinaController::class, 'index'])->name('disciplinas');
    Route::get('/disciplinas/criar', [DisciplinaController::class, 'criar'])->name('disciplinas.criar');
    Route::post('/disciplinas', [DisciplinaController::class, 'store'])->name('disciplinas.store');
    Route::get('/disciplinas/{id}/editar', [DisciplinaController::class, 'edit'])->name('disciplinas.edit');
    Route::put('/disciplinas/{id}', [DisciplinaController::class, 'update'])->name('disciplinas.update');
    Route::delete('/disciplinas/{id}', [DisciplinaController::class, 'destroy'])->name('disciplinas.destroy');

    // 3. GRADE HORÁRIA
    Route::get('/grade', [GradeHorariaController::class, 'geral'])->name('grade.geral');
    Route::get('/disciplinas/{id}/horarios', [GradeHorariaController::class, 'index'])->name('grade.index');
    Route::post('/disciplinas/{id}/horarios', [GradeHorariaController::class, 'store'])->name('grade.store');
    Route::get('/grade/{id}/editar', [GradeHorariaController::class, 'edit'])->name('grade.edit');
    Route::put('/grade/{id}', [GradeHorariaController::class, 'update'])->name('grade.update');
    Route::delete('/grade/{id}', [GradeHorariaController::class, 'destroy'])->name('grade.destroy');

    // 4. FREQUÊNCIA
    Route::get('/api/buscar-aulas', [FrequenciaController::class, 'buscarPorData']);
    Route::post('/api/registrar-chamada', [FrequenciaController::class, 'registrarLote']);
    Route::post('/api/frequencia/{id}/falta', [FrequenciaController::class, 'registrarFalta'])->name('api.frequencia.falta');
    Route::get('/historico', [FrequenciaController::class, 'historico'])->name('frequencia.historico');

    // 5. PERFIL & EVENTOS
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('eventos', EventoController::class)->except(['show', 'create']); // Use resource pra simplificar se quiser, ou mantenha suas rotas manuais

    // 6. RELATÓRIOS
    Route::get('/relatorio/baixar', [RelatorioController::class, 'gerarRelatorio'])->name('relatorio.baixar');

    // =========================================================================
    // 🤖 IA ADVISOR (COM THROTTLE / LIMITE)
    // =========================================================================
    // Só esta rota precisa de proteção contra abuso (5 requests/minuto)
    Route::middleware('throttle:5,1')->get('/api/ai/analisar/{disciplina}', [AiAdvisorController::class, 'analisarRisco'])
        ->name('ai.analisar');

});

require __DIR__.'/auth.php';