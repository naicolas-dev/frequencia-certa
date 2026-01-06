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

        // 1. Busca TODAS as matérias
        $todasDisciplinas = $user->disciplinas()
            ->with(['frequencias', 'horarios'])
            ->orderBy('nome', 'asc')
            ->get();

        // ---------------------------------------------------------
        // CÁLCULO DE ESTATÍSTICAS
        // ---------------------------------------------------------
        
        $todasFrequencias = $todasDisciplinas->pluck('frequencias')->collapse();
        $totalAulasGeral = $todasFrequencias->count();
        $totalFaltasGeral = $todasFrequencias->where('presente', false)->count();
        $totalPresencasGeral = $todasFrequencias->where('presente', true)->count();

        // LÓGICA DO ESTADO VAZIO (Novo) ✨
        // Verdadeiro se: não tem matérias OU tem matérias mas nunca registrou aula
        $temAlgumRegistro = $totalAulasGeral > 0;
        $estadoVazio = $todasDisciplinas->isEmpty() || !$temAlgumRegistro;

        // Porcentagem Global
        $porcentagemGlobal = 0; // Começa zerado para não bugar
        if ($totalAulasGeral > 0) {
            $porcentagemGlobal = round((($totalAulasGeral - $totalFaltasGeral) / $totalAulasGeral) * 100);
        }

        // Cor do texto Global
        $corGlobal = 'text-emerald-500'; // Padrão
        if($porcentagemGlobal < 75) {
            $corGlobal = 'text-red-500';
        } elseif($porcentagemGlobal < 85) {
            $corGlobal = 'text-yellow-500';
        }

        // Contagem de Riscos
        // FIX: Só conta como risco se a matéria tiver pelo menos 1 registro de frequência.
        // Matérias novas (0 aulas) não devem contar como "Em Risco".
        $materiasEmRisco = $todasDisciplinas->filter(function($d) {
            return $d->frequencias->count() > 0 && $d->taxa_presenca <= 75;
        })->count();

        // ---------------------------------------------------------
        // APLICAÇÃO DOS FILTROS
        // ---------------------------------------------------------
        
        $disciplinasFiltradas = $todasDisciplinas;

        // Filtro: HOJE
        if ($request->filtro === 'hoje') {
            $diaHoje = now()->dayOfWeek; 
            $disciplinasFiltradas = $todasDisciplinas->filter(function ($d) use ($diaHoje) {
                return $d->horarios->contains('dia_semana', $diaHoje);
            });
        }

        // Filtro: EM RISCO
        if ($request->filtro === 'risco') {
            $disciplinasFiltradas = $todasDisciplinas->filter(function ($d) {
                // Mesma lógica: só exibe no filtro se tiver aulas registradas E estiver mal
                return $d->frequencias->count() > 0 && $d->taxa_presenca <= 75;
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
            'estadoVazio'
        ));
    }
}