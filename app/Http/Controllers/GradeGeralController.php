<?php

namespace App\Http\Controllers;

use App\Models\GradeHoraria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeGeralController extends Controller
{
    public function index()
    {
        // 1. Busca todos os horários do usuário
        // 2. Traz junto os dados da Disciplina (para pegar nome e cor)
        // 3. Ordena pelo horário de início (07:00 vem antes de 08:00)
        $todosHorarios = GradeHoraria::where('user_id', Auth::id())
            ->with('disciplina')
            ->orderBy('horario_inicio')
            ->get();

        // 4. Agrupa por dia da semana (Chave 1 = Segunda, Chave 2 = Terça...)
        $gradePorDia = $todosHorarios->groupBy('dia_semana');

        return view('grade.geral', compact('gradePorDia'));
    }
}