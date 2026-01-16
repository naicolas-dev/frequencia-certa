<?php

use App\Http\Controllers\AiAdvisorController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\GradeHorariaController;
use App\Http\Controllers\GradeImportController;
use App\Http\Controllers\FrequenciaController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ======================================================
// 🌍 ROTAS PÚBLICAS
// ======================================================

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(
            Url::create('/')
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(1.0)
        );

    return $sitemap->toResponse(request());
});


Route::get('/', fn () => view('welcome'));

Route::get('/offline', fn () => view('offline'));

// Social Login (Firebase)
Route::post('/auth/social/login', [SocialAuthController::class, 'login'])
    ->name('social.login');


// ======================================================
// 🔓 ROTAS AUTENTICADAS
// ======================================================

Route::middleware(['auth', 'verified'])->group(function () {

    // -------------------------
    // 🔔 PUSH NOTIFICATIONS
    // -------------------------
    Route::post('/push/subscribe', function (Request $request) {
        $request->validate([
            'endpoint'    => 'required',
            'keys.auth'   => 'required',
            'keys.p256dh' => 'required',
        ]);

        $request->user()->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );

        return response()->json(['success' => true]);
    });

    // -------------------------
    // 🏠 DASHBOARD & ONBOARDING
    // -------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/intro', [IntroController::class, 'index'])
        ->name('intro');

    Route::post('/intro', [IntroController::class, 'store'])
        ->name('intro.store');

    Route::post('/tour/finish', function (Request $request) {
        $request->user()->update(['has_completed_tour' => true]);
        return response()->json(['success' => true]);
    })->name('tour.finish');

    // -------------------------
    // 📚 DISCIPLINAS
    // -------------------------
    Route::get('/disciplinas', [DisciplinaController::class, 'index'])->name('disciplinas');
    Route::get('/disciplinas/criar', [DisciplinaController::class, 'criar'])->name('disciplinas.criar');
    Route::post('/disciplinas', [DisciplinaController::class, 'store'])->name('disciplinas.store');
    Route::get('/disciplinas/{id}/editar', [DisciplinaController::class, 'edit'])->name('disciplinas.edit');
    Route::put('/disciplinas/{id}', [DisciplinaController::class, 'update'])->name('disciplinas.update');
    Route::delete('/disciplinas/{id}', [DisciplinaController::class, 'destroy'])->name('disciplinas.destroy');

    // -------------------------
    // 🗓️ GRADE HORÁRIA (REFATORADO PARA AJAX)
    // -------------------------
    Route::get('/grade', [GradeHorariaController::class, 'geral'])->name('grade.geral');
    
    // Lista os horários de uma disciplina específica (Tela Principal com o Modal)
    Route::get('/disciplinas/{id}/horarios', [GradeHorariaController::class, 'index'])->name('grade.index');
    
    // Salvar novo horário (Agora rota genérica, pois o ID da disciplina vem no JSON)
    Route::post('/grade', [GradeHorariaController::class, 'store'])->name('grade.store');
    
    // Atualizar e Deletar (AJAX)
    Route::put('/grade/{id}', [GradeHorariaController::class, 'update'])->name('grade.update');
    Route::delete('/grade/{id}', [GradeHorariaController::class, 'destroy'])->name('grade.destroy');

    // Importação de Grade (IA)
    Route::get('/grade/importar', [GradeImportController::class, 'index'])
        ->name('grade.importar.view');

    Route::post('/api/grade/salvar-lote', [GradeImportController::class, 'salvarLote'])
        ->name('grade.salvar.lote');

    Route::middleware('throttle:5,1')
        ->post('/api/grade/importar', [GradeImportController::class, 'processar'])
        ->name('grade.importar');

    // -------------------------
    // 📊 FREQUÊNCIA
    // -------------------------
    Route::get('/api/buscar-aulas', [FrequenciaController::class, 'buscarPorData']);
    Route::post('/api/registrar-chamada', [FrequenciaController::class, 'registrarLote']);
    Route::post('/api/frequencia/{id}/falta', [FrequenciaController::class, 'registrarFalta'])
        ->name('api.frequencia.falta');

    Route::get('/historico', [FrequenciaController::class, 'historico'])
        ->name('frequencia.historico');

    // -------------------------
    // 🎉 EVENTOS
    // -------------------------
    Route::resource('eventos', EventoController::class)
        ->except(['show', 'create']);

    // -------------------------
    // 📄 RELATÓRIOS
    // -------------------------
    Route::get('/relatorio/baixar', [RelatorioController::class, 'gerarRelatorio'])
        ->name('relatorio.baixar');

    // -------------------------
    // 🤖 IA ADVISOR
    // -------------------------
    Route::middleware('throttle:5,1')
        ->get('/api/ai/analisar/{disciplina}', [AiAdvisorController::class, 'analisarRisco'])
        ->name('ai.analisar');
});


// ======================================================
// 🔒 ROTAS QUE REALMENTE EXIGEM EMAIL VERIFICADO
// ======================================================

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


require __DIR__ . '/auth.php';