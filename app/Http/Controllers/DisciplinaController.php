<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importante importar o Auth

class DisciplinaController extends Controller
{
    public function index()
    {
        // Pega as disciplinas APENAS do usuário logado
        $disciplinas = Auth::user()->disciplinas;
        
        // Retorna a view 'dashboard' enviando os dados
        return view('dashboard', compact('disciplinas'));
    }
}