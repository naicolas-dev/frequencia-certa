<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disciplina;
use App\Models\HorarioAula;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HorarioAulaController extends Controller
{
    /**
     * Listar todos os Horarios de Aula de uma Disciplina.
     * Recebe a Disciplina via Route Model Binding.
     */
    public function index(Disciplina $disciplina): JsonResponse
    {
        // 1. Autorização: Verifica se a disciplina pertence ao usuário.
        if ($disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Disciplina não encontrada ou não autorizada.'], 404);
        }

        // 2. Retorna os horários
        return response()->json([
            'horarios' => $disciplina->horarios
        ]);
    }

    /**
     * Armazenar (Criar) um novo Horario de Aula para uma Disciplina.
     */
    public function store(Request $request, Disciplina $disciplina): JsonResponse
    {
        // 1. Autorização: Verifica se a disciplina pertence ao usuário.
        if ($disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Disciplina não encontrada ou não autorizada.'], 404);
        }

        // 2. Validação
        $dadosValidados = $request->validate([
            'dia_semana' => 'required|integer|min:1|max:7', // 1=Seg a 7=Dom
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // 3. Criação
        $horario = $disciplina->horarios()->create($dadosValidados);

        return response()->json([
            'mensagem' => 'Horário de aula criado com sucesso!',
            'horario' => $horario
        ], 201);
    }

    /**
     * Mostrar os detalhes de um Horario de Aula específico.
     */
    public function show(HorarioAula $horario): JsonResponse
    {
        // 1. Autorização: Checa se o horário pertence a uma disciplina do usuário atual.
        if ($horario->disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        return response()->json([
            'horario' => $horario
        ]);
    }

    /**
     * Atualizar um Horario de Aula existente.
     */
    public function update(Request $request, HorarioAula $horario): JsonResponse
    {
        // 1. Autorização
        if ($horario->disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        // 2. Validação (permite que os campos sejam opcionais no update)
        $dadosValidados = $request->validate([
            'dia_semana' => 'nullable|integer|min:1|max:7',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i|after:hora_inicio',
        ]);

        $horario->update($dadosValidados);

        return response()->json([
            'mensagem' => 'Horário de aula atualizado com sucesso!',
            'horario' => $horario
        ]);
    }

    /**
     * Remover um Horario de Aula.
     */
    public function destroy(HorarioAula $horario): JsonResponse
    {
        // 1. Autorização
        if ($horario->disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        $horario->delete();

        return response()->json([
            'mensagem' => 'Horário de aula removido com sucesso.'
        ], 204);
    }
}