<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disciplina;
use App\Models\Frequencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FrequenciaController extends Controller
{
    /**
     * Listar todas as Frequências (Faltas) de uma Disciplina.
     */
    public function index(Disciplina $disciplina): JsonResponse
    {
        // 1. Autorização
        if ($disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        // 2. Retorna lista ordenada pela data (mais recente primeiro)
        return response()->json([
            'frequencias' => $disciplina->frequencias()->orderBy('data_aula', 'desc')->get()
        ]);
    }

    /**
     * Registrar uma nova falta (ou presença).
     */
    public function store(Request $request, Disciplina $disciplina): JsonResponse
    {
        // 1. Autorização
        if ($disciplina->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        // 2. Validação
        $dadosValidados = $request->validate([
            'data_aula' => 'required|date',
            'faltou' => 'boolean', // Opcional, default é true (falta)
        ]);

        // 3. Verifica duplicidade: Já existe registro para esta data nesta matéria?
        $registroExistente = $disciplina->frequencias()
                                        ->where('data_aula', $dadosValidados['data_aula'])
                                        ->first();

        if ($registroExistente) {
            return response()->json([
                'mensagem' => 'Já existe um registro de frequência para esta data.',
                'frequencia' => $registroExistente
            ], 409); // 409 Conflict
        }

        // 4. Criação (user_id vem do auth, disciplina_id da rota)
        $frequencia = $disciplina->frequencias()->create([
            'user_id' => auth()->id(),
            'data_aula' => $dadosValidados['data_aula'],
            'faltou' => $dadosValidados['faltou'] ?? true,
        ]);

        return response()->json([
            'mensagem' => 'Frequência registrada com sucesso!',
            'frequencia' => $frequencia
        ], 201);
    }

    /**
     * Exibir detalhes de uma frequência específica.
     */
    public function show(Frequencia $frequencia): JsonResponse
    {
        if ($frequencia->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        return response()->json(['frequencia' => $frequencia]);
    }

    /**
     * Atualizar uma frequência (ex: mudar de falta para presença).
     */
    public function update(Request $request, Frequencia $frequencia): JsonResponse
    {
        if ($frequencia->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        $dadosValidados = $request->validate([
            'data_aula' => 'date',
            'faltou' => 'boolean',
        ]);

        $frequencia->update($dadosValidados);

        return response()->json([
            'mensagem' => 'Registro atualizado.',
            'frequencia' => $frequencia
        ]);
    }

    /**
     * Apagar um registro de frequência.
     */
    public function destroy(Frequencia $frequencia): JsonResponse
    {
        if ($frequencia->user_id !== auth()->id()) {
            return response()->json(['mensagem' => 'Não autorizado.'], 403);
        }

        $frequencia->delete();

        return response()->json(['mensagem' => 'Registro removido.'], 204);
    }
}