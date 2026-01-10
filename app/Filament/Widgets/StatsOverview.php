<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Frequencia;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // ---------------------------------------------------------------------
        // HELPER: Gerador de Gráficos (Don't Repeat Yourself)
        // ---------------------------------------------------------------------
        $gerarGrafico = function ($query, $colunaData = 'created_at') {
            // Busca os dados agrupados por dia (últimos 7 dias)
            $dados = $query
                ->selectRaw("DATE($colunaData) as dia, count(*) as total")
                ->where($colunaData, '>=', now()->subDays(7))
                ->groupBy('dia')
                ->pluck('total', 'dia')
                ->toArray();

            // Preenche os dias vazios com 0 para o gráfico não quebrar
            $grafico = [];
            for ($i = 6; $i >= 0; $i--) {
                $data = now()->subDays($i)->format('Y-m-d');
                $grafico[] = $dados[$data] ?? 0;
            }

            return $grafico;
        };

        // ---------------------------------------------------------------------
        // DEFINIÇÃO DOS CARDS
        // ---------------------------------------------------------------------
        return [
            
            // 1. Crescimento da Base (Verde)
            Stat::make('Total de Alunos', User::count())
                ->description('Cadastros nos últimos 7 dias')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($gerarGrafico(User::query(), 'created_at')),

            // 2. Engajamento Tecnológico (Azul)
            Stat::make('Notificações Ativas', User::has('pushSubscriptions')->count())
                ->description('Dispositivos recebendo push')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('primary')
                // Gráfico baseado na criação do usuário que tem push
                ->chart($gerarGrafico(User::has('pushSubscriptions'), 'created_at')),

            // 3. Valor do Negócio / Risco (Vermelho)
            Stat::make('Faltas Monitoradas', Frequencia::where('presente', false)->count())
                ->description('Ausências na última semana')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                // AQUI É O PULO DO GATO:
                // Usamos a coluna 'data' (dia da aula) e não 'created_at'.
                // Isso mostra a tendência real de faltas acontecendo nas aulas.
                ->chart($gerarGrafico(Frequencia::where('presente', false), 'data')),
        ];
    }
}