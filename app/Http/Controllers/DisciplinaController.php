<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disciplina;

class DisciplinaController extends Controller
{
    public function index()
    {
        // 1. Carrega as disciplinas com os relacionamentos necessários
        $disciplinas = Auth::user()->disciplinas()
            ->with(['horarios', 'frequencias'])
            ->get();
            
        // 2. Prepara as variáveis que a Dashboard exige
        $todasDisciplinas = $disciplinas;
        $disciplinasFiltradas = $disciplinas; 
        
        // 3. Cálculos básicos
        $materiasEmRisco = $disciplinas->filter(function ($d) {
            // Só conta como risco se tiver aulas registradas e frequência baixa
            return $d->frequencias->count() > 0 && $d->taxa_presenca < 75; 
        })->count();

        // Cálculo da média global
        $somaPresencas = 0;
        $totalAulas = 0;
        foreach($disciplinas as $d) {
            $totalAulas += $d->frequencias->count();
            $somaPresencas += $d->frequencias->where('presente', true)->count();
        }
        $porcentagemGlobal = $totalAulas > 0 ? round(($somaPresencas / $totalAulas) * 100) : 0; // Inicia em 0 se vazio
        
        $corGlobal = match(true) {
            $porcentagemGlobal < 75 => 'text-red-500',
            $porcentagemGlobal < 85 => 'text-yellow-500',
            default => 'text-emerald-500',
        };

        // --- CORREÇÃO DO ERRO ---
        // Definimos a variável que estava faltando na View
        $estadoVazio = $disciplinas->isEmpty() || $totalAulas === 0;

        // 4. Retorna a view com TUDO que ela precisa
        return view('dashboard', compact(
            'disciplinas', 
            'todasDisciplinas', 
            'disciplinasFiltradas', 
            'materiasEmRisco', 
            'porcentagemGlobal', 
            'corGlobal',
            'estadoVazio' // <--- Agora enviamos ela aqui!
        ));
    }

    public function criar()
    {
        return view('disciplinas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|max:7',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        // Se não veio cor do formulário, gera uma aleatória
        if (empty($dados['cor'])) {
            $dados['cor'] = $this->gerarCorAleatoria();
        }

        $inicio = $request->data_inicio ?? Auth::user()->ano_letivo_inicio;
        $fim    = $request->data_fim    ?? Auth::user()->ano_letivo_fim;

        Auth::user()->disciplinas()->create([
            'nome' => $request->nome,
            'cor' => $request->cor,
            'data_inicio' => $inicio,
            'data_fim' => $fim,
            'carga_horaria_total' => 0, 
            'porcentagem_minima' => 75,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Disciplina cadastrada com sucesso!'], 201);
        }

        return redirect()->route('dashboard')->with('toast',[
            'type' => 'success',
            'message' => 'Disciplina cadastrada com sucesso!'
        ]);
    }

    public function edit($id)
    {
        $disciplina = Disciplina::findOrFail($id);
        if ($disciplina->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado');
        }
        return view('disciplinas.edit', compact('disciplina'));
    }

    public function update(Request $request, $id)
    {
        $disciplina = Disciplina::findOrFail($id);

        if ($disciplina->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|max:7',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $disciplina->update([
            'nome' => $request->nome,
            'cor' => $request->cor,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Disciplina atualizada com sucesso!'], 200);
        }

        return redirect()->route('dashboard')->with('toast',[
            'type' => 'success',
            'message' => 'Disciplina atualizada com sucesso!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $disciplina = Disciplina::findOrFail($id);

        if ($disciplina->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Não autorizado'], 403);
            }
            abort(403);
        }

        $disciplina->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Disciplina removida com sucesso!'], 200);
        }

        return redirect()->route('dashboard')->with('toast',[
            'type' => 'success',
            'message' => 'Disciplina removida com sucesso!'
        ]);
    }
}