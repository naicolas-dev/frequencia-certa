<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Redirecionamento de segurança (Onboarding)
        if (!$user->has_seen_intro) {
            return redirect()->route('intro');
        }

        // Inicia a query carregando relacionamentos necessários
        $query = $user->disciplinas()->with(['frequencias', 'horarios']);

    $disciplinas = $query->orderBy('nome', 'asc')->get();

        // --- FILTRO 1: AULAS DE HOJE ---
        if ($request->filtro === 'hoje') {
            // 0 (Domingo) a 6 (Sábado) - padrão do Carbon
            $diaHoje = now()->dayOfWeek; 
            
            $query->whereHas('horarios', function ($q) use ($diaHoje) {
                $q->where('dia_semana', $diaHoje);
            });
        }

        // Busca os dados do banco
        $disciplinas = $query->get();

        // --- FILTRO 2: EM RISCO (Feito na coleção, pois envolve cálculo) ---
        if ($request->filtro === 'risco') {
            $disciplinas = $disciplinas->filter(function ($disciplina) {
                // Filtra matérias com menos de 75% de presença
                return $disciplina->taxa_presenca <= 75;
            });
        }

        return view('dashboard', compact('disciplinas'));
    }
}