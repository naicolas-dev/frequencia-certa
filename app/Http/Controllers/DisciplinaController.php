<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disciplina;

class DisciplinaController extends Controller
{
    public function index()
    {
        /**
         * ALTERAÇÃO IMPORTANTE:
         * Usamos o 'with' para trazer junto os Horários e Frequências.
         * Isso evita que o sistema vá no banco de dados 10 vezes se o aluno tiver 10 matérias.
         */ 
        $disciplinas = Auth::user()->disciplinas()->with(['horarios', 'frequencias'])->get();
        
        return view('dashboard', compact('disciplinas'));
    }

    // 1. Mostra a tela de cadastro
    public function create()
    {
        return view('disciplinas.create');
    }

    // 2. Recebe os dados e salva no banco
    public function store(Request $request)
    {
            // 1. Validação
        $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|max:7',
        ]);

        // 2. Salvar no Banco
        // Usamos o relacionamento para já vincular ao usuário logado
        Auth::user()->disciplinas()->create([
            'nome' => $request->nome,
            'cor' => $request->cor,
            // Valores padrão (obrigatórios no banco, mas ocultos no form por enquanto)
            'carga_horaria_total' => 0, 
            'porcentagem_minima' => 75,
        ]);

        // 3. Redirecionar
        return redirect()->route('dashboard')->with('success', 'Disciplina criada com sucesso!');
    }

    public function edit($id)
    {
        $disciplina = Disciplina::findOrFail($id);

        // Segurança: Verifica se a disciplina pertence ao usuário logado
        if ($disciplina->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado');
        }

        return view('disciplinas.edit', compact('disciplina'));
    }

    /**
     * Salva as alterações no banco (PUT)
     */
    public function update(Request $request, $id)
    {
        $disciplina = Disciplina::findOrFail($id);

        if ($disciplina->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|max:7',
        ]);

        $disciplina->update([
            'nome' => $request->nome,
            'cor' => $request->cor,
        ]);

        return redirect()->route('dashboard')->with('status', 'Matéria atualizada com sucesso!');
    }

    /**
     * Apaga a disciplina e suas faltas (DELETE)
     */
    public function destroy($id)
    {
        $disciplina = Disciplina::findOrFail($id);

        if ($disciplina->user_id !== Auth::id()) {
            abort(403);
        }

        // Ao deletar a disciplina, o Laravel já deve deletar as grades e frequências 
        // se as chaves estrangeiras estiverem configuradas com 'onDelete cascade'.
        // Caso contrário, ele deleta apenas a disciplina.
        $disciplina->delete();

        return redirect()->route('dashboard')->with('status', 'Matéria excluída.');
    }
}