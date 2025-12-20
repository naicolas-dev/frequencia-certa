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

        // 1. Salva dados do onboarding
        $user->update([
            'estado' => $request->estado,
            'cidade' => $request->cidade,
            'has_seen_intro' => true,
        ]);

        // 2. Integração com API de feriados (fail-safe)
        try {
            // Invertexto usa apenas o estado
            $calendarioService->obterFeriados($request->estado);
        } catch (\Throwable $e) {
            Log::warning('Falha ao gerar calendário no onboarding', [
                'user_id' => $user->id,
                'estado'  => $request->estado,
                'erro'    => $e->getMessage(),
            ]);
        }

        return redirect()->route('dashboard');
    }
}
