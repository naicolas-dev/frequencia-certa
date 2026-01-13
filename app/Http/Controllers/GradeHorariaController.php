<?php

namespace App\Http\Controllers;

use App\Models\GradeHoraria;
use App\Models\Disciplina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class GradeHorariaController extends Controller
{
    /**
     * Exibe a Grade Horária Geral (Semana Completa)
     * Rota: /grade
     */
    public function geral()
    {
        $horarios = GradeHoraria::where('user_id', Auth::id())
            ->with('disciplina')
            ->orderBy('horario_inicio')
            ->get();

        $gradePorDia = [];
        for ($i = 1; $i <= 6; $i++) {
            $gradePorDia[$i] = $horarios->where('dia_semana', $i);
        }

        return view('grade.geral', compact('gradePorDia'));
    }

    /**
     * Mostra a tela de configuração de horários
     * Pode ser filtrado por uma disciplina ou geral
     * Rota: /disciplina/{id}/grade
     */
    public function index($disciplinaId)
    {
        $disciplina = Auth::user()->disciplinas()->findOrFail($disciplinaId);
        
        // Carrega todas as disciplinas para preencher o <select> do Modal de Edição/Criação
        $disciplinas = Auth::user()->disciplinas()->orderBy('nome')->get();

        $horarios = $disciplina->horarios()
                               ->orderBy('dia_semana')
                               ->orderBy('horario_inicio')
                               ->get();

        return view('grade.index', compact('disciplina', 'horarios', 'disciplinas'));
    }

    /**
     * Salva um novo horário
     * Rota: POST /grade (ou /disciplina/{id}/grade dependendo da sua rota, adaptei para ser flexível)
     */
    public function store(Request $request)
    {
        // Validação
        $request->validate([
            'disciplina_id'  => 'required|exists:disciplinas,id', // O ID vem do form agora
            'dia_semana'     => 'required|integer|between:0,6', // 0=Dom, 6=Sab (ajuste conforme seu padrão 1-7 ou 0-6)
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim'    => 'required|date_format:H:i|after:horario_inicio',
        ], [
            'horario_fim.after' => 'O horário final deve ser depois do inicial.'
        ]);

        // Verifica permissão da disciplina
        $disciplina = Auth::user()->disciplinas()->findOrFail($request->disciplina_id);

        // Verificação de Conflito
        $conflito = GradeHoraria::where('user_id', Auth::id())
            ->where('dia_semana', $request->dia_semana)
            ->where(function($query) use ($request) {
                $query->where('horario_inicio', '<', $request->horario_fim)
                    ->where('horario_fim', '>', $request->horario_inicio);
            })
            ->first();

        if ($conflito) {
            $msg = 'Conflito com: ' . ($conflito->disciplina->nome ?? 'Outra disciplina');

            // SE FOR AJAX: Retorna erro 422 (Unprocessable Entity)
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->withInput()->with('toast', ['type' => 'error', 'message' => $msg]);
        }

        // Criação
        GradeHoraria::create([
            'user_id'        => Auth::id(),
            'disciplina_id'  => $disciplina->id,
            'dia_semana'     => $request->dia_semana,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim'    => $request->horario_fim,
        ]);

        // SE FOR AJAX: Retorna sucesso 200
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Horário adicionado com sucesso!']);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Horário cadastrado!']);
    }

    /**
     * Atualiza um horário existente
     * Rota: PUT /grade/{id}
     */
    public function update(Request $request, $id)
    {
        $horario = GradeHoraria::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'disciplina_id'  => 'required|exists:disciplinas,id',
            'dia_semana'     => 'required|integer|between:0,6',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim'    => 'required|date_format:H:i|after:horario_inicio',
        ], [
            'horario_fim.after' => 'Horário final inválido.'
        ]);

        // Verifica se a nova disciplina pertence ao usuário
        if ($request->disciplina_id != $horario->disciplina_id) {
            Auth::user()->disciplinas()->findOrFail($request->disciplina_id);
        }

        // Verifica Conflito (Excluindo o próprio horário atual)
        $conflito = GradeHoraria::where('user_id', Auth::id())
            ->where('dia_semana', $request->dia_semana)
            ->where('id', '!=', $id)
            ->where(function($query) use ($request) {
                $query->where('horario_inicio', '<', $request->horario_fim)
                    ->where('horario_fim', '>', $request->horario_inicio);
            })
            ->first();

        if ($conflito) {
            $msg = 'Conflito com: ' . ($conflito->disciplina->nome ?? 'Outra disciplina');

            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->withInput()->with('toast', ['type' => 'error', 'message' => $msg]);
        }

        $horario->update([
            'disciplina_id'  => $request->disciplina_id,
            'dia_semana'     => $request->dia_semana,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim'    => $request->horario_fim,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Horário atualizado com sucesso!']);
        }

        return redirect()->route('grade.index', $horario->disciplina_id)
            ->with('toast', ['type' => 'success', 'message' => 'Horário atualizado!']);
    }

    /**
     * Deleta um horário
     * Rota: DELETE /grade/{id}
     */
    public function destroy(Request $request, $id)
    {
        $horario = GradeHoraria::where('user_id', Auth::id())->findOrFail($id);
        $disciplinaId = $horario->disciplina_id;
        
        $horario->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Horário removido com sucesso!']);
        }

        return redirect()->route('grade.index', $disciplinaId)
            ->with('toast', ['type' => 'success', 'message' => 'Horário removido!']);
    }
}