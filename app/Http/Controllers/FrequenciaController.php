<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\Frequencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrequenciaController extends Controller
{
    // Método API: Adiciona uma FALTA (presente = false)
    public function registrarFalta(Request $request, $disciplinaId)
    {
        // 1. Segurança: Garante que a disciplina é do usuário logado
        $disciplina = Auth::user()->disciplinas()->findOrFail($disciplinaId);

        // 2. Cria o registro de Falta
        Frequencia::create([
            'user_id' => Auth::id(),
            'disciplina_id' => $disciplina->id,
            'data' => now(), // Data de hoje
            'presente' => false, // FALSE = FALTA
            'observacao' => 'Registro rápido pelo Dashboard'
        ]);

        // 3. Recalcula o total de faltas para devolver ao Front-end
        // Conta quantas vezes 'presente' é false para essa matéria
        $totalFaltas = $disciplina->frequencias()->where('presente', false)->count();

        // 4. Retorna JSON (O segredo da fluidez!)
        return response()->json([
            'sucesso' => true,
            'nova_qtd_faltas' => $totalFaltas,
            'mensagem' => 'Falta registrada com sucesso!'
        ]);
    }
}