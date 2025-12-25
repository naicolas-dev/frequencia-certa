<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIntroRequest;
use App\Services\CalendarioService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class IntroController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::user()->has_seen_intro) {
            return redirect()->route('dashboard');
        }

        return view('intro.index');
    }

    public function store(
        StoreIntroRequest $request,
        CalendarioService $calendarioService
    ): RedirectResponse {
        $user = $request->user();

        // 1. Pega os dados validados (Estado + Datas)
        // Certifique-se de que o StoreIntroRequest já tem as regras das datas
        $dados = $request->validated();

        // 2. Salva TODOS os dados do onboarding de uma vez
        $user->update([
            'estado' => $dados['estado'],
            'ano_letivo_inicio' => $dados['ano_letivo_inicio'], // Novo campo
            'ano_letivo_fim' => $dados['ano_letivo_fim'],       // Novo campo
            'has_seen_intro' => true,
        ]);

        // 3. Integração com API de feriados (fail-safe)
        try {
            $calendarioService->obterFeriados($dados['estado']);
        } catch (\Throwable $e) {
            Log::warning('Falha ao gerar calendário no onboarding', [
                'user_id' => $user->id,
                'estado'  => $dados['estado'],
                'erro'    => $e->getMessage(),
            ]);
        }

        return redirect()->route('dashboard');
    }
}