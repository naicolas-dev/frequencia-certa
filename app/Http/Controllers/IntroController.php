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

        // 1. Pega os dados validados e normalizados
        $dados = $request->validated();

        // 2. Salva dados do onboarding usando o array $dados
        $user->update([
            'estado' => $dados['estado'], // Alterado de $request->estado
            'has_seen_intro' => true,
        ]);

        // 3. Integração com API de feriados (fail-safe)
        try {
            // Usa o estado validado
            $calendarioService->obterFeriados($dados['estado']);
        } catch (\Throwable $e) {
            Log::warning('Falha ao gerar calendário no onboarding', [
                'user_id' => $user->id,
                'estado'  => $dados['estado'], // Usa o dado validado no log também
                'erro'    => $e->getMessage(),
            ]);
        }

        return redirect()->route('dashboard');
    }
}