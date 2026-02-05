<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    public function gerarRelatorio()
    {
        $user = Auth::user();

        // Busca todas as disciplinas com estatísticas completas
        $disciplinas = $user->disciplinas()
            ->with(['horarios', 'frequencias'])
            ->withCount([
                'frequencias as total_aulas_realizadas',
                'frequencias as total_faltas' => function ($q) {
                    $q->where('presente', false);
                }
            ])
            ->orderBy('nome', 'asc')
            ->get();

        // Enriquece com estatísticas (calcula total_aulas_previstas)
        $statsService = app(\App\Services\DisciplinaStatsService::class);
        $statsService->enrichWithStats($disciplinas, $user);

        // Gera o PDF usando uma View
        $pdf = Pdf::loadView('relatorios.baixar', compact('user', 'disciplinas'));

        // Faz o download do arquivo
        return $pdf->download('relatorio-frequencia.pdf');
    }
}