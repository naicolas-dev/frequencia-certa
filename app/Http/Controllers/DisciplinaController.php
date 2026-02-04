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
        foreach ($disciplinas as $d) {
            $totalAulas += $d->frequencias->count();
            $somaPresencas += $d->frequencias->where('presente', true)->count();
        }
        $porcentagemGlobal = $totalAulas > 0 ? round(($somaPresencas / $totalAulas) * 100) : 0; // Inicia em 0 se vazio

        $corGlobal = match (true) {
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
            'estadoVazio'
        ));
    }

    /**
     * Retorna lista simples de disciplinas para a API (Selects/AI)
     */
    public function jsonList()
    {
        $disciplinas = Auth::user()->disciplinas()
            ->orderBy('nome')
            ->select('id', 'nome')
            ->get();

        return response()->json($disciplinas);
    }

    public function criar()
    {
        return view('disciplinas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'nullable|string|max:7',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        // FIX: A variável $dados não existia. Usamos uma variável local $cor.
        $cor = $request->cor;
        if (empty($cor)) {
            $cor = $this->gerarCorAleatoria();
        }

        $inicio = $request->data_inicio ?? Auth::user()->ano_letivo_inicio;
        $fim = $request->data_fim ?? Auth::user()->ano_letivo_fim;

        // 1. CAPTURAMOS A DISCIPLINA EM UMA VARIÁVEL ($disciplina)
        $disciplina = Auth::user()->disciplinas()->create([
            'nome' => $request->nome,
            'cor' => $cor, // Usa a cor tratada (que pode ser a gerada aleatoriamente)
            'data_inicio' => $inicio,
            'data_fim' => $fim,
            'carga_horaria_total' => 0,
            'porcentagem_minima' => 75,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Disciplina cadastrada com sucesso!'], 201);
        }

        // 2. ALTERADO: Redireciona para a rota de editar grade passando o ID
        return redirect()->route('grade.index', $disciplina->id)->with('toast', [
            'type' => 'success',
            'message' => 'Disciplina criada! Agora adicione os horários.'
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

        return redirect()->route('dashboard')->with('toast', [
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

        return redirect()->route('dashboard')->with('toast', [
            'type' => 'success',
            'message' => 'Disciplina removida com sucesso!'
        ]);
    }

    private function gerarCorAleatoria(): string
    {
        $cores = [
            '#EF4444', // Red
            '#F97316', // Orange
            '#F59E0B', // Amber
            '#84CC16', // Lime
            '#10B981', // Emerald
            '#06B6D4', // Cyan
            '#3B82F6', // Blue
            '#6366F1', // Indigo
            '#8B5CF6', // Violet
            '#EC4899', // Pink
        ];

        return $cores[array_rand($cores)];
    }
}