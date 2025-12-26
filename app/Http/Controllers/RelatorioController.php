<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    public function gerarRelatorio()
    {
        $user = Auth::user();

        // Busca todas as disciplinas com frequências
        $disciplinas = $user->disciplinas()
                            ->with('frequencias')
                            ->orderBy('nome', 'asc')
                            ->get();

        // Gera o PDF usando uma View
        $pdf = Pdf::loadView('relatorios.baixar', compact('user', 'disciplinas'));

        // Faz o download do arquivo
        return $pdf->download('relatorio-frequencia.pdf');
    }
}