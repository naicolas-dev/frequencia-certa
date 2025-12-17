<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disciplina;
use Illuminate\Http\JsonResponse; // Usar para tipagem do retorno

class DisciplinaApiController extends Controller
{

    /**
     * Listar todas as Disciplinas do usuário autenticado.
     */
    public function index(): JsonResponse
    {
        // Retorna apenas as disciplinas que pertencem ao usuário logado
        $disciplinas = auth()->user()->disciplinas()->get();

        return response()->json([
            'disciplinas' => $disciplinas
        ]);
    }

    /**
     * Armazenar (Criar) uma nova Disciplina.
     */
    public function store(Request $request): JsonResponse
    {
        // Validação básica (Recomendado criar Request Classes dedicadas)
        $dadosValidados = $request->validate([
            'nome' => 'required|string|max:100',
            'carga_horaria_total' => 'nullable|integer',
            'porcentagem_minima' => 'nullable|numeric|min:0|max:100',
        ]);
        
        // Define o user_id automaticamente com o ID do aluno logado
        $dadosValidados['user_id'] = auth()->id();

        $disciplina = Disciplina::create($dadosValidados);

        return response()->json([
            'mensagem' => 'Disciplina criada com sucesso!',
            'disciplina' => $disciplina
        ], 201); // 201 Created
    }

    /**
     * Mostrar os detalhes de uma Disciplina específica.
     */
    public function show(Disciplina $disciplina): JsonResponse
    {
        // *** Autorização: Checa se a disciplina pertence ao usuário atual ***
        if ($disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403); // 403 Forbidden
        }

        // Carrega os horários junto para facilitar a visualização da grade
        return response()->json([
            'disciplina' => $disciplina->load('horarios')
        ]);
    }

    /**
     * Atualizar uma Disciplina existente.
     */
    public function update(Request $request, Disciplina $disciplina): JsonResponse
    {
        // *** Autorização ***
        if ($disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        $dadosValidados = $request->validate([
            'nome' => 'required|string|max:100',
            'carga_horaria_total' => 'nullable|integer',
            'porcentagem_minima' => 'nullable|numeric|min:0|max:100',
        ]);

        $disciplina->update($dadosValidados);

        return response()->json([
            'mensagem' => 'Disciplina atualizada com sucesso!',
            'disciplina' => $disciplina
        ]);
    }

    /**
     * Remover uma Disciplina.
     */
    public function destroy(Disciplina $disciplina): JsonResponse
    {
        // *** Autorização ***
        if ($disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        $disciplina->delete();

        return response()->json([
            'mensagem' => 'Disciplina removida com sucesso.'
        ], 204); // 204 No Content
    }
}