<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}