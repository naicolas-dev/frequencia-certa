<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->has_seen_intro) {
            return redirect()->route('intro');
        }

        // 1. Busca TODAS as matérias (Para estatísticas globais reais)
        // Trazemos tudo do banco de uma vez (Eager Loading)
        $todasDisciplinas = $user->disciplinas()
            ->with(['frequencias', 'horarios'])
            ->orderBy('nome', 'asc')
            ->get();

        // ---------------------------------------------------------
        // CÁLCULO DE ESTATÍSTICAS (Baseado em TUDO)
        // ---------------------------------------------------------
        
        $todasFrequencias = $todasDisciplinas->pluck('frequencias')->collapse();
        $totalAulasGeral = $todasFrequencias->count();
        $totalFaltasGeral = $todasFrequencias->where('presente', false)->count();
        $totalPresencasGeral = $todasFrequencias->where('presente', true)->count();

        // Porcentagem Global Real
        $porcentagemGlobal = 100;
        if ($totalAulasGeral > 0) {
            $porcentagemGlobal = round((($totalAulasGeral - $totalFaltasGeral) / $totalAulasGeral) * 100);
        }

        // Cor do texto Global
        $corGlobal = 'text-emerald-600 dark:text-emerald-400';
        if($porcentagemGlobal < 75) {
            $corGlobal = 'text-red-600 dark:text-red-400';
        } elseif($porcentagemGlobal < 85) {
            $corGlobal = 'text-yellow-600 dark:text-yellow-400';
        }

        // Contagem Real de Riscos (Independente do dia)
        $materiasEmRisco = $todasDisciplinas->filter(function($d) {
            return $d->taxa_presenca < 75;
        })->count();

        // ---------------------------------------------------------
        // APLICAÇÃO DOS FILTROS (Apenas para a Lista de Cards)
        // ---------------------------------------------------------
        
        // Começamos com a lista completa
        $disciplinasFiltradas = $todasDisciplinas;

        // Filtro: HOJE
        if ($request->filtro === 'hoje') {
            $diaHoje = now()->dayOfWeek; // 0 (Dom) - 6 (Sab)
            
            // Filtramos a coleção em memória (mais rápido que nova query)
            $disciplinasFiltradas = $todasDisciplinas->filter(function ($d) use ($diaHoje) {
                // Verifica se a matéria tem horário hoje
                return $d->horarios->contains('dia_semana', $diaHoje);
            });
        }

        // Filtro: EM RISCO
        if ($request->filtro === 'risco') {
            $disciplinasFiltradas = $todasDisciplinas->filter(function ($d) {
                return $d->taxa_presenca < 75;
            });
        }

        return view('dashboard', compact(
            'todasDisciplinas',
            'disciplinasFiltradas',
            'porcentagemGlobal', 
            'corGlobal', 
            'materiasEmRisco', 
            'totalPresencasGeral', 
            'totalFaltasGeral',
        ));
    }
}