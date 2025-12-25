<?php

namespace App\Http\Controllers;

use App\Models\GradeHoraria;
use App\Models\Disciplina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeHorariaController extends Controller
{
    /**
     * Exibe a Grade Horária Geral (Semana Completa)
     * Rota: /grade
     */
    public function geral()
    {
        // Busca todos os horários do usuário logado, já com as disciplinas carregadas
        $horarios = GradeHoraria::where('user_id', Auth::id())
            ->with('disciplina') // Eager Loading para otimizar
            ->orderBy('horario_inicio')
            ->get();

        // Agrupa os horários pelo dia da semana (1=Seg, 2=Ter...) para a View
        $gradePorDia = [];
        for ($i = 1; $i <= 6; $i++) {
            $gradePorDia[$i] = $horarios->where('dia_semana', $i);
        }

        return view('grade.geral', compact('gradePorDia'));
    }

    /**
     * Mostra a tela de configuração de horários de UMA disciplina específica
     * Rota: /disciplina/{id}/grade
     */
    public function index($disciplinaId)
    {
        // Busca a disciplina e garante que pertence ao usuário
        $disciplina = Auth::user()->disciplinas()->findOrFail($disciplinaId);
        
        // Carrega os horários dessa disciplina ordenados
        // Note que usamos a relação definida no Model Disciplina
        // Se der erro, verifique se no Model Disciplina tem: public function horarios() { return $this->hasMany(GradeHoraria::class); }
        $horarios = $disciplina->horarios()
                               ->orderBy('dia_semana')
                               ->orderBy('horario_inicio')
                               ->get();

        return view('grade.index', compact('disciplina', 'horarios'));
    }

    /**
     * Salva um novo horário para uma disciplina
     * Rota: POST /disciplina/{id}/grade
     */
    public function store(Request $request, $disciplinaId)
    {
        // Garante que a disciplina é do usuário antes de adicionar
        $disciplina = Auth::user()->disciplinas()->findOrFail($disciplinaId);

        $request->validate([
            'dia_semana' => 'required|integer|between:1,7',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
        ], [
            'horario_fim.after' => 'O horário final deve ser depois do inicial.'
        ]);

        $conflito = GradeHoraria::where('user_id', Auth::id())
            ->where('dia_semana', $request->dia_semana)
            ->where(function($query) use ($request) {
                $query->where('horario_inicio', '<', $request->horario_fim)
                    ->where('horario_fim', '>', $request->horario_inicio);
            })
            ->first();

        if ($conflito) {
            $nomeDisciplina = $conflito->disciplina->nome ?? 'outra disciplina';

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Horário conflitante com o horário de ' . $nomeDisciplina
            ]);
        }

        GradeHoraria::create([
            'user_id' => Auth::id(), // Importante preencher o user_id
            'disciplina_id' => $disciplina->id,
            'dia_semana' => $request->dia_semana,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Horário cadastrado com sucesso!'
        ]);
    }

    /**
     * Exibe o formulário de edição de um horário
     * Rota: GET /grade/{id}/editar
     */
    public function edit($id)
    {
        // Busca o horário e verifica se pertence ao usuário
        $horario = GradeHoraria::where('user_id', Auth::id())
            ->with('disciplina')
            ->findOrFail($id);

        return view('grade.edit', compact('horario'));
    }

    /**
     * Atualiza um horário existente
     * Rota: PUT /grade/{id}
     */
    public function update(Request $request, $id)
    {
        $horario = GradeHoraria::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'dia_semana' => 'required|integer|between:1,7',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
        ], [
            'horario_fim.after' => 'O horário final deve ser depois do inicial.'
        ]);

        $conflito = GradeHoraria::where('user_id', Auth::id())
            ->where('dia_semana', $request->dia_semana)
            ->where('id', '!=', $id)
            ->where(function($query) use ($request) {
                $query->where('horario_inicio', '<', $request->horario_fim)
                    ->where('horario_fim', '>', $request->horario_inicio);
            })
            ->first();

        if ($conflito) {
            $nomeDisciplina = $conflito->disciplina->nome ?? 'outra disciplina';

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Horário conflitante com o horário de ' . $nomeDisciplina
            ]);
        }

        $horario->update([
            'dia_semana' => $request->dia_semana,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
        ]);

        // Redireciona de volta para a lista daquela disciplina
        return redirect()
            ->route('grade.index', $horario->disciplina_id)
            ->with('toast', [
                'type' => 'success',
                'message' => 'Horário atualizado com sucesso!'
            ]);
    }

    /**
     * Deleta um horário
     * Rota: DELETE /grade/{id}
     */
    public function destroy($id)
    {
        $horario = GradeHoraria::where('user_id', Auth::id())->findOrFail($id);
        
        $disciplinaId = $horario->disciplina_id; // Salva o ID para redirecionar
        $horario->delete();
        
        // Redireciona mantendo na página da disciplina, se possível
        return redirect()->route('grade.index', $disciplinaId)
            ->with('toast', [
                'type' => 'success',
                'message' => 'Horário removido com sucesso!'
            ]);
    }
}